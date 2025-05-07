<?php

namespace App\Filament\TravelAdmin\Resources\PackageBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageBooking extends EditRecord
{
    protected static string $resource = PackageBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
