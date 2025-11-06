<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class LatestLogins extends BaseWidget
{
    protected static ?string $heading = 'Last 5 Logins';
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = ['lg' => 1];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->where('event', 'login')
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('causer.email')
                    ->label('User')
                    ->formatStateUsing(fn ($record) => optional($record->causer)->email ?? '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i'),
            ])
            ->paginated(false);
    }
}
