<x-filament-panels::page>
    @php
        $data = $this->getFinancialData();
    @endphp

    <style>
        .custom-report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .custom-report-th {
            padding: 0.75rem 1.5rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(107, 114, 128, 0.2);
            background-color: rgba(243, 244, 246, 0.5);
        }
        :is(.dark) .custom-report-th {
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff;
        }
        .custom-report-td {
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            border-bottom: 1px solid rgba(107, 114, 128, 0.2);
        }
        :is(.dark) .custom-report-td {
            color: #e5e7eb;
        }
        .custom-stat-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .custom-stat-icon-wrapper {
            padding: 0.75rem;
            border-radius: 0.75rem;
        }
        .custom-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            background-color: #d97706; /* amber-600 */
            border-radius: 0.5rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .custom-btn:hover {
            background-color: #b45309; /* amber-700 */
        }
        .custom-grid-filters {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            align-items: flex-end;
        }
        @media (min-width: 640px) {
            .custom-grid-filters {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 768px) {
            .custom-grid-filters {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        .custom-grid-stats {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .custom-grid-stats {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>

    {{-- Filter Section --}}
    <x-filament::section icon="heroicon-o-funnel" heading="Filter Periode Laporan" description="Pilih rentang waktu untuk menampilkan data laporan keuangan">
        <div class="custom-grid-filters">
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Tipe Filter</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="filterType">
                        <option value="hari">Harian</option>
                        <option value="bulan">Bulanan</option>
                        <option value="tahun">Tahunan</option>
                        <option value="all">Semua Periode</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            @if ($filterType === 'hari')
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Pilih Tanggal</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="selectedDate" />
                    </x-filament::input.wrapper>
                </div>
            @endif

            @if ($filterType === 'bulan')
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Pilih Bulan</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="selectedMonth">
                            @foreach ($months as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            @endif

            @if ($filterType === 'bulan' || $filterType === 'tahun')
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Pilih Tahun</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="selectedYear">
                            @foreach ($years as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            @endif

            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; margin-top: 1rem;">
                <a href="{{ route('admin.laporan-keuangan.print', ['filter_type' => $filterType, 'date' => $selectedDate, 'month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="custom-btn">
                    <x-filament::icon icon="heroicon-o-printer" style="width: 1.25rem; height: 1.25rem;" />
                    Cetak Laporan
                </a>
            </div>
        </div>
    </x-filament::section>

    {{-- Stats Grid --}}
    <div class="custom-grid-stats">
        {{-- Total Transaksi --}}
        <x-filament::section>
            <div class="custom-stat-card">
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Total Transaksi Lunas</p>
                    <p style="font-size: 1.875rem; font-weight: 700; margin-top: 0.25rem;">{{ $data['totalTransaksi'] }}</p>
                </div>
                <div class="custom-stat-icon-wrapper" style="background-color: rgba(59, 130, 246, 0.1);">
                    <x-filament::icon icon="heroicon-o-shopping-cart" style="width: 1.5rem; height: 1.5rem; color: #3b82f6;" />
                </div>
            </div>
        </x-filament::section>

        {{-- Total Pendapatan --}}
        <x-filament::section>
            <div class="custom-stat-card">
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Total Pendapatan</p>
                    <p style="font-size: 1.5rem; font-weight: 700; margin-top: 0.25rem; color: #10b981;">Rp {{ number_format($data['totalPemasukan'], 0, ',', '.') }}</p>
                </div>
                <div class="custom-stat-icon-wrapper" style="background-color: rgba(16, 185, 129, 0.1);">
                    <x-filament::icon icon="heroicon-o-currency-dollar" style="width: 1.5rem; height: 1.5rem; color: #10b981;" />
                </div>
            </div>
        </x-filament::section>

        {{-- Rata-rata --}}
        <x-filament::section>
            <div class="custom-stat-card">
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Rata-rata Transaksi</p>
                    <p style="font-size: 1.5rem; font-weight: 700; margin-top: 0.25rem; color: #f59e0b;">Rp {{ $data['totalTransaksi'] > 0 ? number_format($data['totalPemasukan'] / $data['totalTransaksi'], 0, ',', '.') : '0' }}</p>
                </div>
                <div class="custom-stat-icon-wrapper" style="background-color: rgba(245, 158, 11, 0.1);">
                    <x-filament::icon icon="heroicon-o-chart-bar" style="width: 1.5rem; height: 1.5rem; color: #f59e0b;" />
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Detail Table --}}
    <x-filament::section icon="heroicon-o-document-text" heading="Rincian Transaksi Pemasukan" description="Daftar seluruh order lunas pada periode yang dipilih">
        <div style="overflow-x: auto; margin: -1.5rem;">
            <table class="custom-report-table">
                <thead>
                    <tr>
                        <th class="custom-report-th">No. Invoice</th>
                        <th class="custom-report-th">Tanggal</th>
                        <th class="custom-report-th">Pelanggan</th>
                        <th class="custom-report-th">Metode</th>
                        <th class="custom-report-th" style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['orders'] as $order)
                        <tr>
                            <td class="custom-report-td" style="font-family: monospace;">{{ $order->invoice_number }}</td>
                            <td class="custom-report-td">
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span style="font-size: 0.75rem; color: #6b7280;">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td class="custom-report-td" style="font-weight: 500;">{{ $order->customer->nama ?? '-' }}</td>
                            <td class="custom-report-td">
                                <x-filament::badge color="info">
                                    {{ $order->payment_method ?? 'Midtrans' }}
                                </x-filament::badge>
                            </td>
                            <td class="custom-report-td" style="text-align: right; color: #10b981; font-weight: 600;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="custom-report-td" style="text-align: center; padding: 3rem 1rem; color: #6b7280;">
                                Tidak ada data pemasukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($data['totalTransaksi'] > 0)
                    <tfoot>
                        <tr>
                            <td colspan="4" class="custom-report-td" style="text-align: right; font-weight: 700; border-bottom: none;">Total Pendapatan Bersih:</td>
                            <td class="custom-report-td" style="text-align: right; font-size: 1.125rem; font-weight: 700; color: #10b981; border-bottom: none;">
                                Rp {{ number_format($data['totalPemasukan'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
