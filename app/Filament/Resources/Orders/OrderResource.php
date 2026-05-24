<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Transaksi';

    protected static ?string $navigationLabel = 'Pesanan / Transaksi';

    protected static ?string $pluralModelLabel = 'Pesanan / Transaksi';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'processing'])->count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return static::getModel()::whereIn('status', ['pending', 'processing'])->count() > 0 ? 'warning' : 'gray';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->disabled()
                    ->label('No. Invoice'),
                Select::make('customer_id')
                    ->relationship('customer', 'nama')
                    ->disabled()
                    ->label('Pelanggan'),
                TextInput::make('phone')
                    ->required()
                    ->label('No. Telepon / HP'),
                Textarea::make('shipping_address')
                    ->required()
                    ->label('Alamat Pengiriman')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->label('Status Pesanan'),
                Select::make('status_payment')
                    ->options([
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'paid' => 'Paid',
                    ])
                    ->required()
                    ->label('Status Pembayaran'),
                TextInput::make('total_price')
                    ->disabled()
                    ->numeric()
                    ->label('Total Bayar (Rp)')
                    ->prefix('Rp. '),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('total_price')
                    ->label('Total Bayar')
                    ->prefix('Rp. ')
                    ->numeric(0, ',', '.')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status_payment')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'expired' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'shipped' => 'primary',
                        'processing' => 'info',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pesanan')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('status_payment')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('proses')
                    ->label('Proses')
                    ->color('info')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(fn (Order $record) => $record->update(['status' => 'processing'])),
                Action::make('kirim')
                    ->label('Kirim')
                    ->color('primary')
                    ->icon('heroicon-o-truck')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status === 'processing')
                    ->action(fn (Order $record) => $record->update(['status' => 'shipped'])),
                Action::make('selesai')
                    ->label('Selesai')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status === 'shipped')
                    ->action(fn (Order $record) => $record->update(['status' => 'completed'])),
                Action::make('batal')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => !in_array($record->status, ['completed', 'cancelled']))
                    ->action(fn (Order $record) => $record->update(['status' => 'cancelled'])),
                Action::make('lunas')
                    ->label('Tandai Lunas')
                    ->color('success')
                    ->icon('heroicon-o-banknotes')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->status_payment !== 'paid')
                    ->action(fn (Order $record) => $record->update(['status_payment' => 'paid'])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->schema([
                        TextEntry::make('customer.nama')
                            ->label('Nama Pelanggan'),
                        TextEntry::make('phone')
                            ->label('No. Telepon / HP'),
                        TextEntry::make('shipping_address')
                            ->label('Alamat Pengiriman')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Status & Pembayaran')
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('No. Invoice'),
                        TextEntry::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->default('-'),
                        TextEntry::make('status_payment')
                            ->label('Status Pembayaran')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed', 'expired' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('status')
                            ->label('Status Pesanan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'shipped' => 'primary',
                                'processing' => 'info',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('total_price')
                            ->label('Total Bayar')
                            ->prefix('Rp. ')
                            ->numeric(0, ',', '.'),
                    ])
                    ->columns(2),
                Section::make('Item Pesanan')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->label('')
                            ->schema([
                                TextEntry::make('product.nama_product')
                                    ->label('Nama Produk'),
                                TextEntry::make('price')
                                    ->label('Harga Satuan')
                                    ->prefix('Rp. ')
                                    ->numeric(0, ',', '.'),
                                TextEntry::make('qty')
                                    ->label('Jumlah'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->prefix('Rp. ')
                                    ->numeric(0, ',', '.'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrders::route('/'),
        ];
    }
}
