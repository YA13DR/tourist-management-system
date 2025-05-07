<?php

namespace App\Filament\Resources\HotelAmenityResource\Pages;

use App\Filament\Resources\HotelAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelAmenities extends ListRecords
{
    protected static string $resource = HotelAmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
