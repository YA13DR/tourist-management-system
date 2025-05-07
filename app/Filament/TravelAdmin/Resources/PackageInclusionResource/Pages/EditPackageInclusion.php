<?php

namespace App\Filament\TravelAdmin\Resources\PackageInclusionResource\Pages;

use App\Filament\TravelAdmin\Resources\PackageInclusionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageInclusion extends EditRecord
{
    protected static string $resource = PackageInclusionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
