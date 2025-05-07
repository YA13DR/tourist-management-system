<?php

namespace App\Filament\TravelAdmin\Resources\PackageInclusionResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageInclusionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageInclusion extends ViewRecord
{
    protected static string $resource = PackageInclusionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
