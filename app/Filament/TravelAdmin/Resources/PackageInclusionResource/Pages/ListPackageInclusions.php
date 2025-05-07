<?php

namespace App\Filament\TravelAdmin\Resources\PackageInclusionResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageInclusionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageInclusions extends ListRecords
{
    protected static string $resource = PackageInclusionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
