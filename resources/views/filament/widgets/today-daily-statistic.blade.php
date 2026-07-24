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
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
            <div>
                <div style="font-size: 16px; line-height: 1.35; color: #111827; font-weight: 800;">今日数据</div>
                <div style="margin-top: 2px; font-size: 13px; color: #64748b;">{{ $today->format('Y/m/d') }}</div>
            </div>
            <button
                type="button"
                wire:click="syncToday"
                wire:loading.attr="disabled"
                wire:target="syncToday"
                style="display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; background: #0ea5e9; color: #fff; padding: 7px 11px; font-size: 13px; font-weight: 700;"
            >
                <svg wire:loading.remove wire:target="syncToday" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                    <path d="M3 21v-5h5"></path>
                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                    <path d="M16 8h5V3"></path>
                </svg>
                <span wire:loading.remove wire:target="syncToday">刷新今日数据</span>
                <span wire:loading wire:target="syncToday">刷新中</span>
            </button>
        </div>

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
