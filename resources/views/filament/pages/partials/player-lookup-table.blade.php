<div style="border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; overflow: hidden;">
    <div style="padding: 12px 14px; border-bottom: 1px solid #eef2f7; font-size: 14px; font-weight: 800;">
        {{ $title }} <span style="color: #64748b; font-size: 12px;">({{ count($users) }} 条)</span>
    </div>
    <div style="width: 100%; overflow-x: auto;">
        <table style="min-width: 1038px; width: 1038px; border-collapse: separate; border-spacing: 0; table-layout: fixed; font-size: 12px; color: #111827;">
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
