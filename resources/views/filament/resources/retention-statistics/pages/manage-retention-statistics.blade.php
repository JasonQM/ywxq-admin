@php
    $number = fn (float|int $value): string => number_format((float) $value, 0);
    $percent = fn (float|int $value): string => number_format(((float) $value) * 100, 2) . '%';

    $summary = $this->getSummary();
    $records = $this->getRecords();

    $columns = [
        ['key' => 'day', 'label' => '日期', 'width' => 92],
        ['key' => 'register_num', 'label' => '注册', 'width' => 70],
        ['key' => 'new_recharge_user_count', 'label' => '新付数', 'width' => 78],
        ['key' => 'd1_login_num', 'label' => '次留', 'width' => 68],
        ['key' => 'd1_rate', 'label' => '次留率', 'width' => 72],
        ['key' => 'd3_login_num', 'label' => '三留', 'width' => 68],
        ['key' => 'd3_rate', 'label' => '三留率', 'width' => 72],
        ['key' => 'd7_login_num', 'label' => '7留', 'width' => 68],
        ['key' => 'd7_rate', 'label' => '7留率', 'width' => 72],
        ['key' => 'rd1_login_num', 'label' => '付费次留', 'width' => 88],
        ['key' => 'rd1_rate', 'label' => '付费次留率', 'width' => 96],
        ['key' => 'rd3_login_num', 'label' => '付费三留', 'width' => 88],
        ['key' => 'rd3_rate', 'label' => '付费三留率', 'width' => 96],
        ['key' => 'rd7_login_num', 'label' => '付费7留', 'width' => 88],
        ['key' => 'rd7_rate', 'label' => '付费7留率', 'width' => 96],
    ];

    $summaryCells = [
        'day' => '汇总',
        'register_num' => $number($summary['register_num'] ?? 0),
        'new_recharge_user_count' => $number($summary['new_recharge_user_count'] ?? 0),
        'd1_login_num' => $number($summary['d1_login_num'] ?? 0),
        'd1_rate' => $percent($summary['d1_rate'] ?? 0),
        'd3_login_num' => $number($summary['d3_login_num'] ?? 0),
        'd3_rate' => $percent($summary['d3_rate'] ?? 0),
        'd7_login_num' => $number($summary['d7_login_num'] ?? 0),
        'd7_rate' => $percent($summary['d7_rate'] ?? 0),
        'rd1_login_num' => $number($summary['rd1_login_num'] ?? 0),
        'rd1_rate' => $percent($summary['rd1_rate'] ?? 0),
        'rd3_login_num' => $number($summary['rd3_login_num'] ?? 0),
        'rd3_rate' => $percent($summary['rd3_rate'] ?? 0),
        'rd7_login_num' => $number($summary['rd7_login_num'] ?? 0),
        'rd7_rate' => $percent($summary['rd7_rate'] ?? 0),
    ];

    $tableWidth = array_sum(array_column($columns, 'width'));
    $cell = 'padding: 6px 6px; white-space: nowrap; text-align: center;';
    $retentionDayMap = [
        'd1_login_num' => 1,
        'd1_rate' => 1,
        'd3_login_num' => 3,
        'd3_rate' => 3,
        'd7_login_num' => 7,
        'd7_rate' => 7,
        'rd1_login_num' => 1,
        'rd1_rate' => 1,
        'rd3_login_num' => 3,
        'rd3_rate' => 3,
        'rd7_login_num' => 7,
        'rd7_rate' => 7,
    ];
@endphp

<x-filament-panels::page>
    <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; background: #fff;">
        <div style="display: flex; align-items: end; justify-content: space-between; gap: 12px; padding: 12px 14px; border-bottom: 1px solid #eef2f7; flex-wrap: wrap;">
            <div style="display: flex; align-items: end; gap: 12px; flex-wrap: wrap;">
                <div style="min-width: 160px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 12px; font-weight: 600; color: #111827;">开始日期</label>
                    <input type="date" wire:model.live="from" style="width: 160px; max-width: 100%; border: 1px solid #d1d5db; border-radius: 7px; padding: 7px 9px; font-size: 13px;">
                </div>
                <div style="min-width: 160px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 12px; font-weight: 600; color: #111827;">结束日期</label>
                    <input type="date" wire:model.live="until" style="width: 160px; max-width: 100%; border: 1px solid #d1d5db; border-radius: 7px; padding: 7px 9px; font-size: 13px;">
                </div>
            </div>

            <button type="button" wire:click="resetFilters" style="padding: 8px 4px; color: #dc2626; font-size: 14px; font-weight: 600;">重置</button>
        </div>

        <div style="width: 100%; overflow-x: auto;">
            <table style="min-width: {{ $tableWidth }}px; width: {{ $tableWidth }}px; border-collapse: separate; border-spacing: 0; table-layout: fixed; font-size: 12px; color: #111827;">
                <thead>
                    <tr style="background: #f8fafc; box-shadow: inset 0 -1px 0 #eef2f7;">
                        @foreach ($columns as $column)
                            <th style="{{ $cell }} width: {{ $column['width'] }}px; font-weight: 700;">
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                    <tr style="background: #f1f5f9; box-shadow: inset 0 -1px 0 #e2e8f0;">
                        @foreach ($columns as $column)
                            <td style="{{ $cell }} width: {{ $column['width'] }}px; font-weight: 700;">
                                {{ $summaryCells[$column['key']] }}
                            </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr style="background: {{ $loop->even ? '#fbfdff' : '#fff' }};">
                            @foreach ($columns as $column)
                                @php
                                    $key = $column['key'];
                                    $requiredDays = $retentionDayMap[$key] ?? null;
                                    $available = $requiredDays === null || $this->isRetentionAvailable($record, $requiredDays);
                                @endphp
                                <td style="{{ $cell }} color: {{ $available ? '#111827' : '#9ca3af' }};">
                                    @if (! $available)
                                        -
                                    @else
                                        @switch($key)
                                            @case('day') {{ $record->day->format('Y/m/d') }} @break
                                            @case('register_num') {{ $number($record->register_num) }} @break
                                            @case('new_recharge_user_count') {{ $number($record->new_recharge_user_count) }} @break
                                            @case('d1_login_num') {{ $number($record->d1_login_num) }} @break
                                            @case('d1_rate') {{ $percent($record->d1_rate) }} @break
                                            @case('d3_login_num') {{ $number($record->d3_login_num) }} @break
                                            @case('d3_rate') {{ $percent($record->d3_rate) }} @break
                                            @case('d7_login_num') {{ $number($record->d7_login_num) }} @break
                                            @case('d7_rate') {{ $percent($record->d7_rate) }} @break
                                            @case('rd1_login_num') {{ $number($record->rd1_login_num) }} @break
                                            @case('rd1_rate') {{ $percent($record->rd1_rate) }} @break
                                            @case('rd3_login_num') {{ $number($record->rd3_login_num) }} @break
                                            @case('rd3_rate') {{ $percent($record->rd3_rate) }} @break
                                            @case('rd7_login_num') {{ $number($record->rd7_login_num) }} @break
                                            @case('rd7_rate') {{ $percent($record->rd7_rate) }} @break
                                        @endswitch
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" style="padding: 24px; text-align: center; color: #6b7280;">当前时间范围没有数据</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
