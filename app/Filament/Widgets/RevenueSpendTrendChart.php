<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use Filament\Widgets\ChartWidget;

class RevenueSpendTrendChart extends ChartWidget
{
    protected static ?int $sort = 30;

    protected ?string $heading = '收入与消耗趋势';

    protected ?string $description = '最近14天：消耗、充值、兑换';

    protected ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $records = DailyStatistic::query()
            ->orderByDesc('day')
            ->limit(14)
            ->get()
            ->sortBy('day')
            ->values();

        return [
            'labels' => $records->map(fn (DailyStatistic $record): string => $record->day->format('m/d'))->all(),
            'datasets' => [
                [
                    'label' => '消耗',
                    'data' => $records->map(fn (DailyStatistic $record): float => (float) $record->consume_amount)->all(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.12)',
                    'tension' => 0.35,
                ],
                [
                    'label' => '总充值',
                    'data' => $records->map(fn (DailyStatistic $record): float => (float) $record->recharge_amount)->all(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'tension' => 0.35,
                ],
                [
                    'label' => '兑换金额',
                    'data' => $records->map(fn (DailyStatistic $record): float => (float) $record->withdraw_amount)->all(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.10)',
                    'tension' => 0.35,
                ],
            ],
        ];
    }
}
