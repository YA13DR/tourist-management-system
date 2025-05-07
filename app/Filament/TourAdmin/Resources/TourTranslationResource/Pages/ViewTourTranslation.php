<?php

namespace App\Filament\TourAdmin\Resources\TourTranslationResource\Pages;

use App\Filament\TourAdmin\Resources\TourTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTourTranslation extends ViewRecord
{
    protected static string $resource = TourTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
