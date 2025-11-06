<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Category';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_kategori')->label('Nama Kategori')->required()->maxLength(100),
            Forms\Components\TextInput::make('kode_kategori')->label('Kode Kategori')->required()->unique(ignoreRecord: true)->maxLength(50),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kode_kategori')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Dibuat'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn (Category $record) => activity()->performedOn($record)->event('update')->log('Update kategori')),
                Tables\Actions\DeleteAction::make()
                    ->after(fn (Category $record) => activity()->performedOn($record)->event('delete')->log('Hapus kategori')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}