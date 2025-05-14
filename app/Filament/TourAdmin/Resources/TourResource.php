<?php

namespace App\Filament\TourAdmin\Resources;

use App\Filament\TourAdmin\Resources\TourResource\Pages;
use App\Filament\TourAdmin\Resources\TourResource\RelationManagers;
use App\Models\Admin;
use App\Models\Tour;
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


class TourResource extends Resource
{
    protected static ?string $model = Tour::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Tour Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
        && (Filament::auth()->user()->role === 'super_admin' 
        ||(
           Filament::auth()->user()->role === 'admin' 
           && Filament::auth()->user()->section === 'tour'
        ));
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check() 
         && (Filament::auth()->user()->role === 'super_admin' 
         ||(
            Filament::auth()->user()->role === 'admin' 
            && Filament::auth()->user()->section === 'tour'
         ));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('location_id')
                    ->label('Location')
                    ->relationship('location', 'name') 
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('shortDescription')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('durationHours')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('durationDays')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('basePrice')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discountPercentage')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('maxCapacity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('minParticipants')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('difficultyLevel')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('averageRating')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('totalRatings')
                    ->required()
                    ->numeric()
                    ->default(0),
                    Forms\Components\MultiSelect::make('categories')
                    ->relationship('categories', 'name')
                    ->label('Tour Categories')
                    ->preload(),
                FileUpload::make('mainImageURL')
                ->label('Tour Image')
                ->image()
                ->directory('tour_images') 
                ->visibility('public'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shortDescription')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('durationHours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('durationDays')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basePrice')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discountPercentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('maxCapacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minParticipants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('difficultyLevel')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('averageRating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('totalRatings')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('mainImageURL')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset('images/'.$record->imageURL) ) 
                    ->height(50)
                    ->width(50),
                Tables\Columns\IconColumn::make('isActive')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isFeatured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy')
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
            'index' => Pages\ListTours::route('/'),
            'create' => Pages\CreateTour::route('/create'),
            'view' => Pages\ViewTour::route('/{record}'),
            'edit' => Pages\EditTour::route('/{record}/edit'),
        ];
    }
}
