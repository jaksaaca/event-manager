<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserResource extends Resource
{
    protected static ?string $model = \App\Models\User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'User';

    /** Sidebar hanya tampil untuk Super Admin */
    public static function shouldRegisterNavigation(): bool
    {
        return static::isSuperAdmin();
    }

    /** List page hanya Super Admin */
    public static function canViewAny(): bool
    {
        return static::isSuperAdmin();
    }

    /** Create/Edit/Delete khusus Super Admin */
    public static function canCreate(): bool
    {
        return static::isSuperAdmin();
    }
    public static function canEdit($record): bool
    {
        return static::isSuperAdmin();
    }
    public static function canDelete($record): bool
    {
        return static::isSuperAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(120),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->minLength(8)
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state)),

            Select::make('roles')
                ->label('Role')
                ->relationship('roles', 'name')
                ->preload()
                ->searchable()
                ->required()
                ->multiple(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')->label('Role')->badge(),
                TextColumn::make('created_at')->dateTime('d M Y')->label('Dibuat'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->relationship('roles', 'name'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()
                    ->after(fn (User $record) => activity()
                        ->performedOn($record)->event('update')->log('Update user')),

                \Filament\Tables\Actions\DeleteAction::make()
                    ->before(function (User $record) {
                        if (Auth::id() === $record->id) {
                            throw ValidationException::withMessages([
                                'id' => 'Tidak boleh menghapus akun sendiri.',
                            ]);
                        }
                    })
                    ->after(fn (User $record) => activity()
                        ->performedOn($record)->event('delete')->log('Hapus user')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    /** Helper: aman untuk IDE/linter & runtime */
    private static function isSuperAdmin(): bool
    {
        $u = Auth::user();
        return $u instanceof \App\Models\User
            && method_exists($u, 'hasRole')
            && $u->hasRole('Super Admin');
    }
}
