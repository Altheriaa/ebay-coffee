<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  order: Object,
});

const page = usePage();

const formatRupiah = (value) => {
  return 'Rp' + Number(value).toLocaleString('id-ID');
};

const formatDate = (dateStr) => {
  const d = new Date(dateStr);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  return `${day}/${month}/${year}`;
};

const formatDateTime = (dateStr) => {
  const d = new Date(dateStr);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  const hours = String(d.getHours()).padStart(2, '0');
  const minutes = String(d.getMinutes()).padStart(2, '0');
  return `${day}/${month}/${year} ${hours}:${minutes} WIB`;
};

const shippingCost = computed(() => {
  return props.order.total_price - props.order.subtotal;
});

const totalItems = computed(() => {
  return (props.order.order_items || []).reduce((sum, item) => sum + item.qty, 0);
});

const printInvoice = () => {
  window.print();
};
</script>

<template>
  <div class="invoice-page bg-[#f5f0eb] min-h-screen">
    <!-- Print Action Bar (hidden on print) -->
    <div class="no-print sticky top-0 z-50 bg-[#271310] shadow-lg">
      <div class="max-w-[800px] mx-auto px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link href="/transaksi" class="text-[#f0bd8b] hover:text-white transition-colors flex items-center gap-1.5 text-sm font-semibold">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali
          </Link>
          <span class="text-white/30">|</span>
          <span class="text-white/70 text-sm">Invoice {{ order.invoice_number }}</span>
        </div>
        <button @click="printInvoice"
          class="inline-flex items-center gap-2 bg-[#7d562d] hover:bg-[#9a6b38] text-white font-bold px-5 py-2 rounded-xl transition-all duration-200 text-sm shadow-md">
          <span class="material-symbols-outlined text-base">print</span>
          Cetak / Unduh PDF
        </button>
      </div>
    </div>

    <!-- Invoice Content -->
    <div class="max-w-[800px] mx-auto px-4 py-8 print:py-0 print:px-0">
      <div class="bg-white rounded-2xl print:rounded-none shadow-xl print:shadow-none overflow-hidden border border-[#e8ddd4] print:border-0">

        <!-- Header with Logo -->
        <div class="bg-gradient-to-r from-[#271310] to-[#3d2218] px-8 py-6 print:py-5 flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-white tracking-tight" style="font-family:'Playfair Display',serif;">Bay & Coffee</h1>
            <p class="text-white/50 text-xs mt-0.5">Premium Artisan Coffee Roastery</p>
          </div>
          <div class="text-right">
            <p class="text-white/40 text-[10px] uppercase tracking-widest font-bold">Nota Pesanan</p>
            <p class="text-[#f0bd8b] text-sm font-bold mt-0.5">{{ order.invoice_number }}</p>
          </div>
        </div>

        <!-- Buyer & Seller Info -->
        <div class="px-8 py-5 border-b border-[#e8ddd4]">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Buyer -->
            <div class="space-y-2.5">
              <div>
                <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">Nama Pembeli</p>
                <p class="text-sm font-bold text-[#271310]">{{ order.customer?.nama || '-' }}</p>
              </div>
              <div>
                <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">Alamat Pembeli</p>
                <p class="text-xs text-[#271310]/80 leading-relaxed">{{ order.shipping_address || '-' }}</p>
              </div>
              <div>
                <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">No. Handphone Pembeli</p>
                <p class="text-sm font-semibold text-[#271310]">{{ order.phone || '-' }}</p>
              </div>
            </div>

            <!-- Seller / Store Info -->
            <div class="space-y-2.5 sm:text-right">
              <div>
                <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">Penjual</p>
                <p class="text-sm font-bold text-[#271310]">Bay & Coffee Store</p>
              </div>
              <div>
                <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">Email</p>
                <p class="text-xs text-[#271310]/80">support@bayandcoffee.com</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Meta -->
        <div class="px-8 py-4 bg-[#faf6f2] border-b border-[#e8ddd4]">
          <div class="grid grid-cols-3 gap-4">
            <div>
              <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">No. Pesanan</p>
              <p class="text-xs font-bold text-[#271310]">{{ order.invoice_number }}</p>
            </div>
            <div>
              <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">Tanggal Transaksi</p>
              <p class="text-xs font-bold text-[#271310]">{{ formatDateTime(order.created_at) }}</p>
            </div>
            <div>
              <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-1">Metode Pembayaran</p>
              <p class="text-xs font-bold text-[#271310] uppercase">{{ order.payment_method || 'Midtrans' }}</p>
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <div class="px-8 py-5 border-b border-[#e8ddd4]">
          <h3 class="text-xs font-bold uppercase tracking-widest text-[#7d562d] mb-4 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm print:hidden">shopping_bag</span>
            Rincian Pesanan
          </h3>
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b-2 border-[#e8ddd4]">
                <th class="text-left py-2.5 font-bold text-[#271310]/60 uppercase tracking-wider w-8">No.</th>
                <th class="text-left py-2.5 font-bold text-[#271310]/60 uppercase tracking-wider">Produk</th>
                <th class="text-right py-2.5 font-bold text-[#271310]/60 uppercase tracking-wider">Harga Produk</th>
                <th class="text-center py-2.5 font-bold text-[#271310]/60 uppercase tracking-wider w-20">Kuantitas</th>
                <th class="text-right py-2.5 font-bold text-[#271310]/60 uppercase tracking-wider">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in order.order_items" :key="item.id"
                class="border-b border-[#e8ddd4]/60">
                <td class="py-3 text-[#271310]/60 align-top">{{ idx + 1 }}</td>
                <td class="py-3 align-top">
                  <p class="font-bold text-[#271310] leading-snug">{{ item.product?.nama_product || 'Produk' }}</p>
                  <p class="text-[10px] text-[#271310]/50 mt-0.5">{{ item.product?.weight || 0 }} {{ item.product?.satuan || 'gr' }}</p>
                </td>
                <td class="py-3 text-right text-[#271310]/80 align-top">{{ formatRupiah(item.price) }}</td>
                <td class="py-3 text-center text-[#271310]/80 align-top">{{ item.qty }}</td>
                <td class="py-3 text-right font-semibold text-[#271310] align-top">{{ formatRupiah(item.price * item.qty) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Buyer Email -->
        <div class="px-8 py-3 border-b border-[#e8ddd4]">
          <p class="text-[10px] uppercase tracking-widest font-bold text-[#7d562d]/60 mb-0.5">Email Pembeli</p>
          <p class="text-xs text-[#271310]/80">{{ page.props.auth?.user?.email || '-' }}</p>
        </div>

        <!-- Subtotal Section -->
        <div class="px-8 py-4 border-b border-[#e8ddd4]">
          <div class="flex justify-end">
            <div class="w-full sm:w-80 space-y-1">
              <div class="flex justify-between text-xs">
                <span class="font-bold text-[#271310]">Subtotal</span>
                <span class="font-bold text-[#271310]">{{ formatRupiah(order.subtotal) }}</span>
              </div>
              <p class="text-[10px] text-[#271310]/50 text-right">Total Kuantitas (Aktif) {{ totalItems }} produk</p>
            </div>
          </div>
        </div>

        <!-- Grand Total Section -->
        <div class="px-8 py-5 bg-[#faf6f2]">
          <div class="flex justify-end">
            <div class="w-full sm:w-80">
              <div class="bg-white border border-[#e8ddd4] rounded-xl p-4 space-y-2.5">
                <div class="flex justify-between text-xs">
                  <span class="text-[#271310]/60">Subtotal Pesanan</span>
                  <span class="text-[#271310]/80">{{ formatRupiah(order.subtotal) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-[#271310]/60">Biaya Pengiriman</span>
                  <span class="text-[#271310]/80">{{ shippingCost > 0 ? formatRupiah(shippingCost) : 'Gratis' }}</span>
                </div>
                <div class="border-t border-[#e8ddd4] pt-2.5 mt-2.5">
                  <div class="flex justify-between items-baseline">
                    <span class="text-xs font-bold text-[#271310]">Total Pembayaran</span>
                    <span class="text-xl font-extrabold text-[#7d562d]">{{ formatRupiah(order.total_price) }}</span>
                  </div>
                </div>
              </div>

              <p class="text-[10px] text-[#271310]/40 mt-3 text-center leading-relaxed">
                Biaya-biaya yang ditagihkan oleh Bay & Coffee (jika ada) sudah termasuk pajak.
              </p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-6 border-t border-[#e8ddd4] bg-[#faf6f2]/50">
          <div class="space-y-1">
            <p class="text-xs font-bold text-[#271310]">Bay & Coffee</p>
            <p class="text-[10px] text-[#271310]/50 leading-relaxed">
              Premium Artisan Coffee Roastery<br>
              Indonesia
            </p>
          </div>
          <div class="text-center mt-6">
            <p class="text-[10px] text-[#271310]/30">1 of 1</p>
            <p class="text-[10px] text-[#271310]/30">End of receipt</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style>
/* Import Google Fonts for print */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

@media print {
  /* Hide navigation bars and action buttons */
  .no-print {
    display: none !important;
  }

  /* Reset background */
  .invoice-page {
    background: white !important;
    min-height: auto !important;
  }

  /* Reset the invoice card */
  .invoice-page > div > div {
    box-shadow: none !important;
    border: none !important;
    border-radius: 0 !important;
  }

  /* Remove page margins */
  @page {
    margin: 10mm 10mm;
    size: A4;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
}
</style>
