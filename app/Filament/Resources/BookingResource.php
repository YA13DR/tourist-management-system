<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_reference')
                ->label('Booking Ref')
                ->searchable()
                ->copyable(),

            TextColumn::make('user.first_name')
                ->label('User')
                ->searchable()
                ->sortable(),

            TextColumn::make('booking_type')
                ->label('Type')
                ->formatStateUsing(fn ($state) => match($state) {
                    1 => 'Tour',
                    2 => 'Hotel',
                    3 => 'Taxi',
                    4 => 'Restaurant',
                    5 => 'Package',
                    default => 'Unknown',
                }),

            TextColumn::make('booking_date')
                ->label('Date')
                ->dateTime()
                ->sortable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => match($state) {
                    1 => 'warning',
                    2 => 'success',
                    3 => 'danger',
                    4 => 'gray',
                })
                ->formatStateUsing(fn ($state) => match($state) {
                    1 => 'Pending',
                    2 => 'Confirmed',
                    3 => 'Cancelled',
                    4 => 'Completed',
                }),

            TextColumn::make('total_price')
                ->label('Total')
                ->money('USD'),

            TextColumn::make('discount_amount')
                ->label('Discount')
                ->money('USD'),

            TextColumn::make('payment_status')
                ->label('Payment')
                ->badge()
                ->color(fn ($state) => match($state) {
                    1 => 'warning',
                    2 => 'success',
                    3 => 'gray',
                    4 => 'danger',
                })
                ->formatStateUsing(fn ($state) => match($state) {
                    1 => 'Pending',
                    2 => 'Paid',
                    3 => 'Refunded',
                    4 => 'Failed',
                }),
            ])
            ->filters([
                SelectFilter::make('booking_type')
                ->label('Booking Type')
                ->options([
                    1 => 'Tour',
                    2 => 'Hotel',
                    3 => 'Taxi',
                    4 => 'Restaurant',
                    5 => 'Package',
                ])
                ->attribute('booking_type'),
                SelectFilter::make('payment_status')
                ->label('Payment Status')
                ->options([
                    1 => 'Pending',
                    2 => 'Paid',
                    3 => 'Refunded',
                    4 => 'Failed',
                ])
                ->attribute('payment_status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('booking_date', 'desc');
    }
    public static function canCreate(): bool
    {
        return false; 
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
