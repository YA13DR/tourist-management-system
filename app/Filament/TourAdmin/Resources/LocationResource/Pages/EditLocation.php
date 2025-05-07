<?php

namespace App\Filament\TourAdmin\Resources\LocationResource\Pages;

use App\Filament\TourAdmin\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocation extends EditRecord
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
