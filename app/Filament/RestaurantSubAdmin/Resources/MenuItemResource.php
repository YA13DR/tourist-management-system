<?php

namespace App\Filament\RestaurantSubAdmin\Resources;

use App\Filament\RestaurantSubAdmin\Resources\MenuItemResource\Pages;
use App\Filament\RestaurantSubAdmin\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Restaurant Managment';

    protected static ?int $navigationSort = 6;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('category.restaurant', function ($query) {
            $query->where('admin_id', auth()->id());
        });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                ->label('Category')
                ->options(function () {
                    $restaurantId = \App\Models\Restaurant::where('admin_id', auth()->id())->value('id');
                    return \App\Models\MenuCategory::where('restaurant_id', $restaurantId)
                        ->pluck('name', 'id');
                })
                ->required(), Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Toggle::make('isVegetarian')
                    ->required(),
                Forms\Components\Toggle::make('isVegan')
                    ->required(),
                Forms\Components\Toggle::make('isGlutenFree')
                    ->required(),
                Forms\Components\TextInput::make('spiciness')
                    ->numeric()
                    ->default(null),
                    FileUpload::make('imageURL')
                    ->label('Restaurant Image')
                    ->image()
                    ->directory('restaurant_images') 
                    ->visibility('public'),
                    // ->required(),
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
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('imageURL')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => asset('images/'.$record->imageURL) ) 
                    ->height(50)
                    ->width(50),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('isVegetarian')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isVegan')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isGlutenFree')
                    ->boolean(),
                Tables\Columns\TextColumn::make('spiciness')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('isActive')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isFeatured')
                    ->boolean(),
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'view' => Pages\ViewMenuItem::route('/{record}'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
