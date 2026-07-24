<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;

class TodayDailyStatisticWidget extends Widget
{
    protected static ?int $sort = 10;

    protected string $view = 'filament.widgets.today-daily-statistic';

    protected int|string|array $columnSpan = 'full';

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
