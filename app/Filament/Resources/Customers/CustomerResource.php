<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\ManageCustomers;
use App\Models\Customer;
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

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

     // new imported
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Users';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';
    // ----

    protected static ?string $recordTitleAttribute = 'nama';

    /**
     * Owner tidak bisa menambah pelanggan.
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa mengedit pelanggan.
     */
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa menghapus pelanggan.
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
                TextInput::make('nama')
                    ->required(),
                TextInput::make('no_hp')
                    ->required(),
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('user_id')
                    ->label('User ID')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->searchable(),
                TextColumn::make('alamat')
                    ->label('alamat')
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
            'index' => ManageCustomers::route('/'),
        ];
    }
}
