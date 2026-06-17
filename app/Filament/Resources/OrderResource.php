<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Pesanan')->schema([
                Forms\Components\TextInput::make('order_number')
                    ->label('No. Pesanan')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Diproses'   => 'Diproses',
                        'Dikirim'    => 'Dikirim',
                        'Selesai'    => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('user.name')
                    ->label('Pelanggan')
                    ->disabled(),
                Forms\Components\TextInput::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->disabled(),
                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\TextInput::make('shipping_cost')
                    ->label('Ongkos Kirim')
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\TextInput::make('discount')
                    ->label('Diskon')
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->prefix('Rp')
                    ->disabled(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order_number')
                ->label('No. Pesanan')
                ->searchable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('Pelanggan')
                ->searchable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'Diproses',
                    'primary' => 'Dikirim',
                    'success' => 'Selesai',
                    'danger'  => 'Dibatalkan',
                ]),
            Tables\Columns\TextColumn::make('total')
                ->label('Total')
                ->money('IDR')
                ->sortable(),
            Tables\Columns\TextColumn::make('payment_method')
                ->label('Pembayaran'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal')
                ->dateTime('d M Y')
                ->sortable(),
        ])
        ->defaultSort('created_at', 'desc')
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}