@php
    $money = fn (float|int $value): string => number_format((float) $value, 2);
    $number = fn (float|int $value): string => number_format((float) $value, 0);
    $percent = fn (float|int $value): string => number_format(((float) $value) * 100, 2) . '%';

    $summary = $this->getSummary();
    $records = $this->getRecords();

    $columns = [
        ['key' => 'day', 'label' => '日期', 'width' => 92],
        ['key' => 'consume_amount', 'label' => '消耗', 'width' => 78],
        ['key' => 'login_num', 'label' => '登录', 'width' => 70],
        ['key' => 'register_num', 'label' => '注册', 'width' => 70],
        ['key' => 'register_cost', 'label' => '注成本', 'width' => 78],
        ['key' => 'active_dau', 'label' => 'DAU', 'width' => 70],
        ['key' => 'register_rate', 'label' => '注率', 'width' => 70],
        ['key' => 'real_name_num', 'label' => '实名', 'width' => 70],
        ['key' => 'real_name_rate', 'label' => '实名率', 'width' => 76],
        ['key' => 'new_recharge_user_count', 'label' => '新付数', 'width' => 78],
        ['key' => 'pay_cost', 'label' => '付成本', 'width' => 78],
        ['key' => 'recharge_user_count', 'label' => '总付数', 'width' => 78],
        ['key' => 'pay_rate', 'label' => '付率', 'width' => 68],
        ['key' => 'new_recharge_amount', 'label' => '新付额', 'width' => 86],
        ['key' => 'recharge_amount', 'label' => '总付额', 'width' => 86],
        ['key' => 'withdraw_user_count', 'label' => '兑人', 'width' => 70],
        ['key' => 'withdraw_amount', 'label' => '兑额', 'width' => 78],
        ['key' => 'arpu', 'label' => 'ARPU', 'width' => 68],
        ['key' => 'arppu', 'label' => 'ARPPU', 'width' => 76],
        ['key' => 'new_customer_roa', 'label' => '新ROA', 'width' => 78],
        ['key' => 'roi', 'label' => 'ROI', 'width' => 64],
    ];
    $columns = array_values(array_filter($columns, fn (array $column): bool => in_array($column['key'], $this->visibleColumns, true)));
    $tableWidth = array_sum(array_column($columns, 'width'));

    $summaryCells = [
        'day' => '汇总',
        'consume_amount' => $money($summary['consume_amount'] ?? 0),
        'login_num' => $number($summary['login_num'] ?? 0),
        'active_dau' => $number($summary['active_dau'] ?? 0),
        'register_num' => $number($summary['register_num'] ?? 0),
        'register_rate' => $percent($summary['register_rate'] ?? 0),
        'real_name_num' => $number($summary['real_name_num'] ?? 0),
        'real_name_rate' => $percent($summary['real_name_rate'] ?? 0),
        'new_recharge_user_count' => $number($summary['new_recharge_user_count'] ?? 0),
        'recharge_user_count' => $number($summary['recharge_user_count'] ?? 0),
        'pay_rate' => $percent($summary['pay_rate'] ?? 0),
        'register_cost' => $money($summary['register_cost'] ?? 0),
        'pay_cost' => $money($summary['pay_cost'] ?? 0),
        'new_recharge_amount' => $money($summary['new_recharge_amount'] ?? 0),
        'recharge_amount' => $money($summary['recharge_amount'] ?? 0),
        'withdraw_user_count' => $number($summary['withdraw_user_count'] ?? 0),
        'withdraw_amount' => $money($summary['withdraw_amount'] ?? 0),
        'arpu' => $money($summary['arpu'] ?? 0),
        'arppu' => $money($summary['arppu'] ?? 0),
        'new_customer_roa' => $percent($summary['new_customer_roa'] ?? 0),
        'roi' => $percent($summary['roi'] ?? 0),
    ];

    $cell = 'padding: 6px 6px; white-space: nowrap; text-align: center;';
    $columnGroups = [
        '基础' => [
            'day' => '日期',
            'consume_amount' => '消耗',
            'login_num' => '登录数',
            'register_num' => '注册人数',
            'register_cost' => '注册成本',
            'active_dau' => '活跃DAU',
        ],
        '转化' => [
            'register_rate' => '注册率',
            'real_name_num' => '实名人数',
            'real_name_rate' => '实名认证率',
        ],
        '付费' => [
            'new_recharge_user_count' => '新付数',
            'pay_cost' => '付费成本',
            'recharge_user_count' => '总付数',
            'pay_rate' => '付费率',
            'new_recharge_amount' => '新客付费金额',
            'recharge_amount' => '总付费金额',
        ],
        '回收与效率' => [
            'withdraw_user_count' => '兑换人数',
            'withdraw_amount' => '兑换金额',
            'arpu' => 'ARPU',
            'arppu' => 'ARPPU',
            'new_customer_roa' => '新客ROA',
            'roi' => 'ROI',
        ],
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

            <div style="display: flex; align-items: center; gap: 12px; position: relative;">
                <button type="button" wire:click="resetFilters" style="padding: 8px 4px; color: #dc2626; font-size: 14px; font-weight: 600;">重置</button>

                <details style="position: relative;">
                    <summary
                        title="字段显示"
                        style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; list-style: none; color: #374151; background: #fff;"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 5h18"></path>
                            <path d="M6 12h12"></path>
                            <path d="M10 19h4"></path>
                        </svg>
                    </summary>
                    <div style="position: absolute; right: 0; top: 40px; z-index: 20; width: min(320px, 86vw); max-height: 360px; overflow: auto; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14); padding: 10px;">
                        <div style="margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #111827;">字段显示</div>
                        <div style="display: grid; gap: 10px;">
                            @foreach ($columnGroups as $group => $options)
                                <div>
                                    <div style="margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #6b7280;">{{ $group }}</div>
                                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 7px 10px;">
                                        @foreach ($options as $key => $label)
                                            <label style="display: inline-flex; align-items: center; gap: 6px; min-width: 0; font-size: 12px; color: #374151;">
                                                <input type="checkbox" value="{{ $key }}" wire:model.live="visibleColumns" @disabled($key === 'day')>
                                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>
            </div>
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
                                <td style="{{ $cell }}">
                                    @switch($column['key'])
                                        @case('day')
                                            {{ $record->day->format('Y/m/d') }}
                                            @break
                                        @case('consume_amount')
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                wire:model.defer="consumeAmounts.{{ $record->id }}"
                                                wire:change="saveConsume({{ $record->id }})"
                                                style="width: 64px; border: 1px solid #d1d5db; border-radius: 5px; padding: 3px 5px; text-align: center; font-size: 12px;"
                                            >
                                            @break
                                        @case('login_num') {{ $number($record->login_num) }} @break
                                        @case('active_dau') {{ $number($record->active_dau) }} @break
                                        @case('register_num') {{ $number($record->register_num) }} @break
                                        @case('register_rate') {{ $percent($record->register_rate) }} @break
                                        @case('real_name_num') {{ $number($record->real_name_num) }} @break
                                        @case('real_name_rate') {{ $percent($record->real_name_rate) }} @break
                                        @case('new_recharge_user_count') {{ $number($record->new_recharge_user_count) }} @break
                                        @case('recharge_user_count') {{ $number($record->recharge_user_count) }} @break
                                        @case('pay_rate') {{ $percent($record->pay_rate) }} @break
                                        @case('register_cost') {{ $money($record->register_cost) }} @break
                                        @case('pay_cost') {{ $money($record->pay_cost) }} @break
                                        @case('new_recharge_amount') {{ $money($record->new_recharge_amount) }} @break
                                        @case('recharge_amount') {{ $money($record->recharge_amount) }} @break
                                        @case('withdraw_user_count') {{ $number($record->withdraw_user_count) }} @break
                                        @case('withdraw_amount') {{ $money($record->withdraw_amount) }} @break
                                        @case('arpu') {{ $money($record->arpu) }} @break
                                        @case('arppu') {{ $money($record->arppu) }} @break
                                        @case('new_customer_roa') {{ $percent($record->new_customer_roa) }} @break
                                        @case('roi') {{ $percent($record->roi) }} @break
                                    @endswitch
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
