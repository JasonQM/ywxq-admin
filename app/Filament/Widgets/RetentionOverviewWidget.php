<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;

class RetentionOverviewWidget extends Widget
{
    protected static ?int $sort = 50;

    protected string $view = 'filament.widgets.retention-overview';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected function getViewData(): array
    {
        return [
            'rows' => [
                $this->row('次留', 1, 'd1_login_num', 'register_num'),
                $this->row('3留', 3, 'd3_login_num', 'register_num'),
                $this->row('7留', 7, 'd7_login_num', 'register_num'),
                $this->row('付费次留', 1, 'rd1_login_num', 'new_recharge_user_count'),
                $this->row('付费3留', 3, 'rd3_login_num', 'new_recharge_user_count'),
                $this->row('付费7留', 7, 'rd7_login_num', 'new_recharge_user_count'),
            ],
        ];
    }

    private function row(string $label, int $days, string $numeratorColumn, string $denominatorColumn): array
    {
        $cutoff = CarbonImmutable::today(config('app.timezone'))->subDays($days + 1);
        $summary = DailyStatistic::query()
            ->whereDate('day', '<=', $cutoff->toDateString())
            ->selectRaw("sum({$numeratorColumn}) as numerator, sum({$denominatorColumn}) as denominator")
            ->first();

        $numerator = (int) ($summary?->numerator ?? 0);
        $denominator = (int) ($summary?->denominator ?? 0);

        return [
            'label' => $label,
            'cutoff' => $cutoff->format('Y/m/d'),
            'count' => $numerator,
            'rate' => $denominator > 0 ? $numerator / $denominator : 0,
        ];
    }
}
