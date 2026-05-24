<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
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

const loading = ref(false);

const props = defineProps({
    carts: Array,
    midtransClientKey: String,
})

const formatRupiah = (value) => {
  return 'Rp ' + value.toLocaleString('id-ID');
};

const subtotal = computed(() => {
  return props.carts.reduce((sum, item) => {
    return sum + (item.product.harga * item.qty);
  }, 0);
});

const total = computed(() => {
  const shipping = subtotal.value >= 400000 ? 0 : 50000;
  return subtotal.value + shipping;
});

const updateQty = (cartItemId, currentQty, amount) => {
  const newQty = currentQty + amount;
  if (newQty < 1) return;
  router.patch(`/cart/${cartItemId}`, {
    qty: newQty
  }, {
    preserveScroll: true
  });
}

const removeItem = (cartItemId) => {
  router.delete(`/cart/${cartItemId}`, {
    preserveScroll: true
  });
}

onMounted(() => {
    const script = document.createElement('script');
    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
    script.setAttribute('data-client-key', props.midtransClientKey);
    document.head.appendChild(script);
});

// Checkout
const checkout = async () => {
    const confirm = await toast.fire({
        title: 'Konfirmasi Pembayaran',
        text: "Konfirmasi Pembayaran",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        confirmButtonColor: '#7d562d'
    });

    if (confirm.isConfirmed) {
        loading.value = true;
        try {
            const response = await axios.post('/checkout'); 
            // reload halaman agar keranjang langsung kosong
            router.reload();
            // buka popup Midtrans
            openSnap(response.data.snap_token);
            } catch (error) {
                const message = error.response?.data?.message || 'Terjadi kesalahan sistem.';
                toast.error(message);
            } finally {
                loading.value = false;
            }
        }
    }

    // fungsi membuka pop up midtrans (snap)
    const openSnap = (token) => {
        window.snap.pay(token, {
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
        })
    }
</script>

<template>
  <AppLayout>
    <!-- ===== PAGE HEADER ===== -->
    <section class="bg-primary py-10 sm:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-on-primary/50 mb-4">
          <Link href="/" class="hover:text-on-primary transition-colors duration-200">Home</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-on-primary font-medium">Cart</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary">Shopping Cart</h1>
        <p class="text-sm text-on-primary/60 mt-1">{{ carts.length }} items in your cart</p>
      </div>
    </section>

    <!-- ===== CART CONTENT ===== -->
    <section class="py-8 sm:py-12 lg:py-16 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Empty State -->
        <div v-if="carts.length === 0" class="text-center py-20">
          <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-outline/40 text-4xl" style="font-variation-settings:'FILL' 0">shopping_cart</span>
          </div>
          <h2 class="text-xl font-bold text-primary mb-2">Your cart is empty</h2>
          <p class="text-sm text-on-surface-variant mb-6">Looks like you haven't added anything to your cart yet.</p>
          <Link href="/shop"
            class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 py-3 rounded-xl transition-all duration-200 text-sm">
            <span class="material-symbols-outlined text-base">storefront</span>
            Continue Shopping
          </Link>
        </div>

        <!-- Cart with items -->
        <div v-else class="flex flex-col lg:flex-row gap-8">

          <!-- CART ITEMS -->
          <div class="flex-grow">
            <!-- Header (desktop) -->
            <div class="hidden sm:grid grid-cols-12 gap-4 px-4 pb-3 border-b border-outline-variant/20 text-xs font-bold uppercase tracking-widest text-on-surface-variant">
              <div class="col-span-6">Product</div>
              <div class="col-span-2 text-center">Price</div>
              <div class="col-span-2 text-center">Quantity</div>
              <div class="col-span-2 text-right">Subtotal</div>
            </div>

            <!-- Items -->
            <div v-for="(item, index) in carts" :key="item.id"
              class="border-b border-outline-variant/20 py-5 sm:py-6"
            >
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">

                <!-- Product -->
                <div class="sm:col-span-6 flex items-center gap-4">
                  <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-surface-container-low border border-outline-variant/20 flex-shrink-0">
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                      <img v-if="item.product.foto_product" loading="lazy" :src="`/storage/${item.product.foto_product}`" :alt="item.product.nama_product" class="w-full h-full object-cover">
                      <span v-else class="material-symbols-outlined text-outline/40" style="font-size:36px; font-variation-settings:'FILL' 1">local_cafe</span>
                    </div>
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-bold text-primary leading-snug">{{ item.product.nama_product }}</h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">{{ item.product.weight }} {{ item.product.satuan }}</p>
                    <button @click="removeItem(item.id)"
                      class="mt-2 inline-flex items-center gap-1 text-xs text-error hover:text-error/80 font-medium transition-colors duration-200">
                      <span class="material-symbols-outlined text-xs">delete</span>
                      Remove
                    </button>
                  </div>
                </div>

                <!-- Price -->
                <div class="sm:col-span-2 text-sm font-semibold text-on-surface-variant sm:text-center">
                  <span class="sm:hidden text-xs text-on-surface-variant font-normal mr-1">Price:</span>
                  {{ formatRupiah(item.product.harga) }}
                </div>

                <!-- Quantity -->
                <div class="sm:col-span-2 flex sm:justify-center">
                  <div class="inline-flex items-center border border-outline-variant/30 rounded-lg overflow-hidden">
                    <button @click="updateQty(item.id, item.qty, -1)"
                      class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors duration-200">
                      <span class="material-symbols-outlined text-sm">remove</span>
                    </button>
                    <span class="w-10 h-8 flex items-center justify-center text-xs font-semibold text-primary border-x border-outline-variant/30">{{ item.qty }}</span>
                    <button @click="updateQty(item.id, item.qty, 1)"
                      class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors duration-200">
                      <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                  </div>
                </div>

                <!-- Subtotal -->
                <div class="sm:col-span-2 text-sm font-bold text-primary sm:text-right">
                  <span class="sm:hidden text-xs text-on-surface-variant font-normal mr-1">Subtotal:</span>
                  {{ formatRupiah(item.product.harga * item.qty) }}
                </div>
              </div>
            </div>

            <!-- Continue Shopping -->
            <div class="pt-6">
              <Link href="/shop"
                class="inline-flex items-center gap-2 text-sm font-semibold text-secondary hover:text-on-secondary-container transition-colors duration-200">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Continue Shopping
              </Link>
            </div>
          </div>

          <!-- ORDER SUMMARY -->
          <div class="w-full lg:w-96 flex-shrink-0">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-5 sm:p-6 sticky top-28">
              <h3 class="text-base font-bold text-primary mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">receipt_long</span>
                Order Summary
              </h3>

              <div class="space-y-3 mb-5 pb-5 border-b border-outline-variant/20">
                <div class="flex justify-between text-sm">
                  <span class="text-on-surface-variant">Subtotal</span>
                  <span class="font-semibold text-primary">{{ formatRupiah(subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-on-surface-variant">Shipping</span>
                  <span class="font-semibold text-primary" :class="subtotal >= 400000 ? 'text-secondary' : ''">
                    {{ subtotal >= 400000 ? 'Gratis' : formatRupiah(50000) }}
                  </span>
                </div>
                <div v-if="subtotal >= 400000" class="flex items-center gap-1.5 text-secondary text-xs">
                  <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1">check_circle</span>
                  You qualify for free shipping!
                </div>
              </div>

              <!-- Total -->
              <div class="flex justify-between items-baseline mb-6">
                <span class="text-base font-bold text-primary">Total</span>
                <span class="text-2xl font-bold text-primary">{{ formatRupiah(total) }}</span>
              </div>

              <!-- Checkout -->
              <button @click="checkout" :disabled="loading" class="w-full inline-flex items-center justify-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-secondary/20 hover:shadow-xl text-sm sm:text-base mb-3 disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="loading" class="animate-spin rounded-full h-5 w-5 border-2 border-on-secondary border-t-transparent"></span>
                <span v-else class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">lock</span>
                {{ loading ? 'Processing...' : 'Proceed to Checkout' }}
              </button>
            </div>
          </div>

        </div>
      </div>
    </section>

  </AppLayout>
</template>


