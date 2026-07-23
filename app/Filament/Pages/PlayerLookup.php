<?php

namespace App\Filament\Pages;

use App\Models\PlayerActionLog;
use App\Services\PlayerLookupService;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Throwable;
use UnitEnum;

class PlayerLookup extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = '用户查询';

    protected static ?string $title = '用户查询';

    protected static string|UnitEnum|null $navigationGroup = '用户信息';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.player-lookup';

    public ?string $keyword = null;

    public ?string $searchedKeyword = null;

    public ?string $searchType = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $primaryUser = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $users = [];

    /**
     * @var array<int, string>
     */
    public array $relatedQueries = [];

    /**
     * @var array<int, string>
     */
    public array $lookupErrors = [];

    public ?string $message = null;

    public bool $showActionModal = false;

    public ?string $pendingUid = null;

    public ?string $pendingAction = null;

    public ?string $actionRemark = null;

    public function search(PlayerLookupService $service): void
    {
        $keyword = trim((string) $this->keyword);

        if ($keyword === '') {
            $this->warn('请输入用户ID、手机号或身份证号');

            return;
        }

        $this->resetSearchState($keyword);

        try {
            $initialUsers = $this->initialLookup($service, $keyword);
        } catch (Throwable $exception) {
            report($exception);
            $this->message = '接口请求失败，请稍后重试';

            return;
        }

        $this->primaryUser = $initialUsers[0] ?? null;

        if ($this->primaryUser === null) {
            $this->message = '未查询到用户';

            return;
        }

        $allUsers = $initialUsers;

        foreach ($this->relationValues($this->primaryUser) as $type => $value) {
            try {
                $relatedUsers = match ($type) {
                    'phone' => $service->findByPhone($value),
                    'idCard' => $service->findByIdCard($value),
                };

                $this->relatedQueries[] = ($type === 'phone' ? '手机号' : '身份证').'：'.$value;
                $allUsers = [...$allUsers, ...$relatedUsers];
            } catch (Throwable $exception) {
                report($exception);
                $this->lookupErrors[] = ($type === 'phone' ? '手机号' : '身份证').'关联查询失败';
            }
        }

        $this->users = $this->uniqueUsers($allUsers);
    }

    public function openActionModal(string $uid): void
    {
        $user = $this->findUserInResults($uid);

        if ($user === null) {
            $this->warn('未找到用户');

            return;
        }

        $this->pendingUid = $uid;
        $this->pendingAction = $this->isBanned($user) ? PlayerActionLog::ACTION_UNBAN : PlayerActionLog::ACTION_BAN;
        $this->actionRemark = null;
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->showActionModal = false;
        $this->pendingUid = null;
        $this->pendingAction = null;
        $this->actionRemark = null;
    }

    public function confirmAction(PlayerLookupService $service): void
    {
        $uid = trim((string) $this->pendingUid);
        $action = (string) $this->pendingAction;
        $remark = trim((string) $this->actionRemark);

        if ($uid === '' || ! in_array($action, [PlayerActionLog::ACTION_BAN, PlayerActionLog::ACTION_UNBAN], true)) {
            $this->warn('操作信息不完整');

            return;
        }

        if ($remark === '') {
            $this->warn('请填写备注');

            return;
        }

        try {
            if ($action === PlayerActionLog::ACTION_UNBAN) {
                $service->unban($uid);
                $title = '已解封用户';
            } else {
                $service->ban($uid);
                $title = '已封号用户';
            }

            PlayerActionLog::query()->create([
                'uid' => $uid,
                'action' => $action,
                'remark' => $remark,
                'operated_at' => now(),
            ]);

            Notification::make()
                ->title($title)
                ->success()
                ->send();

            $this->closeActionModal();

            if ($this->searchedKeyword) {
                $this->keyword = $this->searchedKeyword;
                $this->search($service);
            }
        } catch (Throwable $exception) {
            report($exception);
            $this->warn('操作失败，请稍后重试');
        }
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function userFields(array $user): array
    {
        return [
            ['label' => '用户ID', 'value' => $this->userId($user)],
            ['label' => '注册时间', 'value' => $this->registeredAt($user)],
            ['label' => '最后登录时间', 'value' => $this->lastLoginAt($user)],
            ['label' => '累计充值', 'value' => $this->money($this->numberValue($user, 'c_r'))],
            ['label' => '兑换金额', 'value' => $this->money($this->exchangedAmount($user))],
            ['label' => '手机号', 'value' => $this->textValue($user, 'phone')],
            ['label' => '真实姓名', 'value' => $this->realUserName($user)],
            ['label' => '身份证', 'value' => $this->idCardOf($user)],
            ['label' => '支付宝账号', 'value' => $this->textValue($user, 'zhifubao')],
            ['label' => '支付宝姓名', 'value' => $this->textValue($user, 'zhifubao_name')],
        ];
    }

    public function userId(array $user): string
    {
        return $this->textValue($user, '_id', 'uid');
    }

    public function registeredAt(array $user): string
    {
        return $this->dateTimeValue(Arr::get($user, 'ct')) ?: $this->dayValue(Arr::get($user, 'ctDay'));
    }

    public function lastLoginAt(array $user): string
    {
        return $this->dateTimeValue(Arr::get($user, 'lt')) ?: $this->dayValue(Arr::get($user, 'ld'));
    }

    public function realUserName(array $user): string
    {
        return $this->textValue($user, 'real_name', 'realNameName', 'trueName', 'zhifubao_name');
    }

    public function idCardOf(array $user): string
    {
        return $this->textValue($user, 'shenfenzheng');
    }

    public function exchangedAmount(array $user): float
    {
        return max($this->numberValue($user, 'c_c') - $this->numberValue($user, 'coin'), 0) / 10000;
    }

    public function money(float|int $value): string
    {
        return '¥'.number_format((float) $value, 2);
    }

    public function isBanned(array $user): bool
    {
        return (int) Arr::get($user, 'banDay', 0) === 20991231;
    }

    public function banStatus(array $user): string
    {
        return $this->isBanned($user) ? '封号' : '正常';
    }

    private function resetSearchState(string $keyword): void
    {
        $this->keyword = $keyword;
        $this->searchedKeyword = $keyword;
        $this->searchType = null;
        $this->primaryUser = null;
        $this->users = [];
        $this->relatedQueries = [];
        $this->lookupErrors = [];
        $this->message = null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function initialLookup(PlayerLookupService $service, string $keyword): array
    {
        $type = $this->detectKeywordType($keyword);
        $this->searchType = $type;

        return match ($type) {
            'uid' => array_filter([$service->findByUid($keyword)]),
            'phone' => $service->findByPhone($keyword),
            'idCard' => $service->findByIdCard($keyword),
        };
    }

    private function detectKeywordType(string $keyword): string
    {
        $upperKeyword = strtoupper($keyword);

        if (preg_match('/^\d{17}[\dX]$/', $upperKeyword) === 1) {
            return 'idCard';
        }

        if (preg_match('/^1\d{10}$/', $keyword) === 1) {
            return 'phone';
        }

        return 'uid';
    }

    /**
     * @return array{phone?: string, idCard?: string}
     */
    private function relationValues(array $user): array
    {
        $values = [];
        $phone = $this->textValue($user, 'phone');
        $idCard = $this->idCardOf($user);

        if ($phone !== '-' && $this->searchType !== 'phone') {
            $values['phone'] = $phone;
        }

        if ($idCard !== '-' && $this->searchType !== 'idCard') {
            $values['idCard'] = $idCard;
        }

        return $values;
    }

    /**
     * @param  array<int, array<string, mixed>>  $users
     * @return array<int, array<string, mixed>>
     */
    private function uniqueUsers(array $users): array
    {
        $unique = [];

        foreach ($users as $index => $user) {
            $key = $this->userId($user);
            if ($key === '-') {
                $key = 'row-'.$index;
            }

            $unique[$key] = $user;
        }

        return array_values($unique);
    }

    private function findUserInResults(string $uid): ?array
    {
        if ($this->primaryUser !== null && $this->userId($this->primaryUser) === $uid) {
            return $this->primaryUser;
        }

        foreach ($this->users as $user) {
            if ($this->userId($user) === $uid) {
                return $user;
            }
        }

        return null;
    }

    private function warn(string $message): void
    {
        Notification::make()
            ->title($message)
            ->warning()
            ->send();
    }

    private function numberValue(array $user, string $key): float
    {
        return (float) (Arr::get($user, $key, 0) ?: 0);
    }

    private function textValue(array $user, string ...$keys): string
    {
        foreach ($keys as $key) {
            $value = Arr::get($user, $key);

            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return '-';
    }

    private function dateTimeValue(mixed $value): ?string
    {
        if (! is_numeric($value) || (int) $value <= 0) {
            return null;
        }

        $timestamp = (int) $value;
        if ($timestamp > 9999999999) {
            $timestamp = (int) floor($timestamp / 1000);
        }

        return CarbonImmutable::createFromTimestamp($timestamp, config('app.timezone'))->format('Y/m/d H:i:s');
    }

    private function dayValue(mixed $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) !== 8) {
            return '-';
        }

        $date = CarbonImmutable::createFromFormat('Ymd', $digits, config('app.timezone'));

        return $date === false ? '-' : $date->format('Y/m/d');
    }
}
