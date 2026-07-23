@php
    $cell = 'padding: 8px 10px; white-space: nowrap; text-align: center;';
    $typeLabels = [
        'uid' => '用户ID',
        'phone' => '手机号',
        'idCard' => '身份证',
    ];
@endphp

<x-filament-panels::page>
    <div style="display: grid; gap: 14px;">
        <div style="border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; overflow: hidden;">
            <div style="padding: 14px;">
                <form wire:submit.prevent="search" style="display: flex; align-items: end; gap: 10px; flex-wrap: wrap;">
                    <div style="min-width: min(420px, 100%);">
                        <label style="display: block; margin-bottom: 5px; font-size: 12px; font-weight: 700;">用户ID / 手机号 / 身份证号</label>
                        <input type="text" wire:model.defer="keyword" placeholder="请输入用户ID、手机号或身份证号" style="width: 420px; max-width: 100%; border: 1px solid #d1d5db; border-radius: 7px; padding: 8px 10px; font-size: 13px;">
                    </div>
                    <button type="submit" style="height: 37px; padding: 0 14px; border-radius: 7px; background: #0ea5e9; color: #fff; font-size: 13px; font-weight: 700;">查询</button>
                </form>
            </div>
        </div>

        @if ($message)
            <div style="border: 1px solid #fde68a; border-radius: 8px; background: #fffbeb; padding: 12px 14px; color: #92400e; font-size: 13px; font-weight: 600;">
                {{ $message }}
            </div>
        @endif

        @if ($searchedKeyword)
            <div style="display: flex; gap: 8px; flex-wrap: wrap; font-size: 12px; color: #475569;">
                <span style="display: inline-flex; padding: 5px 8px; border-radius: 999px; background: #f1f5f9;">查询条件：{{ $searchedKeyword }}</span>
                @if ($searchType)
                    <span style="display: inline-flex; padding: 5px 8px; border-radius: 999px; background: #e0f2fe; color: #075985;">识别类型：{{ $typeLabels[$searchType] ?? $searchType }}</span>
                @endif
                @foreach ($relatedQueries as $query)
                    <span style="display: inline-flex; padding: 5px 8px; border-radius: 999px; background: #ecfdf5; color: #047857;">关联查询：{{ $query }}</span>
                @endforeach
                @foreach ($lookupErrors as $error)
                    <span style="display: inline-flex; padding: 5px 8px; border-radius: 999px; background: #fef2f2; color: #b91c1c;">{{ $error }}</span>
                @endforeach
            </div>
        @endif

        @if ($primaryUser)
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; overflow: hidden;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; padding: 12px 14px; border-bottom: 1px solid #eef2f7;">
                    <div style="font-size: 14px; font-weight: 800;">主用户</div>
                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; padding: 4px 8px; border-radius: 999px; background: {{ $this->isBanned($primaryUser) ? '#fef2f2' : '#ecfdf5' }}; color: {{ $this->isBanned($primaryUser) ? '#b91c1c' : '#047857' }}; font-size: 12px; font-weight: 700;">
                            {{ $this->banStatus($primaryUser) }}
                        </span>
                        <button
                            type="button"
                            wire:click="openActionModal('{{ $this->userId($primaryUser) }}')"
                            style="padding: 5px 10px; border-radius: 7px; background: {{ $this->isBanned($primaryUser) ? '#16a34a' : '#dc2626' }}; color: #fff; font-size: 12px; font-weight: 700;"
                        >
                            {{ $this->isBanned($primaryUser) ? '解封' : '封号' }}
                        </button>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0; font-size: 13px;">
                    @foreach ($this->userFields($primaryUser) as $field)
                        <div style="display: grid; gap: 4px; padding: 10px 14px; border-bottom: 1px solid #f1f5f9;">
                            <div style="color: #64748b; font-size: 12px; font-weight: 700;">{{ $field['label'] }}</div>
                            <div style="color: #111827; font-weight: 700; word-break: break-all;">{{ $field['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($users !== [])
            @include('filament.pages.partials.player-lookup-table', [
                'title' => '查询结果',
                'users' => $users,
                'cell' => $cell,
            ])
        @endif

        @if ($showActionModal)
            <div style="position: fixed; inset: 0; z-index: 60; display: grid; place-items: center; padding: 16px; background: rgba(15, 23, 42, 0.42);">
                <div style="width: min(460px, 100%); border-radius: 10px; background: #fff; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.22); overflow: hidden;">
                    <div style="padding: 14px 16px; border-bottom: 1px solid #eef2f7;">
                        <div style="font-size: 15px; font-weight: 800;">确认{{ $pendingAction }}</div>
                        <div style="margin-top: 4px; color: #64748b; font-size: 12px;">UID：{{ $pendingUid }}</div>
                    </div>

                    <div style="padding: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700;">备注</label>
                        <textarea
                            wire:model.defer="actionRemark"
                            rows="4"
                            placeholder="请输入本次{{ $pendingAction }}原因"
                            style="width: 100%; resize: vertical; border: 1px solid #d1d5db; border-radius: 8px; padding: 9px 10px; font-size: 13px; line-height: 1.5;"
                        ></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 8px; padding: 12px 16px; border-top: 1px solid #eef2f7; background: #f8fafc;">
                        <button
                            type="button"
                            wire:click="closeActionModal"
                            style="padding: 7px 12px; border-radius: 7px; background: #fff; color: #334155; border: 1px solid #d1d5db; font-size: 12px; font-weight: 700;"
                        >
                            取消
                        </button>
                        <button
                            type="button"
                            wire:click="confirmAction"
                            style="padding: 7px 12px; border-radius: 7px; background: {{ $pendingAction === \App\Models\PlayerActionLog::ACTION_UNBAN ? '#16a34a' : '#dc2626' }}; color: #fff; font-size: 12px; font-weight: 700;"
                        >
                            确认{{ $pendingAction }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
