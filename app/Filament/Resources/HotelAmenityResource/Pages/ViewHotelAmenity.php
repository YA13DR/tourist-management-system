<?php

namespace App\Filament\Resources\HotelAmenityResource\Pages;

use App\Filament\Resources\HotelAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotelAmenity extends ViewRecord
{
    protected static string $resource = HotelAmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
