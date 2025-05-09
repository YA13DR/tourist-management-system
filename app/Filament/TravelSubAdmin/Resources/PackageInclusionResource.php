<?php

namespace App\Filament\TravelSubAdmin\Resources;

use App\Filament\TravelSubAdmin\Resources\PackageInclusionResource\Pages;
use App\Filament\TravelSubAdmin\Resources\PackageInclusionResource\RelationManagers;
use App\Models\PackageInclusion;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageInclusionResource extends Resource
{
    protected static ?string $model = PackageInclusion::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
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
            ->whereHas('package.tour', function ($query) {
                $query->where('admin_id', auth()->id());
            });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('package_id')
                ->default(fn () => \App\Models\Tour::where('admin_id', auth()->id())->value('id')),
                
                Forms\Components\Select::make('inclusionType')
                    ->label('Inclusion Type')
                    ->options([
                        1 => 'Tour',
                        2 => 'Hotel',
                        3 => 'Transport',
                        4 => 'Meal',
                        5 => 'Other',
                    ])
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('isHighlighted')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('package.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inclusionType')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\IconColumn::make('isHighlighted')
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
            'index' => Pages\ListPackageInclusions::route('/'),
            'create' => Pages\CreatePackageInclusion::route('/create'),
            'view' => Pages\ViewPackageInclusion::route('/{record}'),
            'edit' => Pages\EditPackageInclusion::route('/{record}/edit'),
        ];
    }
}
