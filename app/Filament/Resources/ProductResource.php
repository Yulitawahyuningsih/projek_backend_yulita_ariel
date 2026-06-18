<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Produk')->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('discount_price')
                    ->label('Harga Diskon')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('stock')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Gambar Produk')->schema([
                Forms\Components\Repeater::make('images')
                    ->relationship('images')
                    ->schema([
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->required(),
                        Forms\Components\Toggle::make('is_primary')
                            ->label('Gambar Utama')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->label(''),
            ]),

            Forms\Components\Section::make('Varian Produk')->schema([
                Forms\Components\Repeater::make('variants')
                    ->relationship('variants')
                    ->schema([
                        Forms\Components\Select::make('size')
                            ->label('Ukuran')
                            ->options(['S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL'])
                            ->required(),
                        Forms\Components\TextInput::make('color')
                            ->label('Warna')
                            ->required(),
                        Forms\Components\ColorPicker::make('color_hex')
                            ->label('Kode Warna'),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok Varian')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(4)
                    ->label(''),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('images.image_url')
                ->label('Gambar')
                ->disk('public')
                ->circular(false)
                ->size(60),
            Tables\Columns\TextColumn::make('name')
                ->label('Nama Produk')
                ->searchable(),
            Tables\Columns\TextColumn::make('category.name')
                ->label('Kategori')
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->label('Harga')
                ->money('IDR')
                ->sortable(),
            Tables\Columns\TextColumn::make('discount_price')
                ->label('Harga Diskon')
                ->money('IDR')
                ->sortable(),
            Tables\Columns\TextColumn::make('stock')
                ->label('Stok')
                ->numeric()
                ->sortable(),
            Tables\Columns\IconColumn::make('is_active')
                ->label('Aktif')
                ->boolean(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}