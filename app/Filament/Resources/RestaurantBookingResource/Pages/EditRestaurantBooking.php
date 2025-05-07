<?php

namespace App\Filament\Resources\RestaurantBookingResource\Pages;

use App\Filament\Resources\RestaurantBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantBooking extends EditRecord
{
    protected static string $resource = RestaurantBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
