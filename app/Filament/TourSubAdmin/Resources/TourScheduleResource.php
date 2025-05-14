<?php

namespace App\Filament\TourSubAdmin\Resources;

use App\Filament\TourSubAdmin\Resources\TourScheduleResource\Pages;
use App\Filament\TourSubAdmin\Resources\TourScheduleResource\RelationManagers;
use App\Models\Tour;
use App\Models\TourSchedule;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TourScheduleResource extends Resource
{
    protected static ?string $model = TourSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Tour Managment';

    protected static ?int $navigationSort = 4;
    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (!$user) {
            return false;
        }
    
        if ($user->role === 'super_admin' || ($user->role === 'admin' && $user->section === 'tour')) {
      
            $tour = Tour::where('admin_id', $user->id)->first();
            
            return $tour !== null;
        }
    
        return false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        $user = Filament::auth()->user();

        if (!$user) {
            return false;
        }
    
        if ($user->role === 'super_admin' || ($user->role === 'admin' && $user->section === 'tour')) {
      
            $tour = Tour::where('admin_id', $user->id)->first();
            
            return $tour !== null;
        }
    
        return false;
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
            
                Forms\Components\DatePicker::make('startDate')->required(),
                Forms\Components\DatePicker::make('endDate'),
                Forms\Components\TimePicker::make('startTime'),
                Forms\Components\TextInput::make('availableSpots')->required()->numeric(),
                Forms\Components\TextInput::make('price')->numeric(),
                Forms\Components\Toggle::make('isActive')->required(),
            
                Forms\Components\CheckboxList::make('activities')
                    ->relationship('activities', 'name')
                    ->label('Available Activities'),
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tour.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('startDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('endDate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('startTime'),
                Tables\Columns\TextColumn::make('activities.name')
                ->label('Activities')
                ->formatStateUsing(fn ($state, $record) =>
                    $record->activities->pluck('name')->join(', ')
                ),
                Tables\Columns\TextColumn::make('availableSpots')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('isActive')
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
            'index' => Pages\ListTourSchedules::route('/'),
            'create' => Pages\CreateTourSchedule::route('/create'),
            'view' => Pages\ViewTourSchedule::route('/{record}'),
            'edit' => Pages\EditTourSchedule::route('/{record}/edit'),
        ];
    }
}
