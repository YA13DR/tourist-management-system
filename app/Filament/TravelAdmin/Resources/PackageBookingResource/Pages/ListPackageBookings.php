<?php

namespace App\Filament\TravelAdmin\Resources\PackageBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageBookings extends ListRecords
{
    protected static string $resource = PackageBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
