<?php

namespace App\Filament\Resources\PlayerActionLogs;

use App\Filament\Resources\PlayerActionLogs\Pages\ListPlayerActionLogs;
use App\Models\PlayerActionLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PlayerActionLogResource extends Resource
{
    protected static ?string $model = PlayerActionLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = '操作日志';

    protected static ?string $modelLabel = '操作日志';

    protected static ?string $pluralModelLabel = '操作日志';

    protected static string|UnitEnum|null $navigationGroup = '用户信息';

    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('operated_at')
                    ->label('操作时间')
                    ->dateTime('Y/m/d H:i:s')
                    ->sortable(),
                TextColumn::make('uid')
                    ->label('UID')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('action')
                    ->label('行为')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PlayerActionLog::ACTION_BAN => 'danger',
                        PlayerActionLog::ACTION_UNBAN => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('remark')
                    ->label('备注')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('行为')
                    ->options([
                        PlayerActionLog::ACTION_BAN => PlayerActionLog::ACTION_BAN,
                        PlayerActionLog::ACTION_UNBAN => PlayerActionLog::ACTION_UNBAN,
                    ]),
            ])
            ->defaultSort('operated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayerActionLogs::route('/'),
        ];
    }
}
