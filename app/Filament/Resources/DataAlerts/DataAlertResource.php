<?php

namespace App\Filament\Resources\DataAlerts;

use App\Filament\Resources\DataAlerts\Pages\ManageDataAlerts;
use App\Models\DataAlert;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class DataAlertResource extends Resource
{
    protected static ?string $model = DataAlert::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BellAlert;

    protected static ?string $navigationLabel = '异常预警';

    protected static ?string $modelLabel = '异常预警';

    protected static ?string $pluralModelLabel = '异常预警';

    protected static string|UnitEnum|null $navigationGroup = '数据预警';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('day')->label('日期')->disabled(),
                Select::make('level')
                    ->label('级别')
                    ->options(self::levelOptions())
                    ->disabled(),
                TextInput::make('title')->label('标题')->disabled(),
                Textarea::make('message')->label('说明')->disabled()->columnSpanFull(),
                Select::make('status')
                    ->label('处理状态')
                    ->options(self::statusOptions())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day')->label('日期')->date('Y/m/d')->sortable(),
                TextColumn::make('level')
                    ->label('级别')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::levelOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('title')->label('预警')->searchable(),
                TextColumn::make('message')->label('说明')->wrap()->limit(80),
                TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        DataAlert::STATUS_OPEN => 'danger',
                        DataAlert::STATUS_RESOLVED => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('level')->label('级别')->options(self::levelOptions()),
                SelectFilter::make('status')->label('状态')->options(self::statusOptions()),
            ])
            ->defaultSort('day', 'desc')
            ->recordActions([
                Action::make('resolve')
                    ->label('已处理')
                    ->icon(Heroicon::Check)
                    ->color('success')
                    ->visible(fn (DataAlert $record): bool => $record->status !== DataAlert::STATUS_RESOLVED)
                    ->action(fn (DataAlert $record) => $record->update(['status' => DataAlert::STATUS_RESOLVED])),
                Action::make('ignore')
                    ->label('忽略')
                    ->icon(Heroicon::BellSlash)
                    ->color('gray')
                    ->visible(fn (DataAlert $record): bool => $record->status === DataAlert::STATUS_OPEN)
                    ->action(fn (DataAlert $record) => $record->update(['status' => DataAlert::STATUS_IGNORED])),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDataAlerts::route('/'),
        ];
    }

    private static function levelOptions(): array
    {
        return [
            'high' => '高',
            'medium' => '中',
            'low' => '低',
        ];
    }

    private static function statusOptions(): array
    {
        return [
            DataAlert::STATUS_OPEN => '待处理',
            DataAlert::STATUS_RESOLVED => '已处理',
            DataAlert::STATUS_IGNORED => '已忽略',
        ];
    }
}
