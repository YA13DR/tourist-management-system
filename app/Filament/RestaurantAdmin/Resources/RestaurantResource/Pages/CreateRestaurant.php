<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantResource;
use App\Models\Location;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;

    // protected function saved(): void
    // {
    //     parent::saved();

    //     // $data = $this->data;
        
    //     // if (isset($data['latitude']) && isset($data['longitude'])) {
    //     //     $location = Location::create([
    //     //         'LocationName' => 'Restaurant ' . $data['RestaurantName'],
    //     //         'latitude' => $data['latitude'],
    //     //         'longitude' => $data['longitude'],
    //     //     ]);
            
    //     //     $this->record->location_id = $location->id;
    //     //     $this->record->save();
    //     // }
    // }
    
}
