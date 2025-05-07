<?php

namespace App\Filament\TourAdmin\Resources\TourTranslationResource\Pages;

use App\Filament\TourAdmin\Resources\TourTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTourTranslation extends EditRecord
{
    protected static string $resource = TourTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
