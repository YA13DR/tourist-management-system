<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Filament\Resources\HotelResource\RelationManagers;
use App\Models\Admin;
use App\Models\Hotel;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Hotel Managment';

    protected static ?int $navigationSort = 1;
    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
        && (Filament::auth()->user()->role === 'super_admin' 
         
         || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'hotel'
         ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()   
        && (Filament::auth()->user()->role === 'super_admin'  
        || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'hotel'
         ));
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
                        FileUpload::make('mainImageURL')
                                    ->label('Hotel Image')
                                    ->image()
                                    ->directory('hotel_images') 
                                    ->visibility('public' && 'storage')
                                    ->required(),
                        Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),            
                    ])->columns(3),
                Forms\Components\Section::make('INFO')
                    ->schema([
                    
                    Forms\Components\TextInput::make('starRating')
                        ->numeric()
                        ->default(null),
                    Forms\Components\TextInput::make('averageRating')
                        ->required()
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('totalRatings')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TimePicker::make('checkInTime'),
                    Forms\Components\TimePicker::make('checkOutTime'),    
                    ])->columns(3),
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
                Forms\Components\Section::make('INFO')
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
                    Forms\Components\Section::make('INFO')
                    ->schema([
                Forms\Components\Toggle::make('isActive')
                    ->required(),
                Forms\Components\Toggle::make('isFeatured')
                    ->required(),
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
                    ->required()
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('mainImageURL')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset(asset('images/'.$record->mainImageURL) )) 
                    ->width(50),  
                Tables\Columns\TextColumn::make('admin.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkInTime'),
                Tables\Columns\TextColumn::make('checkOutTime'),
                Tables\Columns\TextColumn::make('starRating')
                        ->numeric()
                        ->sortable(),
                Tables\Columns\TextColumn::make('averageRating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totalRatings')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('isActive')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isFeatured')
                    ->boolean(),
                
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'view' => Pages\ViewHotel::route('/{record}'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
