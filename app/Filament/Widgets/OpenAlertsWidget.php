<?php

namespace App\Filament\Widgets;

use App\Models\DataAlert;
use Filament\Widgets\Widget;

class OpenAlertsWidget extends Widget
{
    protected static ?int $sort = 60;

    protected string $view = 'filament.widgets.open-alerts';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected function getViewData(): array
    {
        return [
            'alerts' => DataAlert::query()
                ->where('status', DataAlert::STATUS_OPEN)
                ->orderByDesc('day')
                ->limit(8)
                ->get(),
        ];
    }
}
