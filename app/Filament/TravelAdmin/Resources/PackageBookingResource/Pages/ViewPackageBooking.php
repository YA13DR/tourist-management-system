<?php

namespace App\Filament\TravelAdmin\Resources\PackageBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageBooking extends ViewRecord
{
    protected static string $resource = PackageBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
