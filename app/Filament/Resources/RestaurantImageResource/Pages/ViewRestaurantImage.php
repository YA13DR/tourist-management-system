<?php

namespace App\Filament\Resources\RestaurantImageResource\Pages;

use App\Filament\Resources\RestaurantImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurantImage extends ViewRecord
{
    protected static string $resource = RestaurantImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
