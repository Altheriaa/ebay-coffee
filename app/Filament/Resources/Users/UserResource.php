<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    // new imported
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $pluralModelLabel = 'Users';
    // ----

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Owner tidak bisa menambah user.
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa mengedit user.
     */
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && $user->role !== 'owner';
    }

    /**
     * Owner tidak bisa menghapus user.
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
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'pelanggan' => 'Pelanggan', 'owner' => 'Owner'])
                    ->default('pelanggan')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('role')
                    ->badge(),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}
