<?php

namespace App\Filament\TourSubAdmin\Resources;

use App\Filament\TourSubAdmin\Resources\PackageBookingResource\Pages;
use App\Filament\TourSubAdmin\Resources\PackageBookingResource\RelationManagers;
use App\Models\PackageBooking;
use App\Models\Promotion;
use App\Models\TravelPackage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
    private static function calculateBaseCost($packageId, $numberOfAdults, $numberOfChildren)
{
    $package = TravelPackage::find($packageId);
    if (!$package) return 0;

    return $package->basePrice * ($numberOfAdults + $numberOfChildren);
}

private static function applyPromotion($cost, $promotionCode): float
{
    $promotion = Promotion::where('promotion_code', $promotionCode)
        ->where('isActive', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->where(function ($q) {
            $q->where('applicable_type', 1)
              ->orWhere('applicable_type', 3);
        })
        ->first();

    if ($promotion) {
        return $cost - ($cost * $promotion->discount_value / 100);
    }

    return -1; 
}

private static function calculateFinalCost($packageId, $numberOfAdults, $numberOfChildren, $promotionCode = null)
{
    $baseCost = self::calculateBaseCost($packageId, $numberOfAdults, $numberOfChildren);

    if ($promotionCode) {
        $final = self::applyPromotion($baseCost, $promotionCode);
        return $final !== -1 ? $final : $baseCost;
    }

    return $baseCost;
}

public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Hidden::make('tour_id')
            ->default(fn () => \App\Models\Tour::where('admin_id', auth()->id())->value('id'))
            ->dehydrated(),

        Forms\Components\Select::make('package_id')
            ->relationship('package', 'name')
            ->required()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $adults = (int) $get('numberOfAdults') ?? 1;
                $children = (int) $get('numberOfChildren') ?? 0;
                $promotionCode = $get('promotion_code');
                $set('cost', self::calculateFinalCost($state, $adults, $children, $promotionCode));

                $package = TravelPackage::find($state);
                if ($package) {
                    $set('agency_id', $package->agency_id);
                }
            }),

        Forms\Components\Hidden::make('agency_id'),

        Forms\Components\TextInput::make('numberOfAdults')
            ->numeric()
            ->default(1)
            ->minValue(1)
            ->live()
            ->required()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $packageId = $get('package_id');
                $children = (int) $get('numberOfChildren') ?? 0;
                $promotionCode = $get('promotion_code');
                $set('cost', self::calculateFinalCost($packageId, $state, $children, $promotionCode));
            }),

        Forms\Components\TextInput::make('numberOfChildren')
            ->numeric()
            ->default(0)
            ->live()
            ->required()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $packageId = $get('package_id');
                $adults = (int) $get('numberOfAdults') ?? 1;
                $promotionCode = $get('promotion_code');
                $set('cost', self::calculateFinalCost($packageId, $adults, $state, $promotionCode));
            }),

        Forms\Components\TextInput::make('promotion_code')
            ->label('Promotion Code')
            ->nullable()
            ->live()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $packageId = $get('package_id');
                $adults = (int) $get('numberOfAdults') ?? 1;
                $children = (int) $get('numberOfChildren') ?? 0;

                $costBefore = self::calculateBaseCost($packageId, $adults, $children);
                $finalCost = self::applyPromotion($costBefore, $state);

                if ($finalCost === -1) {
                    Notification::make()
                        ->title('Invalid or expired promotion code.')
                        ->danger()
                        ->send();

                    $set('cost', $costBefore);
                } else {
                    $set('cost', $finalCost);
                }
            }),

        Forms\Components\TextInput::make('cost')
            ->numeric()
            ->prefix('USD')
            ->required()
            ->disabled()
            ->dehydrated(),
    ])
    ->disabled(fn (callable $get) => $get('cost') <= 0); 
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
                Tables\Columns\TextColumn::make('cost')
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
