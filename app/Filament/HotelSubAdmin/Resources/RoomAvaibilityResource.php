<?php

namespace App\Filament\HotelSubAdmin\Resources;

use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\Pages;
use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\RelationManagers;
use App\Models\RoomAvaibility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomAvaibilityResource extends Resource
{
    protected static ?string $model = RoomAvaibility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Room Managment';
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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListRoomAvaibilities::route('/'),
            'create' => Pages\CreateRoomAvaibility::route('/create'),
            'view' => Pages\ViewRoomAvaibility::route('/{record}'),
            'edit' => Pages\EditRoomAvaibility::route('/{record}/edit'),
        ];
    }
}
