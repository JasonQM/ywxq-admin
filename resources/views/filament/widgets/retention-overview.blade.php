<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">留存概览</x-slot>
        <x-slot name="description">只统计已经满足时间差的日期</x-slot>

        <div style="overflow-x: auto;">
            <table style="width: 100%; min-width: 560px; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; color: #374151;">
                        <th style="padding: 9px; text-align: left;">指标</th>
                        <th style="padding: 9px; text-align: center;">截止日期</th>
                        <th style="padding: 9px; text-align: center;">人数</th>
                        <th style="padding: 9px; text-align: center;">留存率</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr style="background: {{ $loop->even ? '#fbfdff' : '#fff' }};">
                            <td style="padding: 9px; font-weight: 600;">{{ $row['label'] }}</td>
                            <td style="padding: 9px; text-align: center; color: #6b7280;">{{ $row['cutoff'] }}</td>
                            <td style="padding: 9px; text-align: center;">{{ number_format($row['count']) }}</td>
                            <td style="padding: 9px; text-align: center; font-weight: 700;">{{ number_format($row['rate'] * 100, 2) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
