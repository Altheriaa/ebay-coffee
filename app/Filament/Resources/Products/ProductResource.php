<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

// new imported
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
// ----

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    // new imported
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Stok';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';
    // ----

    protected static ?string $recordTitleAttribute = 'nama_product';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori Produk')
                    ->relationship('category', 'nama_kategori')
                    ->required(),
                TextInput::make('nama_product')
                    ->unique()
                    ->required(),
                Textarea::make('deskripsi')
                    ->required(),
                TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('satuan')
                    ->options([
                        'Kg' => 'Kg',
                        'Pcs' => 'Pcs',
                        'Gram' => 'Gram',
                    ])
                    ->required(),
                TextInput::make('weight')
                    ->label('Banyaknya (Gram/Pcs/Kg)')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('foto_product')
                    ->label('Gambar Produk (Format .jpg .png, .jpeg)')
                    ->required()
                    ->directory('uploads/products')
                    ->disk('public')
                    ->columnSpan('full')
                    ->imagePreviewHeight('250')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->rules(['mimes:jpeg,jpg,png']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_product')
            ->columns([
                TextColumn::make('category.nama_kategori')
                    ->label('Kategori Produk')
                    ->sortable(),
                TextColumn::make('nama_product')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->prefix('Rp. ')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->sortable(),
                TextColumn::make('weight')
                    ->label('Banyaknya')
                    ->sortable(),
                ImageColumn::make('foto_product')
                    ->height('200px')
                    ->disk('public'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
        ];
    }
}
