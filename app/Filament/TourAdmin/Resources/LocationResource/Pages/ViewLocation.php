<?php

namespace App\Filament\TourAdmin\Resources\LocationResource\Pages;

use App\Filament\TourAdmin\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLocation extends ViewRecord
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
