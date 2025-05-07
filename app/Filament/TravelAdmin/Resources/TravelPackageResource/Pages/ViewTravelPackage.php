<?php

namespace App\Filament\TravelAdmin\Resources\TravelPackageResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTravelPackage extends ViewRecord
{
    protected static string $resource = TravelPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
