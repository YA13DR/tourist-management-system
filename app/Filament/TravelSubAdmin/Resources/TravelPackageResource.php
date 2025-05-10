<?php

namespace App\Filament\TravelSubAdmin\Resources;

use App\Filament\TravelSubAdmin\Resources\TravelPackageResource\Pages;
use App\Filament\TravelSubAdmin\Resources\TravelPackageResource\RelationManagers;
use App\Models\TravelAgency;
use App\Models\TravelPackage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TravelPackageResource extends Resource
{
    protected static ?string $model = TravelPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Travel Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'travel'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'travel';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('agency', function ($query) {
                $query->where('admin_id', auth()->id());
            });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\Hidden::make('agency_id')
                ->default(fn () => TravelAgency::where('admin_id', auth()->id())->value('id')),
                
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('durationDays')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('basePrice')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discountPercentage')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('maxParticipants')
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
                Forms\Components\TextInput::make('mainImageURL')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Toggle::make('isActive')
                    ->required(),
                Forms\Components\Toggle::make('isFeatured')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('durationDays')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basePrice')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discountPercentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maxParticipants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('averageRating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totalRatings')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mainImageURL')
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
            'index' => Pages\ListTravelPackages::route('/'),
            'create' => Pages\CreateTravelPackage::route('/create'),
            'view' => Pages\ViewTravelPackage::route('/{record}'),
            'edit' => Pages\EditTravelPackage::route('/{record}/edit'),
        ];
    }
}
