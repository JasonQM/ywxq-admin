<div style="border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; overflow: hidden;">
    <div style="padding: 12px 14px; border-bottom: 1px solid #eef2f7; font-size: 14px; font-weight: 800;">
        {{ $title }} <span style="color: #64748b; font-size: 12px;">({{ count($users) }} 条)</span>
    </div>
    <div style="width: 100%; overflow-x: auto;">
        <table style="min-width: 1198px; width: 1198px; border-collapse: separate; border-spacing: 0; table-layout: fixed; font-size: 12px; color: #111827;">
            <thead>
                <tr style="background: #f8fafc; box-shadow: inset 0 -1px 0 #eef2f7;">
                    <th style="{{ $cell }} width: 94px;">用户ID</th>
                    <th style="{{ $cell }} width: 130px;">注册时间</th>
                    <th style="{{ $cell }} width: 130px;">最后登录</th>
                    <th style="{{ $cell }} width: 92px;">累计充值</th>
                    <th style="{{ $cell }} width: 92px;">兑换金额</th>
                    <th style="{{ $cell }} width: 106px;">手机号</th>
                    <th style="{{ $cell }} width: 96px;">真实姓名</th>
                    <th style="{{ $cell }} width: 148px;">身份证</th>
                    <th style="{{ $cell }} width: 120px;">支付宝</th>
                    <th style="{{ $cell }} width: 110px;">支付宝姓名</th>
                    <th style="{{ $cell }} width: 70px;">状态</th>
                    <th style="{{ $cell }} width: 90px;">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr style="background: {{ $loop->even ? '#fbfdff' : '#fff' }}; box-shadow: inset 0 -1px 0 #f1f5f9;">
                        <td style="{{ $cell }}">{{ $this->userId($user) }}</td>
                        <td style="{{ $cell }}">{{ $this->registeredAt($user) }}</td>
                        <td style="{{ $cell }}">{{ $this->lastLoginAt($user) }}</td>
                        <td style="{{ $cell }}">{{ $this->money((float) ($user['c_r'] ?? 0)) }}</td>
                        <td style="{{ $cell }}">{{ $this->money($this->exchangedAmount($user)) }}</td>
                        <td style="{{ $cell }}">{{ $user['phone'] ?? '-' }}</td>
                        <td style="{{ $cell }}">{{ $this->realUserName($user) }}</td>
                        <td style="{{ $cell }}">{{ $this->idCardOf($user) }}</td>
                        <td style="{{ $cell }}">{{ $user['zhifubao'] ?? '-' }}</td>
                        <td style="{{ $cell }}">{{ $user['zhifubao_name'] ?? '-' }}</td>
                        <td style="{{ $cell }}">
                            <span style="display: inline-flex; padding: 3px 7px; border-radius: 999px; background: {{ $this->isBanned($user) ? '#fef2f2' : '#ecfdf5' }}; color: {{ $this->isBanned($user) ? '#b91c1c' : '#047857' }}; font-weight: 700;">
                                {{ $this->banStatus($user) }}
                            </span>
                        </td>
                        <td style="{{ $cell }}">
                            <button
                                type="button"
                                wire:click="openActionModal('{{ $this->userId($user) }}')"
                                style="padding: 5px 9px; border-radius: 7px; background: {{ $this->isBanned($user) ? '#16a34a' : '#dc2626' }}; color: #fff; font-size: 12px; font-weight: 700;"
                            >
                                {{ $this->isBanned($user) ? '解封' : '封号' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
