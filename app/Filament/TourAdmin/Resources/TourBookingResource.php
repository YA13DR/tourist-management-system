<?php

namespace App\Filament\TourAdmin\Resources;

use App\Filament\TourAdmin\Resources\TourBookingResource\Pages;
use App\Filament\TourAdmin\Resources\TourBookingResource\RelationManagers;
use App\Models\TourBooking;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TourBookingResource extends Resource
{
    protected static ?string $model = TourBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
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
                
                Forms\Components\Hidden::make('schedule_id')
                ->default(fn () => \App\Models\TourSchedule::where('admin_id', auth()->id())->value('id')),
                
                Forms\Components\TextInput::make('numberOfAdults')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('numberOfChildren')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tour.name')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('tour.activities.name')
                    ->label('Activities')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->tour->activities->pluck('name')->join(', ')
                    ),
                Tables\Columns\TextColumn::make('user.firstName')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numberOfAdults')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numberOfChildren')
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
            'index' => Pages\ListTourBookings::route('/'),
            'create' => Pages\CreateTourBooking::route('/create'),
            'view' => Pages\ViewTourBooking::route('/{record}'),
            'edit' => Pages\EditTourBooking::route('/{record}/edit'),
        ];
    }
}
