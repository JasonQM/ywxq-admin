<?php

namespace App\Filament\Resources\RetentionStatistics;

use App\Filament\Resources\RetentionStatistics\Pages\ManageRetentionStatistics;
use App\Models\DailyStatistic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class RetentionStatisticResource extends Resource
{
    protected static ?string $model = DailyStatistic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = '留存数据';

    protected static ?string $modelLabel = '留存数据';

    protected static ?string $pluralModelLabel = '留存数据';

    protected static string|UnitEnum|null $navigationGroup = '数据明细';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day')->label('日期')->date('Y/m/d')->sortable(),
                TextColumn::make('register_num')->label('注册人数')->numeric()->sortable(),
                TextColumn::make('new_recharge_user_count')->label('新客付费人')->numeric()->sortable(),
                TextColumn::make('d1_login_num')->label('次留')->numeric()->sortable(),
                TextColumn::make('d1_rate')->label('次留率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('d3_login_num')->label('三留')->numeric()->sortable(),
                TextColumn::make('d3_rate')->label('三留率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('d7_login_num')->label('7留')->numeric()->sortable(),
                TextColumn::make('d7_rate')->label('7留率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('rd1_login_num')->label('付费次留')->numeric()->sortable(),
                TextColumn::make('rd1_rate')->label('付费次留率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('rd3_login_num')->label('付费三留')->numeric()->sortable(),
                TextColumn::make('rd3_rate')->label('付费三留率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
                TextColumn::make('rd7_login_num')->label('付费7留')->numeric()->sortable(),
                TextColumn::make('rd7_rate')->label('付费7留率')->formatStateUsing(fn ($state) => self::percent($state))->sortable(),
            ])
            ->defaultSort('day', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRetentionStatistics::route('/'),
        ];
    }

    private static function percent(mixed $state): string
    {
        return number_format(((float) $state) * 100, 2).'%';
    }
}
