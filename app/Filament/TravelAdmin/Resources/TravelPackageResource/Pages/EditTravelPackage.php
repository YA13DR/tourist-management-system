<?php

namespace App\Filament\TravelAdmin\Resources\TravelPackageResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelPackage extends EditRecord
{
    protected static string $resource = TravelPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
