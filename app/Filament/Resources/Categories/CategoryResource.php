<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Stok';

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static ?string $pluralModelLabel = 'Kategori Produk';

    protected static ?string $recordTitleAttribute = 'nama_category';

    /**
     * Owner tidak bisa menambah kategori.
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa mengedit kategori.
     */
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa menghapus kategori.
     */
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa bulk delete.
     */
    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kategori')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_category')
            ->columns([
                TextColumn::make('nama_kategori')
                    ->searchable(),
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
                EditAction::make()
                    ->visible(fn () => auth()->user()?->role !== 'owner'),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->role !== 'owner'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => auth()->user()?->role !== 'owner'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
