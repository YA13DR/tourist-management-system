<?php

namespace App\Filament\TravelAdmin\Resources;

use App\Filament\TravelAdmin\Resources\PackageBookingResource\Pages;
use App\Filament\TravelAdmin\Resources\PackageBookingResource\RelationManagers;
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
            ->whereHas('package.agency', function ($query) {
                $query->where('admin_id', auth()->id());
            });
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('BookingID')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('PackageID')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('StartDate')
                    ->required(),
                Forms\Components\TextInput::make('NumberOfAdults')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('NumberOfChildren')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('BookingID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PackageID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('StartDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('NumberOfAdults')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('NumberOfChildren')
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
