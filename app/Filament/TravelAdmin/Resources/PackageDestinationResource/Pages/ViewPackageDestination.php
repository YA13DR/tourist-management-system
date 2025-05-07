<?php

namespace App\Filament\TravelAdmin\Resources\PackageDestinationResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageDestinationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageDestination extends ViewRecord
{
    protected static string $resource = PackageDestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
