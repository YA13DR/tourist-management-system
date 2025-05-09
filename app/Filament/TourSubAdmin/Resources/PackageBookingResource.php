<?php

namespace App\Filament\TourSubAdmin\Resources;

use App\Filament\TourSubAdmin\Resources\PackageBookingResource\Pages;
use App\Filament\TourSubAdmin\Resources\PackageBookingResource\RelationManagers;
use App\Models\PackageBooking;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageBookingResource extends Resource
{
    protected static ?string $model = PackageBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Booking Managment';

    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'tour'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'tour';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('tour', function ($query) {
                $query->where('admin_id', auth()->id());
            });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Hidden::make('tour_id')
                ->default(fn () => \App\Models\Tour::where('admin_id', auth()->id())->value('id')),
                

                Forms\Components\Select::make('package_id')
                ->relationship('package', 'name')
                ->searchable()
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    $package = \App\Models\TravelPackage::find($state);
                    if ($package) {
                        $set('agency_id', $package->agency_id);
                        $set('cost', $package->basePrice); 
                    }
                }),

                Forms\Components\Hidden::make('agency_id'),

                Forms\Components\TextInput::make('numberOfAdults')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->required(),

                Forms\Components\TextInput::make('numberOfChildren')
                ->numeric()
                ->default(0)
                ->required(),

                Forms\Components\TextInput::make('cost')
                ->numeric()
                ->prefix('USD')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('startDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numberOfAdults')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numberOfChildren')
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
            'index' => Pages\ListPackageBookings::route('/'),
            'create' => Pages\CreatePackageBooking::route('/create'),
            'view' => Pages\ViewPackageBooking::route('/{record}'),
            'edit' => Pages\EditPackageBooking::route('/{record}/edit'),
        ];
    }
}
