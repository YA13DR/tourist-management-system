<?php

namespace App\Filament\TravelAdmin\Resources\PackageDestinationResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageDestinationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageDestination extends EditRecord
{
    protected static string $resource = PackageDestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
