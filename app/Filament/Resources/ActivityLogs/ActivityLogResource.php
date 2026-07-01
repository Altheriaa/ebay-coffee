<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ManageActivityLogs;
use App\Models\ActivityLog;
use BackedEnum;
use UnitEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string | UnitEnum | null $navigationGroup = 'Monitoring';

    protected static ?string $navigationLabel = 'Log Aktivitas Admin';

    protected static ?string $pluralModelLabel = 'Log Aktivitas Admin';

    protected static ?string $recordTitleAttribute = 'description';

    /**
     * Hanya owner yang bisa melihat resource ini.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'owner';
    }

    /**
     * Owner tidak bisa membuat log baru.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Aktivitas')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Waktu')
                        ->dateTime('d M Y H:i:s'),
                    TextEntry::make('user.name')
                        ->label('Admin'),
                    TextEntry::make('action')
                        ->label('Jenis Aksi')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            default => 'secondary',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'created' => 'Ditambahkan',
                            'updated' => 'Diubah',
                            'deleted' => 'Dihapus',
                            default => $state,
                        }),
                    TextEntry::make('model_type')
                        ->label('Tipe Data')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Product'  => 'primary',
                            'Category' => 'info',
                            'Customer' => 'warning',
                            'Order'    => 'success',
                            'User'     => 'danger',
                            default    => 'secondary',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'Product'  => 'Produk',
                            'Category' => 'Kategori',
                            'Customer' => 'Pelanggan',
                            'Order'    => 'Pesanan',
                            'User'     => 'User',
                            default    => $state,
                        }),
                    TextEntry::make('model_id')
                        ->label('ID Record'),
                    TextEntry::make('ip_address')
                        ->label('IP Address')
                        ->default('-'),
                    TextEntry::make('description')
                        ->label('Deskripsi Lengkap')
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Admin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Ditambahkan',
                        'updated' => 'Diubah',
                        'deleted' => 'Dihapus',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('model_type')
                    ->label('Tipe Data')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Product'  => 'primary',
                        'Category' => 'info',
                        'Customer' => 'warning',
                        'Order'    => 'success',
                        'User'     => 'danger',
                        default    => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable()
                    ->limit(80),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Jenis Aksi')
                    ->options([
                        'created' => 'Ditambahkan',
                        'updated' => 'Diubah',
                        'deleted' => 'Dihapus',
                    ]),
                SelectFilter::make('model_type')
                    ->label('Tipe Data')
                    ->options([
                        'Product'  => 'Produk',
                        'Category' => 'Kategori',
                        'Customer' => 'Pelanggan',
                        'Order'    => 'Pesanan',
                        'User'     => 'User',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Admin')
                    ->relationship('user', 'name', fn (Builder $query) => $query->where('role', 'admin')),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-magnifying-glass'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActivityLogs::route('/'),
        ];
    }
}
