<?php

namespace App\Services;

use App\Models\DailyStatistic;
use App\Models\DataAlert;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GameStatisticsSyncService
{
    public function syncAll(?string $startDay = null, ?string $endDay = null): array
    {
        $start = CarbonImmutable::createFromFormat('Ymd', $startDay ?: config('game.stats_start_day'))->startOfDay();
        $end = CarbonImmutable::createFromFormat('Ymd', $endDay ?: now()->format('Ymd'))->startOfDay();
        $chunkDays = max(1, (int) config('game.chunk_days', 7));
        $statsUpdated = 0;
        $realNameUpdated = 0;

        for ($cursor = $start; $cursor->lte($end); $cursor = $cursor->addDays($chunkDays)) {
            $chunkEnd = $cursor->addDays($chunkDays - 1)->min($end);
            $statsUpdated += $this->syncStatisticsChunk($cursor, $chunkEnd);
            $realNameUpdated += $this->syncRealNameChunk($cursor, $chunkEnd);
        }

        $alerts = $this->checkAlerts($start, $end);

        return compact('statsUpdated', 'realNameUpdated', 'alerts');
    }

    public function syncStatisticsChunk(CarbonImmutable $start, CarbonImmutable $end): int
    {
        $response = Http::timeout((int) config('game.request_timeout', 15))->get(config('game.stats_base_url'), [
            'method' => 'statistics',
            'startDay' => $start->format('Ymd'),
            'endDay' => $end->format('Ymd'),
        ]);

        $response->throw();

        $rows = $this->extractRows($response->json() ?? []);
        $updated = 0;

        foreach ($rows as $row) {
            $day = $this->normalizeDay((string) Arr::get($row, 'day'));
            if ($day === null) {
                continue;
            }

            $stat = DailyStatistic::firstOrNew(['day' => $day->toDateString()]);
            $consume = $stat->exists ? $stat->consume_amount : 0;

            $stat->fill([
                'login_num' => (int) Arr::get($row, 'loginNum', 0),
                'register_num' => (int) Arr::get($row, 'register', 0),
                'consume_amount' => $consume,
                'recharge_amount' => (float) Arr::get($row, 'rechargeAll', 0),
                'recharge_user_count' => (int) Arr::get($row, 'rechargeUserAll', 0),
                'withdraw_amount' => (float) Arr::get($row, 'zhuanZhangAll', 0),
                'withdraw_user_count' => (int) Arr::get($row, 'zhuanZhangUserAll', 0),
                'new_recharge_amount' => (float) Arr::get($row, 'registerRecharge0', 0),
                'new_recharge_user_count' => (int) Arr::get($row, 'registerRechargeUser0', 0),
                'd1_login_num' => (int) Arr::get($row, 'd1', 0),
                'd3_login_num' => (int) Arr::get($row, 'd3', 0),
                'd7_login_num' => (int) Arr::get($row, 'd7', 0),
                'rd1_login_num' => (int) Arr::get($row, 'rd1', 0),
                'rd3_login_num' => (int) Arr::get($row, 'rd3', 0),
                'rd7_login_num' => (int) Arr::get($row, 'rd7', 0),
            ]);
            $stat->save();
            $updated++;
        }

        return $updated;
    }

    public function syncRealNameChunk(CarbonImmutable $start, CarbonImmutable $end): int
    {
        $updated = 0;

        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            $response = Http::timeout((int) config('game.request_timeout', 15))->get(config('game.stats_base_url'), [
                'method' => 'daySMCount',
                'day' => $day->format('Ymd'),
            ]);

            $response->throw();

            $stat = DailyStatistic::firstOrNew(['day' => $day->toDateString()]);
            $stat->real_name_num = $this->extractCount($response->json() ?? []);
            $stat->save();
            $updated++;
        }

        return $updated;
    }

    public function checkAlerts(CarbonImmutable $start, CarbonImmutable $end): int
    {
        $count = 0;

        DailyStatistic::query()
            ->whereBetween('day', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('day')
            ->each(function (DailyStatistic $stat) use (&$count): void {
                foreach ($this->rules($stat) as $alert) {
                    DataAlert::updateOrCreate(
                        ['day' => $stat->day->toDateString(), 'type' => $alert['type']],
                        [
                            'level' => $alert['level'],
                            'title' => $alert['title'],
                            'message' => $alert['message'],
                            'status' => DataAlert::STATUS_OPEN,
                        ],
                    );
                    $count++;
                }
            });

        return $count;
    }

    private function rules(DailyStatistic $stat): array
    {
        $alerts = [];

        if ($stat->active_dau < 0) {
            $alerts[] = $this->alert('active_dau_negative', 'high', '活跃DAU为负', '登录数小于注册数，请检查接口数据。');
        }
        if ($stat->consume_amount > 0 && $stat->recharge_amount <= 0) {
            $alerts[] = $this->alert('consume_without_recharge', 'high', '有消耗但无充值', '当天已填写消耗，但总付费金额为 0。');
        }
        if ($stat->consume_amount <= 0 && $stat->recharge_amount > 0) {
            $alerts[] = $this->alert('missing_consume', 'medium', '缺少消耗数据', '当天有充值但消耗为 0，ROI 会失真。');
        }
        if ($stat->consume_amount > 0 && $stat->roi < 0) {
            $alerts[] = $this->alert('negative_roi', 'high', 'ROI 为负', '充值金额小于兑换金额。');
        }
        if ($stat->consume_amount > 0 && $stat->roi < 0.1) {
            $alerts[] = $this->alert('low_roi', 'medium', 'ROI 偏低', 'ROI 低于 10%。');
        }
        if ($stat->recharge_amount > 0 && $stat->withdraw_amount > $stat->recharge_amount) {
            $alerts[] = $this->alert('withdraw_gt_recharge', 'medium', '兑换金额超过充值', '兑换金额高于总付费金额。');
        }
        if ($stat->register_num > 0 && $stat->pay_rate > 0.5) {
            $alerts[] = $this->alert('high_pay_rate', 'low', '付费率偏高', '付费率超过 50%，建议确认数据口径。');
        }

        return $alerts;
    }

    private function alert(string $type, string $level, string $title, string $message): array
    {
        return compact('type', 'level', 'title', 'message');
    }

    private function extractRows(array $data): array
    {
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }
        if (isset($data['list']) && is_array($data['list'])) {
            return $data['list'];
        }

        return array_is_list($data) ? $data : [];
    }

    private function extractCount(array $data): int
    {
        foreach (['data', 'count', 'num', 'total', 'value'] as $key) {
            if (isset($data[$key]) && is_numeric($data[$key])) {
                return (int) $data[$key];
            }
        }
        if (isset($data['data']) && is_array($data['data'])) {
            return $this->extractCount($data['data']);
        }
        if (array_is_list($data) && isset($data[0]) && is_numeric($data[0])) {
            return (int) $data[0];
        }

        return 0;
    }

    private function normalizeDay(string $value): ?CarbonImmutable
    {
        $value = trim($value);

        foreach (['Ymd', 'Y/m/d', 'Y/n/j', 'Y-m-d', 'Y-n-j'] as $format) {
            $date = CarbonImmutable::createFromFormat($format, $value);

            if ($date !== false) {
                return $date->startOfDay();
            }
        }

        $digits = preg_replace('/\D/', '', $value);
        if (strlen($digits) === 8) {
            return CarbonImmutable::createFromFormat('Ymd', $digits)->startOfDay();
        }

        return null;
    }
}
