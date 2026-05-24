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
  { label: 'Semua Pesanan',   value: 'all' },
  { label: 'Sedang Diproses', value: 'processing' },
  { label: 'Dalam Pengiriman',value: 'shipped' },
  { label: 'Selesai',   value: 'completed' },
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
    // Determine UI status based on order status
    let uiStatus = order.status;
    
    let uiStatusLabel = 'Menunggu Konfirmasi';
    if (order.status === 'pending') {
      uiStatusLabel = 'Menunggu Pembayaran';
    } else if (order.status === 'processing') {
      uiStatusLabel = 'Sedang Diproses';
    } else if (order.status === 'shipped') {
      uiStatusLabel = 'Dalam Pengiriman';
    } else if (order.status === 'completed') {
      uiStatusLabel = 'Sampai Tujuan';
    } else if (order.status === 'cancelled') {
      uiStatusLabel = 'Dibatalkan';
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
      items: uiItems,
      rawOrder: order
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
    case 'processing': return 'bg-blue-500/15 text-blue-600';
    case 'shipped': return 'bg-purple-500/15 text-purple-600';
    case 'completed': return 'bg-secondary/15 text-secondary';
    case 'cancelled': return 'bg-error/15 text-error';
    default: return 'bg-outline-variant/30 text-outline';
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
</script>

<template>
  <AppLayout>
    <section class="bg-primary py-10 sm:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-on-primary/50 mb-4">
          <Link href="/" class="hover:text-on-primary transition-colors duration-200">Home</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-on-primary font-medium">Status Pengiriman</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary">Status Pengiriman</h1>
        <p class="text-sm text-on-primary/60 mt-1">Pantau proses pengemasan dan pelacakan kopi Anda</p>
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
            <span class="material-symbols-outlined text-outline/40 text-3xl">local_shipping</span>
          </div>
          <h3 class="text-base sm:text-lg font-bold text-primary mb-1">Tidak ada pengiriman ditemukan</h3>
          <p class="text-xs sm:text-sm text-on-surface-variant mb-6">Coba pilih filter lain atau belanja kopi terbaik Anda terlebih dahulu.</p>
          <Link href="/shop"
            class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 text-xs sm:text-sm">
            <span class="material-symbols-outlined text-sm">storefront</span>
            Belanja Sekarang
          </Link>
        </div>

        <!-- Transactions List -->
        <div v-else class="space-y-8">
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

            <!-- Content Grid -->
            <div class="border-t border-outline-variant/10 p-5 bg-surface-container-lowest grid grid-cols-1 md:grid-cols-12 gap-8">
              
              <!-- Left Side: Order Items List -->
              <div class="md:col-span-5 space-y-4">
                <h4 class="text-xs font-bold text-outline uppercase tracking-wider mb-3 flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-sm">shopping_bag</span>
                  Detail Pembelian
                </h4>
                <div class="divide-y divide-outline-variant/10">
                  <div v-for="item in tx.items" :key="item.name" class="py-3 first:pt-0 last:pb-0 flex items-center gap-3.5">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-surface-container flex-shrink-0 flex items-center justify-center border border-outline-variant/10">
                      <img v-if="item.foto_product" :src="`/storage/${item.foto_product}`" :alt="item.name" class="w-full h-full object-cover">
                      <span v-else class="material-symbols-outlined text-outline/50">local_cafe</span>
                    </div>
                    <div class="min-w-0 flex-grow">
                      <p class="font-bold text-xs text-primary leading-snug truncate">{{ item.name }}</p>
                      <p class="text-[10px] text-on-surface-variant mt-0.5">{{ item.weight }} · {{ item.qty }} barang</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                      <span class="font-bold text-xs text-primary">{{ formatRupiah(item.price * item.qty) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Total Summary Box -->
                <div class="bg-surface-container-low p-4 rounded-xl mt-4 border border-outline-variant/20">
                  <div class="flex justify-between items-center text-xs">
                    <span class="text-on-surface-variant">Total Belanja ({{ tx.items.length }} Item)</span>
                    <span class="font-bold text-primary text-sm">{{ formatRupiah(tx.total) }}</span>
                  </div>
                  
                  <div v-if="tx.status === 'pending'" class="mt-4 pt-3 border-t border-outline-variant/20 flex justify-end">
                    <button @click="openPayment(tx)"
                      class="inline-flex items-center gap-1.5 bg-secondary hover:bg-secondary/90 text-on-secondary font-bold px-4 py-2 rounded-xl transition-all duration-200 text-xs shadow-sm">
                      <span class="material-symbols-outlined text-xs">payment</span>
                      Bayar Sekarang
                    </button>
                  </div>
                </div>
              </div>

              <!-- Right Side: Tracking Stepper / Timeline -->
              <div class="md:col-span-7 border-l border-outline-variant/30 pl-6 md:pl-8 space-y-6 relative ml-3 md:ml-0">
                <h4 class="text-xs font-bold text-outline uppercase tracking-wider mb-3 flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-sm">local_shipping</span>
                  Pelacakan Pengiriman (Oleh Admin)
                </h4>

                <!-- Cancelled State Banner -->
                <div v-if="tx.status === 'cancelled'" class="bg-error/10 border border-error/20 p-4 rounded-2xl text-error flex items-center gap-3.5">
                  <span class="material-symbols-outlined text-2xl flex-shrink-0">cancel</span>
                  <div>
                    <p class="font-bold text-xs">Pesanan Dibatalkan</p>
                    <p class="text-[10px] text-error/80 mt-0.5">Maaf, pesanan ini telah dibatalkan secara sistem dan tidak dapat diproses lebih lanjut.</p>
                  </div>
                </div>

                <!-- Expired State Banner -->
                <div v-else-if="tx.status === 'expired'" class="bg-outline-variant/20 border border-outline-variant/30 p-4 rounded-2xl text-on-surface-variant flex items-center gap-3.5">
                  <span class="material-symbols-outlined text-2xl flex-shrink-0">event_busy</span>
                  <div>
                    <p class="font-bold text-xs">Transaksi Kedaluwarsa</p>
                    <p class="text-[10px] text-on-surface-variant/80 mt-0.5">Sesi pembayaran telah berakhir karena tidak diselesaikan tepat waktu.</p>
                  </div>
                </div>

                <div v-else class="space-y-6">
                  <!-- Step 1: Pesanan Dibuat -->
                  <div class="relative flex gap-4">
                    <span class="absolute -left-[33px] md:-left-[41px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-white bg-secondary">
                      <span class="material-symbols-outlined text-[10px]">check</span>
                    </span>
                    <div>
                      <h5 class="text-xs font-bold text-primary">Pesanan Dibuat</h5>
                      <p class="text-[10px] text-on-surface-variant/80 mt-0.5">Pesanan berhasil dicatat dan masuk ke sistem roastery kami.</p>
                    </div>
                  </div>

                  <!-- Step 2: Pembayaran -->
                  <div class="relative flex gap-4">
                    <span class="absolute -left-[33px] md:-left-[41px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-white"
                      :class="tx.rawOrder.status_payment === 'paid' ? 'bg-secondary' : 'bg-outline-variant'">
                      <span class="material-symbols-outlined text-[10px]">{{ tx.rawOrder.status_payment === 'paid' ? 'check' : 'hourglass_empty' }}</span>
                    </span>
                    <div>
                      <h5 class="text-xs font-bold" :class="tx.rawOrder.status_payment === 'paid' ? 'text-primary' : 'text-outline'">Pembayaran Diverifikasi</h5>
                      <p class="text-[10px] text-on-surface-variant/80 mt-0.5">
                        {{ tx.rawOrder.status_payment === 'paid' ? 'Pembayaran lunas dan berhasil diverifikasi secara sistem.' : 'Menunggu pembayaran selesai diverifikasi.' }}
                      </p>
                    </div>
                  </div>

                  <!-- Step 3: Proses Admin -->
                  <div class="relative flex gap-4">
                    <span class="absolute -left-[33px] md:-left-[41px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-white"
                      :class="['processing', 'shipped', 'completed'].includes(tx.status) ? 'bg-secondary' : 'bg-outline-variant'">
                      <span class="material-symbols-outlined text-[10px]">{{ ['processing', 'shipped', 'completed'].includes(tx.status) ? 'check' : 'pending' }}</span>
                    </span>
                    <div>
                      <h5 class="text-xs font-bold" :class="['processing', 'shipped', 'completed'].includes(tx.status) ? 'text-primary' : 'text-outline'">Sedang Diproses Admin</h5>
                      <p class="text-[10px] text-on-surface-variant/80 mt-0.5">
                        {{ ['processing', 'shipped', 'completed'].includes(tx.status) ? 'Biji kopi segar pilihan Anda sedang dikemas rapi oleh tim roastery.' : 'Pesanan akan diproses segera setelah pembayaran diverifikasi.' }}
                      </p>
                    </div>
                  </div>

                  <!-- Step 4: Pengiriman -->
                  <div class="relative flex gap-4">
                    <span class="absolute -left-[33px] md:-left-[41px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-white"
                      :class="['shipped', 'completed'].includes(tx.status) ? 'bg-secondary' : 'bg-outline-variant'">
                      <span class="material-symbols-outlined text-[10px]">{{ ['shipped', 'completed'].includes(tx.status) ? 'check' : 'local_shipping' }}</span>
                    </span>
                    <div>
                      <h5 class="text-xs font-bold" :class="['shipped', 'completed'].includes(tx.status) ? 'text-primary' : 'text-outline'">Dalam Pengiriman</h5>
                      <p class="text-[10px] text-on-surface-variant/80 mt-0.5">
                        {{ ['shipped', 'completed'].includes(tx.status) ? 'Kopi segar Anda telah diserahkan ke kurir dan sedang dalam perjalanan.' : 'Pesanan akan dikirimkan setelah proses pengemasan selesai.' }}
                      </p>
                    </div>
                  </div>

                  <!-- Step 5: Selesai -->
                  <div class="relative flex gap-4">
                    <span class="absolute -left-[33px] md:-left-[41px] w-4.5 h-4.5 rounded-full flex items-center justify-center text-white"
                      :class="tx.status === 'completed' ? 'bg-secondary' : 'bg-outline-variant'">
                      <span class="material-symbols-outlined text-[10px]">{{ tx.status === 'completed' ? 'check' : 'sports_score' }}</span>
                    </span>
                    <div>
                      <h5 class="text-xs font-bold" :class="tx.status === 'completed' ? 'text-primary' : 'text-outline'">Sampai Tujuan</h5>
                      <p class="text-[10px] text-on-surface-variant/80 mt-0.5">
                        {{ tx.status === 'completed' ? 'Kopi telah diterima di alamat tujuan! Selamat menikmati seduhan terbaik Anda.' : 'Kopi belum sampai ke alamat tujuan.' }}
                      </p>
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>

      </div>
    </section>
  </AppLayout>
</template>
