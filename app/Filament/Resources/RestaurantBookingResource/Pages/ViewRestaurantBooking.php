<?php

namespace App\Filament\Resources\RestaurantBookingResource\Pages;

use App\Filament\Resources\RestaurantBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurantBooking extends ViewRecord
{
    protected static string $resource = RestaurantBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
