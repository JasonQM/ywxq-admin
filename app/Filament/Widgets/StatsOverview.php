<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use App\Models\DataAlert;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $latest = DailyStatistic::query()->latest('day')->first();
        $sevenDays = DailyStatistic::query()->latest('day')->limit(7)->get();

        return [
            Stat::make('活跃DAU', number_format((int) ($latest?->active_dau ?? 0)))
                ->description(($latest?->day?->format('Y/m/d') ?? '暂无数据').' 最新日')
                ->icon(Heroicon::UserGroup)
                ->color('primary'),
            Stat::make('新增注册', number_format((int) ($latest?->register_num ?? 0)))
                ->description('最新日注册人数')
                ->icon(Heroicon::UserPlus)
                ->color('info'),
            Stat::make('总付费金额', '¥'.number_format((float) ($sevenDays->sum('recharge_amount')), 2))
                ->description('最近7天')
                ->icon(Heroicon::Banknotes)
                ->color('success'),
            Stat::make('消耗', '¥'.number_format((float) ($sevenDays->sum('consume_amount')), 2))
                ->description('最近7天人工填写')
                ->icon(Heroicon::Calculator)
                ->color('warning'),
            Stat::make('ROI', $this->formatPercent($this->sevenDayRoi($sevenDays)))
                ->description('最近7天：充值减兑换后除以消耗')
                ->icon(Heroicon::ArrowTrendingUp)
                ->color($this->sevenDayRoi($sevenDays) >= 0 ? 'success' : 'danger'),
            Stat::make('待处理预警', number_format(DataAlert::query()->where('status', DataAlert::STATUS_OPEN)->count()))
                ->description('数据异常')
                ->icon(Heroicon::BellAlert)
                ->color('danger'),
        ];
    }

    private function sevenDayRoi($records): float
    {
        $consume = (float) $records->sum('consume_amount');
        if ($consume <= 0) {
            return 0.0;
        }

        return (((float) $records->sum('recharge_amount')) - ((float) $records->sum('withdraw_amount'))) / $consume;
    }

    private function formatPercent(float $value): string
    {
        return number_format($value * 100, 2).'%';
    }
}
