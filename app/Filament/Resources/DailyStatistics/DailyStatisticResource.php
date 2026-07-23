<?php

namespace App\Filament\Resources\DailyStatistics;

use App\Filament\Resources\DailyStatistics\Pages\ManageDailyStatistics;
use App\Models\DailyStatistic;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DailyStatisticResource extends Resource
{
    protected static ?string $model = DailyStatistic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?string $navigationLabel = '每日数据';

    protected static ?string $modelLabel = '每日数据';

    protected static ?string $pluralModelLabel = '每日数据';

    protected static string|UnitEnum|null $navigationGroup = '数据明细';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('day')
                    ->label('日期')
                    ->disabled(),
                TextInput::make('consume_amount')
                    ->label('消耗')
                    ->numeric()
                    ->minValue(0)
                    ->step('0.01')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day')->label('日期')->date('Y/m/d')->sortable(),
                TextColumn::make('consume_amount')->label('消耗')->money('CNY')->sortable(),
                TextColumn::make('login_num')->label('登录数')->numeric()->sortable(),
                TextColumn::make('active_dau')->label('活跃DAU')->numeric()->sortable(),
                TextColumn::make('register_num')->label('注册人数')->numeric()->sortable(),
                TextColumn::make('register_rate')->label('注册率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('real_name_num')->label('实名人数')->numeric()->sortable(),
                TextColumn::make('real_name_rate')->label('实名认证率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('new_recharge_user_count')->label('新客付费人')->numeric()->sortable(),
                TextColumn::make('recharge_user_count')->label('总付数')->numeric()->sortable(),
                TextColumn::make('pay_rate')->label('付费率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('register_cost')->label('注册成本')->money('CNY')->sortable(),
                TextColumn::make('pay_cost')->label('付费成本')->money('CNY')->sortable(),
                TextColumn::make('new_recharge_amount')->label('新客付费金额')->money('CNY')->sortable(),
                TextColumn::make('recharge_amount')->label('总付费金额')->money('CNY')->sortable(),
                TextColumn::make('withdraw_user_count')->label('兑换人数')->numeric()->sortable(),
                TextColumn::make('withdraw_amount')->label('兑换金额')->money('CNY')->sortable(),
                TextColumn::make('arpu')->label('arpu')->money('CNY')->sortable(),
                TextColumn::make('arppu')->label('arppu')->money('CNY')->sortable(),
                TextColumn::make('new_customer_roa')->label('新客roa')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('roi')->label('roi')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
            ])
            ->filters([
                Filter::make('day')
                    ->label('时间筛选')
                    ->schema([
                        DatePicker::make('from')->label('开始日期'),
                        DatePicker::make('until')->label('结束日期'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('day', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('day', '<=', $date))),
            ], layout: FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->defaultSort('day', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDailyStatistics::route('/'),
        ];
    }

    private static function percent(mixed $state): string
    {
        return number_format(((float) $state) * 100, 2).'%';
    }
}
