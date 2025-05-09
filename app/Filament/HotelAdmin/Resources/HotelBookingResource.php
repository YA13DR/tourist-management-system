<?php

namespace App\Filament\HotelAdmin\Resources;

use App\Filament\HotelAdmin\Resources\HotelBookingResource\Pages;
use App\Filament\Resources\HotelBookingResource\RelationManagers;
use App\Models\HotelBooking;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelBookingResource extends Resource
{
    protected static ?string $model = HotelBooking::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Booking Managment';

    protected static ?int $navigationSort = 3;
    public static function canAccess(): bool
    {
        return Filament::auth()->check() 
         && Filament::auth()->user()->role === 'sub_admin' 
         && Filament::auth()->user()->section === 'hotel'
         ;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->check()  
        && Filament::auth()->user()->role === 'sub_admin' 
            && Filament::auth()->user()->section === 'hotel';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('hotel', function ($query) {
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
                Forms\Components\TextInput::make('HotelID')
                    ->tel()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('RoomTypeID')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('CheckInDate')
                    ->required(),
                Forms\Components\DatePicker::make('CheckOutDate')
                    ->required(),
                Forms\Components\TextInput::make('NumberOfRooms')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('NumberOfGuests')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotelRoom')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roomType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkInDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkOutDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numberOfRooms')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numberOfGuests')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListHotelBookings::route('/'),
            // 'create' => Pages\CreateHotelBooking::route('/create'),
            'view' => Pages\ViewHotelBooking::route('/{record}'),
            'edit' => Pages\EditHotelBooking::route('/{record}/edit'),
        ];
    }
}
