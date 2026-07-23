<?php

namespace App\Filament\Resources\RetentionStatistics\Pages;

use App\Filament\Resources\RetentionStatistics\RetentionStatisticResource;
use App\Models\DailyStatistic;
use Carbon\CarbonImmutable;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Url;

class ManageRetentionStatistics extends Page
{
    protected static string $resource = RetentionStatisticResource::class;

    protected string $view = 'filament.resources.retention-statistics.pages.manage-retention-statistics';

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $until = null;

    public function resetFilters(): void
    {
        $this->from = null;
        $this->until = null;
    }

    public function getRecords(): Collection
    {
        return $this->baseQuery()
            ->orderByDesc('day')
            ->get();
    }

    public function isRetentionAvailable(DailyStatistic $record, int $days): bool
    {
        return $record->day->toImmutable()->lte($this->retentionCutoff($days));
    }

    public function getSummary(): array
    {
        return [
            'register_num' => (int) $this->baseQuery()->sum('register_num'),
            'new_recharge_user_count' => (int) $this->baseQuery()->sum('new_recharge_user_count'),
            ...$this->retentionSummary('d1', 1, 'd1_login_num', 'register_num'),
            ...$this->retentionSummary('d3', 3, 'd3_login_num', 'register_num'),
            ...$this->retentionSummary('d7', 7, 'd7_login_num', 'register_num'),
            ...$this->retentionSummary('rd1', 1, 'rd1_login_num', 'new_recharge_user_count'),
            ...$this->retentionSummary('rd3', 3, 'rd3_login_num', 'new_recharge_user_count'),
            ...$this->retentionSummary('rd7', 7, 'rd7_login_num', 'new_recharge_user_count'),
        ];
    }

    private function retentionSummary(string $prefix, int $days, string $numeratorColumn, string $denominatorColumn): array
    {
        $summary = $this->baseQuery()
            ->whereDate('day', '<=', $this->retentionCutoff($days)->toDateString())
            ->selectRaw("sum({$numeratorColumn}) as numerator, sum({$denominatorColumn}) as denominator")
            ->first();

        $numerator = (int) ($summary?->numerator ?? 0);
        $denominator = (int) ($summary?->denominator ?? 0);

        return [
            "{$prefix}_login_num" => $numerator,
            "{$prefix}_rate" => $this->ratio($numerator, $denominator),
        ];
    }

    private function retentionCutoff(int $days): CarbonImmutable
    {
        return CarbonImmutable::today(config('app.timezone'))->subDays($days + 1);
    }

    private function baseQuery(): Builder|Relation
    {
        return DailyStatistic::query()
            ->when($this->from, fn (Builder $query, string $date): Builder => $query->whereDate('day', '>=', $date))
            ->when($this->until, fn (Builder $query, string $date): Builder => $query->whereDate('day', '<=', $date));
    }

    private function ratio(float|int $numerator, float|int $denominator): float
    {
        if ((float) $denominator === 0.0) {
            return 0.0;
        }

        return (float) $numerator / (float) $denominator;
    }
}
