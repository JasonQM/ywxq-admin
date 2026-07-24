<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use App\Services\GameStatisticsSyncService;
use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Throwable;

class TodayDailyStatisticWidget extends Widget
{
    protected static ?int $sort = 10;

    protected string $view = 'filament.widgets.today-daily-statistic';

    protected int|string|array $columnSpan = 'full';

    public function syncToday(GameStatisticsSyncService $service): void
    {
        $today = CarbonImmutable::today(config('app.timezone'))->format('Ymd');

        try {
            $result = $service->syncAll($today, $today);

            Notification::make()
                ->title('今日数据已刷新')
                ->body("统计更新 {$result['statsUpdated']} 条，实名更新 {$result['realNameUpdated']} 条。")
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('刷新失败，请稍后重试')
                ->danger()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        $today = CarbonImmutable::today(config('app.timezone'));

        return [
            'today' => $today,
            'record' => DailyStatistic::query()
                ->whereDate('day', $today->toDateString())
                ->first(),
        ];
    }
}
