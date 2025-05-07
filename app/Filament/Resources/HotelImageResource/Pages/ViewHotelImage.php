<?php

namespace App\Filament\Resources\HotelImageResource\Pages;

use App\Filament\Resources\HotelImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotelImage extends ViewRecord
{
    protected static string $resource = HotelImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
