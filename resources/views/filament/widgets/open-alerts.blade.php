<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">待处理预警</x-slot>
        <x-slot name="description">最近需要关注的数据异常</x-slot>

        <div style="display: grid; gap: 10px;">
            @forelse ($alerts as $alert)
                <div style="display: grid; gap: 4px; border: 1px solid #eef2f7; border-radius: 8px; padding: 10px 12px; background: #fff;">
                    <div style="display: flex; justify-content: space-between; gap: 10px; align-items: center;">
                        <div style="font-size: 13px; font-weight: 700; color: #111827;">{{ $alert->title }}</div>
                        <div style="font-size: 12px; color: #6b7280;">{{ $alert->day->format('Y/m/d') }}</div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280;">{{ $alert->message }}</div>
                </div>
            @empty
                <div style="padding: 18px; text-align: center; color: #6b7280; font-size: 13px;">暂无待处理预警</div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
