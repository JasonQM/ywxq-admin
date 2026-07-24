@php
    $money = fn (float|int|string|null $value): string => number_format((float) ($value ?? 0), 2);
    $number = fn (float|int|string|null $value): string => number_format((float) ($value ?? 0), 0);
    $percent = fn (float|int|string|null $value): string => number_format(((float) ($value ?? 0)) * 100, 2) . '%';

    $items = $record ? [
        ['label' => '消耗', 'value' => $money($record->consume_amount)],
        ['label' => '登录', 'value' => $number($record->login_num)],
        ['label' => 'DAU', 'value' => $number($record->active_dau)],
        ['label' => '注册', 'value' => $number($record->register_num)],
        ['label' => '注册率', 'value' => $percent($record->register_rate)],
        ['label' => '实名', 'value' => $number($record->real_name_num)],
        ['label' => '实名率', 'value' => $percent($record->real_name_rate)],
        ['label' => '新付数', 'value' => $number($record->new_recharge_user_count)],
        ['label' => '总付数', 'value' => $number($record->recharge_user_count)],
        ['label' => '付费率', 'value' => $percent($record->pay_rate)],
        ['label' => '注册成本', 'value' => $money($record->register_cost)],
        ['label' => '付费成本', 'value' => $money($record->pay_cost)],
        ['label' => '新付额', 'value' => $money($record->new_recharge_amount)],
        ['label' => '总付额', 'value' => $money($record->recharge_amount)],
        ['label' => '兑人', 'value' => $number($record->withdraw_user_count)],
        ['label' => '兑额', 'value' => $money($record->withdraw_amount)],
        ['label' => 'ARPU', 'value' => $money($record->arpu)],
        ['label' => 'ARPPU', 'value' => $money($record->arppu)],
        ['label' => '新ROA', 'value' => $percent($record->new_customer_roa)],
        ['label' => 'ROI', 'value' => $percent($record->roi)],
    ] : [];
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">今日数据</x-slot>
        <x-slot name="description">{{ $today->format('Y/m/d') }}</x-slot>

        @if ($record)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(128px, 1fr)); gap: 8px;">
                @foreach ($items as $item)
                    <div style="display: grid; gap: 3px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; padding: 10px 12px;">
                        <div style="font-size: 12px; color: #64748b; font-weight: 700;">{{ $item['label'] }}</div>
                        <div style="font-size: 18px; line-height: 1.25; color: #111827; font-weight: 800;">{{ $item['value'] }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="padding: 28px 12px; text-align: center; color: #64748b; font-size: 14px;">
                今日暂无数据，刷新数据后会在这里展示。
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
