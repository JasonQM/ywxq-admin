<?php

namespace App\Filament\Widgets;

use App\Models\DailyStatistic;
use Filament\Widgets\ChartWidget;

class ActivityTrendChart extends ChartWidget
{
    protected static ?int $sort = 20;

    protected ?string $heading = '活跃与新增趋势';

    protected ?string $description = '最近14天：活跃DAU、登录数、注册人数';

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
                    'label' => '活跃DAU',
                    'data' => $records->pluck('active_dau')->all(),
                    'borderColor' => '#0284c7',
                    'backgroundColor' => 'rgba(2, 132, 199, 0.12)',
                    'tension' => 0.35,
                ],
                [
                    'label' => '登录数',
                    'data' => $records->pluck('login_num')->all(),
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.10)',
                    'tension' => 0.35,
                ],
                [
                    'label' => '注册人数',
                    'data' => $records->pluck('register_num')->all(),
                    'borderColor' => '#ea580c',
                    'backgroundColor' => 'rgba(234, 88, 12, 0.10)',
                    'tension' => 0.35,
                ],
            ],
        ];
    }
}
