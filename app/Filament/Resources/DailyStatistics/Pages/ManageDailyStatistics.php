<?php

namespace App\Filament\Resources\DailyStatistics\Pages;

use App\Filament\Resources\DailyStatistics\DailyStatisticResource;
use App\Models\DailyStatistic;
use App\Services\GameStatisticsSyncService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Url;

class ManageDailyStatistics extends Page
{
    protected static string $resource = DailyStatisticResource::class;

    protected string $view = 'filament.resources.daily-statistics.pages.manage-daily-statistics';

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $until = null;

    #[Url]
    public string $fontSize = '12';

    /**
     * @var array<int, string>
     */
    public array $consumeAmounts = [];

    /**
     * @var array<int, string>
     */
    public array $visibleColumns = [
        'day',
        'consume_amount',
        'login_num',
        'register_num',
        'register_cost',
        'active_dau',
        'register_rate',
        'real_name_num',
        'real_name_rate',
        'new_recharge_user_count',
        'pay_cost',
        'recharge_user_count',
        'pay_rate',
        'new_recharge_amount',
        'recharge_amount',
        'withdraw_user_count',
        'withdraw_amount',
        'arpu',
        'arppu',
        'new_customer_roa',
        'roi',
    ];

    public function mount(): void
    {
        $this->fillConsumeAmounts();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label('刷新数据')
                ->icon(Heroicon::ArrowPath)
                ->requiresConfirmation()
                ->action(function (GameStatisticsSyncService $service): void {
                    $result = $service->syncAll();

                    Notification::make()
                        ->title('刷新完成')
                        ->body("统计更新 {$result['statsUpdated']} 条，实名更新 {$result['realNameUpdated']} 条，预警 {$result['alerts']} 条。")
                        ->success()
                        ->send();

                    $this->fillConsumeAmounts();
                }),
        ];
    }

    public function updatedFrom(): void
    {
        $this->fillConsumeAmounts();
    }

    public function updatedUntil(): void
    {
        $this->fillConsumeAmounts();
    }

    public function resetFilters(): void
    {
        $this->from = null;
        $this->until = null;
        $this->fillConsumeAmounts();
    }

    public function updatedFontSize(): void
    {
        if (! in_array($this->fontSize, ['8', '9', '10', '11', '12', '13', '14'], true)) {
            $this->fontSize = '12';
        }
    }

    public function saveConsume(int $id): void
    {
        $stat = DailyStatistic::query()->findOrFail($id);
        $stat->consume_amount = (float) ($this->consumeAmounts[$id] ?? 0);
        $stat->save();

        Notification::make()
            ->title('消耗已保存')
            ->success()
            ->send();

        $this->fillConsumeAmounts();
    }

    public function getRecords(): Collection
    {
        return $this->baseQuery()
            ->orderByDesc('day')
            ->get();
    }

    public function getSummary(): array
    {
        $summary = $this->baseQuery()
            ->selectRaw('
                count(*) as days_count,
                sum(consume_amount) as consume_amount,
                sum(login_num) as login_num,
                sum(active_dau) as active_dau,
                sum(register_num) as register_num,
                sum(real_name_num) as real_name_num,
                sum(new_recharge_user_count) as new_recharge_user_count,
                sum(recharge_user_count) as recharge_user_count,
                sum(new_recharge_amount) as new_recharge_amount,
                sum(recharge_amount) as recharge_amount,
                sum(withdraw_user_count) as withdraw_user_count,
                sum(withdraw_amount) as withdraw_amount
            ')
            ->first();

        $consume = (float) ($summary?->consume_amount ?? 0);
        $login = (int) ($summary?->login_num ?? 0);
        $register = (int) ($summary?->register_num ?? 0);
        $realName = (int) ($summary?->real_name_num ?? 0);
        $newRechargeUsers = (int) ($summary?->new_recharge_user_count ?? 0);
        $rechargeUsers = (int) ($summary?->recharge_user_count ?? 0);
        $newRecharge = (float) ($summary?->new_recharge_amount ?? 0);
        $recharge = (float) ($summary?->recharge_amount ?? 0);
        $withdraw = (float) ($summary?->withdraw_amount ?? 0);

        return [
            'days_count' => (int) ($summary?->days_count ?? 0),
            'consume_amount' => $consume,
            'login_num' => $login,
            'active_dau' => (int) ($summary?->active_dau ?? 0),
            'register_num' => $register,
            'register_rate' => $this->ratio($register, $login),
            'real_name_num' => $realName,
            'real_name_rate' => $this->ratio($realName, $register),
            'new_recharge_user_count' => $newRechargeUsers,
            'recharge_user_count' => $rechargeUsers,
            'pay_rate' => $this->ratio($newRechargeUsers, $register),
            'register_cost' => $this->moneyRatio($consume, $register),
            'pay_cost' => $this->moneyRatio($consume, $rechargeUsers),
            'new_recharge_amount' => $newRecharge,
            'recharge_amount' => $recharge,
            'withdraw_user_count' => (int) ($summary?->withdraw_user_count ?? 0),
            'withdraw_amount' => $withdraw,
            'arpu' => $this->moneyRatio($recharge, $login),
            'arppu' => $this->moneyRatio($recharge, $rechargeUsers),
            'new_customer_roa' => $this->ratio($newRecharge, $consume),
            'roi' => $this->ratio($recharge - $withdraw, $consume),
        ];
    }

    private function baseQuery(): Builder|Relation
    {
        return DailyStatistic::query()
            ->when($this->from, fn (Builder $query, string $date): Builder => $query->whereDate('day', '>=', $date))
            ->when($this->until, fn (Builder $query, string $date): Builder => $query->whereDate('day', '<=', $date));
    }

    private function fillConsumeAmounts(): void
    {
        $this->consumeAmounts = $this->baseQuery()
            ->pluck('consume_amount', 'id')
            ->map(fn ($value) => number_format((float) $value, 2, '.', ''))
            ->all();
    }

    private function ratio(float|int $numerator, float|int $denominator): float
    {
        if ((float) $denominator === 0.0) {
            return 0.0;
        }

        return (float) $numerator / (float) $denominator;
    }

    private function moneyRatio(float|int $numerator, float|int $denominator): float
    {
        if ((float) $denominator === 0.0) {
            return 0.0;
        }

        return round((float) $numerator / (float) $denominator, 2);
    }
}
