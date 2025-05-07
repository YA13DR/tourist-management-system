<?php

namespace App\Filament\TravelAdmin\Resources\PackageDestinationResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageDestinationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageDestinations extends ListRecords
{
    protected static string $resource = PackageDestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
