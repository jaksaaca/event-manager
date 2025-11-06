<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Event';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_event')->required()->label('Nama Event')->maxLength(150),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'nama_kategori')
                ->required()
                ->label('Kategori')
                ->preload()
                ->searchable(),
            Forms\Components\Textarea::make('deskripsi')->rows(5)->label('Deskripsi'),
            Forms\Components\DateTimePicker::make('tanggal_event')->required()->label('Tanggal Event')->native(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nama_event')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('category.nama_kategori')->label('Kategori')->sortable(),
            Tables\Columns\TextColumn::make('tanggal_event')->dateTime('d M Y')->label('Tanggal'),
        ])
        ->actions([
            Tables\Actions\EditAction::make()
                ->after(fn (Event $record) => activity()->performedOn($record)->event('update')->log('Update event')),
            Tables\Actions\DeleteAction::make()
                ->after(fn (Event $record) => activity()->performedOn($record)->event('delete')->log('Hapus event')),
        ])
        ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
