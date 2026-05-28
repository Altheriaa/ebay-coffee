<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AppLayout from '../Layouts/App.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const toastMixin = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  background: '#271310',
  color: '#fff8f6',
  iconColor: '#f0bd8b',
  customClass: {
    popup: 'rounded-2xl border border-[#7d562d]/30 shadow-lg font-sans',
  },
  didOpen: (toastEl) => {
    toastEl.onmouseenter = Swal.stopTimer;
    toastEl.onmouseleave = Swal.resumeTimer;
  }
});

const toast = {
  fire(title, text, icon) {
    if (typeof title === 'object') {
      if (title.showCancelButton || title.input) {
        return Swal.fire({
          ...title,
          background: '#fff8f6',
          color: '#271310',
          confirmButtonColor: '#7d562d',
          cancelButtonColor: '#827472',
          customClass: {
            popup: 'rounded-3xl border border-[#eed5cf] shadow-xl font-sans',
            title: 'font-headline-sm text-primary',
            confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
            cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
          }
        });
      }
      return toastMixin.fire(title);
    }
    return toastMixin.fire({
      icon: icon || 'info',
      title: title,
      html: text
    });
  },
  error(message) {
    return toastMixin.fire({
      icon: 'error',
      title: message
    });
  },
  success(message) {
    return toastMixin.fire({
      icon: 'success',
      title: message
    });
  }
};

const props = defineProps({
  orders: Array,
  midtransClientKey: String,
});

const activeTab = ref('all');
const searchQuery = ref('');

const tabs = [
  { label: 'Semua Transaksi', value: 'all' },
  { label: 'Belum Bayar',     value: 'pending' },
  { label: 'Selesai',         value: 'selesai' },
  { label: 'Kedaluwarsa',     value: 'expired' },
];

onMounted(() => {
    const script = document.createElement('script');
    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
    script.setAttribute('data-client-key', props.midtransClientKey);
    document.head.appendChild(script);
});

// Map orders to UI transaction structure
const transactions = computed(() => {
  return (props.orders || []).map(order => {
    // Determine UI status
    let uiStatus = 'pending';
    let uiStatusLabel = 'Menunggu Pembayaran';
    
    if (order.status_payment === 'paid') {
      uiStatus = 'selesai';
      if (order.status === 'completed') {
        uiStatusLabel = 'Pesanan Selesai';
      } else if (order.status === 'shipped') {
        uiStatusLabel = 'Pesanan Dikirim';
      } else if (order.status === 'processing') {
        uiStatusLabel = 'Pesanan Diproses';
      } else {
        uiStatusLabel = 'Pembayaran Berhasil';
      }
    } else if (order.status_payment === 'expired' || order.status_payment === 'failed' || order.status === 'cancelled') {
      uiStatus = 'expired';
      if (order.status === 'cancelled') {
        uiStatusLabel = 'Pesanan Dibatalkan';
      } else if (order.status_payment === 'failed') {
        uiStatusLabel = 'Pembayaran Gagal';
      } else {
        uiStatusLabel = 'Pembayaran Kedaluwarsa';
      }
    } else {
      uiStatus = 'pending';
      uiStatusLabel = 'Menunggu Pembayaran';
    }

    // Format date: e.g., '22 Mei 2026 · 10:30 WIB'
    const dateObj = new Date(order.created_at);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    const formattedDate = dateObj.toLocaleDateString('id-ID', options);
    const hours = String(dateObj.getHours()).padStart(2, '0');
    const minutes = String(dateObj.getMinutes()).padStart(2, '0');
    const uiDate = `${formattedDate} · ${hours}:${minutes} WIB`;

    // Map items
    const uiItems = (order.order_items || []).map(item => {
      const prod = item.product || {};
      return {
        name: prod.nama_product || 'Produk Tidak Tersedia',
        weight: `${prod.weight || 0} ${prod.satuan || 'gr'}`,
        price: item.price,
        qty: item.qty,
        icon: 'local_cafe',
        foto_product: prod.foto_product
      };
    });

    return {
      id: order.id,
      invoice: order.invoice_number,
      date: uiDate,
      status: uiStatus,
      statusLabel: uiStatusLabel,
      paymentMethod: order.payment_method || 'Midtrans',
      total: order.total_price,
      snap_token: order.snap_token,
      items: uiItems
    };
  });
});

const filteredTransactions = computed(() => {
  return transactions.value.filter(tx => {
    // Status Filter
    if (activeTab.value !== 'all' && tx.status !== activeTab.value) {
      return false;
    }
    // Search Query Filter
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase();
      const invoiceMatch = tx.invoice.toLowerCase().includes(q);
      const itemsMatch = tx.items.some(item => item.name.toLowerCase().includes(q));
      return invoiceMatch || itemsMatch;
    }
    return true;
  });
});

function getCountForTab(tabValue) {
  if (tabValue === 'all') return transactions.value.length;
  return transactions.value.filter(tx => tx.status === tabValue).length;
}

function getStatusClass(status) {
  switch (status) {
    case 'pending': return 'bg-yellow-500/15 text-yellow-600';
    case 'selesai': return 'bg-secondary/15 text-secondary';
    case 'expired': return 'bg-outline-variant/20 text-on-surface-variant/70';
    default:        return 'bg-outline-variant/30 text-outline';
  }
}

const formatRupiah = (value) => {
  return 'Rp ' + value.toLocaleString('id-ID');
};

function openPayment(tx) {
  if (window.snap && tx.snap_token) {
    window.snap.pay(tx.snap_token, {
      onSuccess: async (result) => {
        try {
          await axios.post('/payment/success', result);
        } catch (e) { console.error('Sync error:', e); }
        toast.fire('Berhasil', 'Pembayaran Anda telah diterima', 'success')
          .then(() => router.reload());
      },
      onPending: async (result) => {
        try {
          await axios.post('/payment/success', result);
        } catch (e) { console.error('Sync error:', e); }
        toast.fire('Menunggu Pembayaran', 'Silakan selesaikan pembayaran sesuai instruksi', 'info')
          .then(() => router.reload());
      },
      onError: (result) => {
        toast.fire('Gagal', 'Pembayaran Anda gagal', 'error');
      },
      onClose: () => {
        router.reload();
      }
    });
  } else {
    toast.fire('Gagal', 'Token pembayaran tidak ditemukan atau Snap belum dimuat.', 'error');
  }
}

async function cancelTransaction(tx) {
  const confirm = await toast.fire({
    title: 'Batalkan Transaksi',
    text: `Apakah Anda yakin ingin membatalkan transaksi ${tx.invoice}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Tidak',
    confirmButtonColor: '#d33'
  });

  if (confirm.isConfirmed) {
    router.post(`/transaksi/${tx.id}/cancel`, {}, {
      onSuccess: () => {
        toast.fire('Berhasil', 'Transaksi telah dibatalkan.', 'success');
      },
      onError: () => {
        toast.fire('Gagal', 'Gagal membatalkan transaksi.', 'error');
      }
    });
  }
}

function buyAgain(tx) {
  router.post(`/transaksi/${tx.id}/reorder`, {}, {
    onSuccess: () => {
      toast.fire('Berhasil', 'Produk berhasil ditambahkan kembali ke keranjang.', 'success');
    }
  });
}

function downloadInvoice(tx) {
  window.open(`/transaksi/${tx.id}/invoice`, '_blank');
}

</script>

<template>
  <AppLayout>
    <section class="bg-primary py-10 sm:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-on-primary/50 mb-4">
          <Link href="/" class="hover:text-on-primary transition-colors duration-200">Home</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-on-primary font-medium">Riwayat Transaksi</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary">Riwayat Transaksi</h1>
        <p class="text-sm text-on-primary/60 mt-1">Pantau status pesanan dan riwayat pembelian kopi terbaik Anda</p>
      </div>
    </section>

    <section class="py-8 sm:py-12 bg-background min-h-[500px]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Search & Tabs -->
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-8 pb-4 border-b border-outline-variant/20">
          <!-- Status Tabs -->
          <div class="flex items-center gap-1.5 bg-surface-container-low p-1 rounded-xl w-full md:w-auto overflow-x-auto">
            <button v-for="tab in tabs" :key="tab.value"
              @click="activeTab = tab.value"
              class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 whitespace-nowrap"
              :class="activeTab === tab.value
                ? 'bg-secondary text-on-secondary shadow-sm'
                : 'text-on-surface-variant hover:text-primary hover:bg-surface-container'"
            >
              {{ tab.label }}
              <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-black/10"
                :class="activeTab === tab.value ? 'bg-white/20' : ''">
                {{ getCountForTab(tab.value) }}
              </span>
            </button>
          </div>

          <!-- Search Bar -->
          <div class="relative w-full md:w-80">
            <input type="text" v-model="searchQuery" placeholder="Cari nomor invoice / produk..."
              class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-on-surface placeholder-outline focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 text-lg">search</span>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredTransactions.length === 0" class="text-center py-16">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-outline/40 text-3xl">receipt_long</span>
          </div>
          <h3 class="text-base sm:text-lg font-bold text-primary mb-1">Tidak ada transaksi ditemukan</h3>
          <p class="text-xs sm:text-sm text-on-surface-variant mb-6">Coba ubah filter atau lakukan transaksi belanja kopi terlebih dahulu.</p>
          <Link href="/shop"
            class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 text-xs sm:text-sm">
            <span class="material-symbols-outlined text-sm">storefront</span>
            Belanja Sekarang
          </Link>
        </div>

        <!-- Transactions List -->
        <div v-else class="space-y-6">
          <div v-for="tx in filteredTransactions" :key="tx.id"
            class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            
            <!-- Header Card -->
            <div class="bg-surface-container-low px-4 sm:px-6 py-4 flex flex-wrap gap-4 items-center justify-between border-b border-outline-variant/20">
              <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5">
                <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">{{ tx.invoice }}</span>
                <span class="text-xs text-outline/80">·</span>
                <span class="text-xs text-on-surface-variant">{{ tx.date }}</span>
                <span class="text-xs text-outline/80">·</span>
                <!-- Status Badge -->
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full"
                  :class="getStatusClass(tx.status)">
                  {{ tx.statusLabel }}
                </span>
              </div>
              
              <div class="flex items-center gap-1.5 text-xs text-outline">
                <span class="material-symbols-outlined text-sm">payments</span>
                Metode: <span class="font-semibold text-primary uppercase">{{ tx.paymentMethod }}</span>
              </div>
            </div>

            <!-- Items list -->
            <div class="p-4 sm:p-6 divide-y divide-outline-variant/10">
              <div v-for="item in tx.items" :key="item.name" class="py-4 first:pt-0 last:pb-0 flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container-low flex-shrink-0 flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high border border-outline-variant/10">
                  <img v-if="item.foto_product" loading="lazy" :src="`/storage/${item.foto_product}`" :alt="item.name" class="w-full h-full object-cover">
                  <span v-else class="material-symbols-outlined text-outline/50" style="font-size:28px; font-variation-settings:'FILL' 1">{{ item.icon }}</span>
                </div>
                <div class="flex-grow min-w-0">
                  <h4 class="font-bold text-xs sm:text-sm text-primary leading-tight truncate">{{ item.name }}</h4>
                  <p class="text-[10px] sm:text-xs text-on-surface-variant mt-0.5">{{ item.weight }} · {{ item.qty }} barang x {{ formatRupiah(item.price) }}</p>
                </div>
                <div class="text-right">
                  <span class="font-bold text-xs sm:text-sm text-primary">{{ formatRupiah(item.price * item.qty) }}</span>
                </div>
              </div>
            </div>

            <!-- Summary & Actions -->
            <div class="bg-surface-container-lowest px-4 sm:px-6 py-4 flex flex-col sm:flex-row gap-4 items-center justify-between border-t border-outline-variant/10">
              <div>
                <p class="text-xs text-on-surface-variant">Total Pembayaran</p>
                <p class="text-lg font-extrabold text-primary leading-none mt-1">{{ formatRupiah(tx.total) }}</p>
              </div>

              <!-- Context Actions -->
              <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <template v-if="tx.status === 'pending'">
                  <button @click="openPayment(tx)"
                    class="flex-grow sm:flex-grow-0 inline-flex items-center justify-center gap-1.5 bg-secondary hover:bg-secondary/90 text-on-secondary font-bold px-4 py-2.5 rounded-xl transition-all duration-200 text-xs shadow-sm">
                    <span class="material-symbols-outlined text-sm">payment</span>
                    Bayar Sekarang
                  </button>
                  <button @click="cancelTransaction(tx)"
                    class="flex-grow sm:flex-grow-0 inline-flex items-center justify-center gap-1.5 bg-surface border border-error/30 hover:bg-error/5 text-error font-semibold px-4 py-2.5 rounded-xl transition-all duration-200 text-xs">
                    <span class="material-symbols-outlined text-sm">cancel</span>
                    Batalkan
                  </button>
                </template>
                <template v-else-if="tx.status === 'selesai'">
                  <button @click="buyAgain(tx)"
                    class="flex-grow sm:flex-grow-0 inline-flex items-center justify-center gap-1.5 bg-secondary/10 hover:bg-secondary hover:text-on-secondary text-secondary font-bold px-4 py-2.5 rounded-xl transition-all duration-200 text-xs">
                    <span class="material-symbols-outlined text-sm">replay</span>
                    Beli Lagi
                  </button>
                  <button @click="downloadInvoice(tx)"
                    class="flex-grow sm:flex-grow-0 inline-flex items-center justify-center gap-1.5 bg-surface border border-outline-variant/40 hover:border-secondary text-on-surface-variant hover:text-secondary font-semibold px-4 py-2.5 rounded-xl transition-all duration-200 text-xs">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Unduh Invoice
                  </button>
                   <Link href="/shipping"
                    class="flex-grow sm:flex-grow-0 inline-flex items-center justify-center gap-1.5 bg-surface border border-outline-variant/40 hover:border-secondary text-on-surface-variant hover:text-secondary font-semibold px-4 py-2.5 rounded-xl transition-all duration-200 text-xs cursor-pointer">
                    <span class="material-symbols-outlined text-sm">local_shipping</span>
                    Status Pengiriman
                  </Link>
                </template>
                <template v-else-if="tx.status === 'expired'">
                  <button @click="buyAgain(tx)"
                    class="flex-grow sm:flex-grow-0 inline-flex items-center justify-center gap-1.5 bg-secondary hover:bg-secondary/90 text-on-secondary font-bold px-5 py-2.5 rounded-xl transition-all duration-200 text-xs">
                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                    Pesan Ulang
                  </button>
                </template>
              </div>
            </div>

          </div>
        </div>

      </div>
    </section>
  </AppLayout>
</template>


