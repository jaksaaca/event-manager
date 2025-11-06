<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
{
    activity()->performedOn($this->record)->event('update')->log('Update event');
}

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
