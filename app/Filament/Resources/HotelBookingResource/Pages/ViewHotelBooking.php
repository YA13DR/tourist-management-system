<?php

namespace App\Filament\Resources\HotelBookingResource\Pages;

use App\Filament\Resources\HotelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotelBooking extends ViewRecord
{
    protected static string $resource = HotelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
