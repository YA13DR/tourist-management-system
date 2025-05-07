<?php

namespace App\Filament\Resources\RestaurantTableResource\Pages;

use App\Filament\Resources\RestaurantTableResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurantTable extends ViewRecord
{
    protected static string $resource = RestaurantTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
