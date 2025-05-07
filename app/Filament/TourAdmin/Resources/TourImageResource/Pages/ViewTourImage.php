<?php

namespace App\Filament\TourAdmin\Resources\TourImageResource\Pages;

use App\Filament\TourAdmin\Resources\TourImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourImage extends ViewRecord
{
    protected static string $resource = TourImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
