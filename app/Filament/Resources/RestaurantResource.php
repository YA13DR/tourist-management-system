<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Filament\Resources\RestaurantResource\RelationManagers;
use App\Models\Admin;
use App\Models\Restaurant;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Restaurant Managment';
    
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && (Filament::auth()->user()->role === 'super_admin' 
         
         || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'restaurant'
         ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()   
        && (Filament::auth()->user()->role === 'super_admin'  
        || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'restaurant'
         ));
    }
    public static function mutateFormDataBeforeUpdate(array $data): array
    {
        $location = \App\Models\Location::create([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'city' => $data['city'],
            'region' => $data['region'],
            'country' => $data['country'],
        ]);

        $data['location_id'] = $location->LocationID;
        unset($data['latitude'], $data['longitude'], $data['city'], $data['region'], $data['country']);

        return $data;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('INFO')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        FileUpload::make('mainImageURL')
                        ->label('restaurant Image')
                        ->image()
                        ->directory('restaurant_images') 
                        ->visibility('public' && 'storage'),
                        // ->required(),
                ])->columns(2),
                Forms\Components\Section::make('Location')
                // ->relationship('location') 
                ->schema([
                        
                        Forms\Components\TextInput::make('latitude')
                        ->label('Latitude')
                        ->required()
                        ->readonly(),
                    
                    Forms\Components\TextInput::make('longitude')
                        ->label('Longitude')
                        ->required()
                        ->readonly(),
                        Forms\Components\TextInput::make('city')
                        ->label('City'),
                    Forms\Components\TextInput::make('country')
                        ->label('Country'),
                    Forms\Components\TextInput::make('region')
                        ->label('Region'),
                    Forms\Components\Placeholder::make('Map')
                        ->content(function () {
                            return view('map');
                        })->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Section::make('Time')
                ->schema([
                    Forms\Components\TimePicker::make('openingTime')
                        ->native(false)
                        ->required(),
                    Forms\Components\TimePicker::make('closingTime')
                        ->native(false)
                        ->required(),
                ])->columns(2),
                

                Forms\Components\Section::make('Rating')
                    ->schema([
                    Forms\Components\TextInput::make('averageRating')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('totalRatings')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])->columns(2),

                Forms\Components\Section::make('Rating')
                    ->schema([  
                    Forms\Components\TextInput::make('priceRange')
                    ->numeric()
                    ->default(null),
                ])->columns(1),
                
                
                Forms\Components\Section::make('Comunication')
                    ->schema([
                    Forms\Components\TextInput::make('website')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\TextInput::make('phone')
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\TextInput::make('email')
                        ->maxLength(255)
                        ->default(null),
                ])->columns(3),
                Forms\Components\Section::make('Others')
                ->schema([
                    Forms\Components\Toggle::make('isActive')
                        ->required(),
                    Forms\Components\Toggle::make('isFeatured')
                        ->required(),
                ])->columns(3),
                
                Forms\Components\Section::make('Users')
                ->schema([
                Forms\Components\TextInput::make('cuisine')
                    ->maxLength(255)
                    ->default(null),
                    Forms\Components\Select::make('admin_id')
                    ->label('Manager')
                    ->options(function () {
                        $section = auth()->user()?->section;
                
                        return Admin::where('role', 'sub_admin')
                            ->where('section', $section)
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required(),
                ])->columns(1),   
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuisine')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('priceRange')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('openingTime')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('closingTime')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('averageRating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('totalRatings')
                    ->numeric()
                    ->sortable(),
                    ImageColumn::make('mainImageURL')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset(asset('images/'.$record->MainImageURL) )) 
                    ->width(50),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('hasReservation')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isActive')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isFeatured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
               
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'view' => Pages\ViewRestaurant::route('/{record}'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
