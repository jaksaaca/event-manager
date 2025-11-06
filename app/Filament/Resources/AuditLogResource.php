<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogResource extends Resource
{
    protected static ?string $model = \Spatie\Activitylog\Models\Activity::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Audit Log';

    public static function shouldRegisterNavigation(): bool
    {
        return static::userHasAnyRole(['Super Admin', 'Admin']);
    }
    public static function canViewAny(): bool
    {
        return static::userHasAnyRole(['Super Admin', 'Admin']);
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.email')
                    ->label('User')
                    ->formatStateUsing(fn ($record) => optional($record->causer)->email ?? '-'),

                TextColumn::make('event')->label('Aksi')->sortable(),

                TextColumn::make('description')->label('Detail')->limit(80)->wrap(),

                TextColumn::make('subject_type')
                    ->label('Model')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // event ada kolomnya, aman
                SelectFilter::make('event')
                    ->label('Filter Aksi')
                    ->options([
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'login'  => 'Login',
                        'logout' => 'Logout',
                    ])
                    ->query(fn ($query, $data) =>
                        $data['value'] ? $query->where('event', $data['value']) : $query
                    ),

                SelectFilter::make('user')
                    ->label('Filter User')
                    ->options(\App\Models\User::orderBy('email')->pluck('email', 'id')->toArray())
                    ->query(function ($query, $data) {
                        if (!($data['value'] ?? null)) return $query;
                        return $query->where('causer_type', \App\Models\User::class)
                                     ->where('causer_id', $data['value']);
                    }),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }

    private static function userHasAnyRole(array $roleNames): bool
    {
        $userId = Auth::id();
        if (! $userId) return false;

        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('model_has_roles.model_id', $userId)
            ->whereIn('roles.name', $roleNames)
            ->exists();
    }
}
