<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use Filament\Widgets\ChartWidget;

class RoiPayTrendChart extends ChartWidget
{
    protected static ?int $sort = 40;

    protected ?string $heading = 'ROI 与付费趋势';

    protected ?string $description = '最近14天：ROI、付费率、ARPU、ARPPU';

    protected ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = 'full';

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
                    'label' => 'ROI(%)',
                    'data' => $records->map(fn (DailyStatistic $record): float => round(((float) $record->roi) * 100, 2))->all(),
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.10)',
                    'tension' => 0.35,
                ],
                [
                    'label' => '付费率(%)',
                    'data' => $records->map(fn (DailyStatistic $record): float => round(((float) $record->pay_rate) * 100, 2))->all(),
                    'borderColor' => '#7c3aed',
                    'backgroundColor' => 'rgba(124, 58, 237, 0.10)',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'ARPU',
                    'data' => $records->map(fn (DailyStatistic $record): float => (float) $record->arpu)->all(),
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.10)',
                    'tension' => 0.35,
                ],
                [
                    'label' => 'ARPPU',
                    'data' => $records->map(fn (DailyStatistic $record): float => (float) $record->arppu)->all(),
                    'borderColor' => '#db2777',
                    'backgroundColor' => 'rgba(219, 39, 119, 0.10)',
                    'tension' => 0.35,
                ],
            ],
        ];
    }
}
