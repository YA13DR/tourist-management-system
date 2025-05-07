<?php

namespace App\Filament\TravelAdmin\Resources;

use App\Filament\TravelAdmin\Resources\TravelAgencyResource\Pages;
use App\Filament\TravelAdmin\Resources\TravelAgencyResource\RelationManagers;
use App\Models\Admin;
use App\Models\TravelAgency;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelAgencyResource extends Resource
{
    protected static ?string $model = TravelAgency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Travel Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && (Filament::auth()->user()->role === 'super_admin' 
         
         || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'travel'
         ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()   
        && (Filament::auth()->user()->role === 'super_admin'  
        || Filament::auth()->user()->UserType === 'Admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'travel'
         ));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'name') 
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('averageRating')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('totalRatings')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('logoURL')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('website')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('phone')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('email')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('isActive')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('AgencyName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('LocationID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('AverageRating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('TotalRatings')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('LogoURL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Website')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('IsActive')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ManagerID')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTravelAgencies::route('/'),
            'create' => Pages\CreateTravelAgency::route('/create'),
            'view' => Pages\ViewTravelAgency::route('/{record}'),
            'edit' => Pages\EditTravelAgency::route('/{record}/edit'),
        ];
    }
}
