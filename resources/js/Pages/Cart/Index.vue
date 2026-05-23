<script setup>
import { Link } from '@inertiajs/vue3';
import { reactive, computed } from 'vue';
import AppLayout from '../Layouts/App.vue';

// Dummy cart data — will be replaced with real state management later
const cartItems = reactive([
  { name: 'Ethiopian Yirgacheffe', weight: '12oz · Light Roast',  price: 'Rp 180.000', icon: 'coffee',       qty: 2 },
  { name: 'Guatemala Antigua',     weight: '12oz · Medium Roast', price: 'Rp 165.000', icon: 'local_cafe',   qty: 1 },
  { name: 'House Blend – Medium',  weight: '16oz · Medium Roast', price: 'Rp 210.000', icon: 'coffee_maker', qty: 1 },
]);

const formatRupiah = (value) => {
  return 'Rp ' + value.toLocaleString('id-ID');
};

const subtotal = computed(() => {
  return cartItems.reduce((sum, item) => {
    return sum + (parseInt(item.price.replace(/[^\d]/g, ''), 10) || 0) * item.qty;
  }, 0);
});

const total = computed(() => {
  const shipping = subtotal.value >= 400000 ? 0 : 50000;
  return subtotal.value + shipping;
});

const trustBadges = [
  { icon: 'verified_user', label: 'Secure' },
  { icon: 'credit_card',   label: 'Encrypted' },
  { icon: 'undo',          label: '30-day Return' },
];
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
        <p class="text-sm text-on-primary/60 mt-1">{{ cartItems.length }} items in your cart</p>
      </div>
    </section>

    <!-- ===== CART CONTENT ===== -->
    <section class="py-8 sm:py-12 lg:py-16 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Empty State -->
        <div v-if="cartItems.length === 0" class="text-center py-20">
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
            <div v-for="(item, index) in cartItems" :key="item.name"
              class="border-b border-outline-variant/20 py-5 sm:py-6"
            >
              <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">

                <!-- Product -->
                <div class="sm:col-span-6 flex items-center gap-4">
                  <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-surface-container-low border border-outline-variant/20 flex-shrink-0">
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                      <span class="material-symbols-outlined text-outline/40" style="font-size:36px; font-variation-settings:'FILL' 1">{{ item.icon }}</span>
                    </div>
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-bold text-primary leading-snug">{{ item.name }}</h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">{{ item.weight }}</p>
                    <button @click="cartItems.splice(index, 1)"
                      class="mt-2 inline-flex items-center gap-1 text-xs text-error hover:text-error/80 font-medium transition-colors duration-200">
                      <span class="material-symbols-outlined text-xs">delete</span>
                      Remove
                    </button>
                  </div>
                </div>

                <!-- Price -->
                <div class="sm:col-span-2 text-sm font-semibold text-on-surface-variant sm:text-center">
                  <span class="sm:hidden text-xs text-on-surface-variant font-normal mr-1">Price:</span>
                  {{ item.price }}
                </div>

                <!-- Quantity -->
                <div class="sm:col-span-2 flex sm:justify-center">
                  <div class="inline-flex items-center border border-outline-variant/30 rounded-lg overflow-hidden">
                    <button @click="item.qty > 1 && item.qty--"
                      class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors duration-200">
                      <span class="material-symbols-outlined text-sm">remove</span>
                    </button>
                    <span class="w-10 h-8 flex items-center justify-center text-xs font-semibold text-primary border-x border-outline-variant/30">{{ item.qty }}</span>
                    <button @click="item.qty++"
                      class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors duration-200">
                      <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                  </div>
                </div>

                <!-- Subtotal -->
                <div class="sm:col-span-2 text-sm font-bold text-primary sm:text-right">
                  <span class="sm:hidden text-xs text-on-surface-variant font-normal mr-1">Subtotal:</span>
                  {{ formatRupiah(parseInt(item.price.replace(/[^\d]/g, ''), 10) * item.qty) }}
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

              <!-- Promo Code -->
              <div class="mb-5 pb-5 border-b border-outline-variant/20">
                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2 block">Promo Code</label>
                <div class="flex gap-2">
                  <input type="text" placeholder="Enter code"
                    class="flex-grow text-sm bg-surface border border-outline-variant/30 rounded-xl px-3 py-2.5 text-on-surface placeholder-outline focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
                  <button class="px-4 py-2.5 bg-primary/10 hover:bg-primary/20 text-primary text-sm font-semibold rounded-xl transition-colors duration-200">
                    Apply
                  </button>
                </div>
              </div>

              <!-- Total -->
              <div class="flex justify-between items-baseline mb-6">
                <span class="text-base font-bold text-primary">Total</span>
                <span class="text-2xl font-bold text-primary">{{ formatRupiah(total) }}</span>
              </div>

              <!-- Checkout -->
              <button class="w-full inline-flex items-center justify-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-secondary/20 hover:shadow-xl text-sm sm:text-base mb-3">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">lock</span>
                Proceed to Checkout
              </button>

              <!-- Trust -->
              <div class="flex items-center justify-center gap-4 pt-4">
                <div v-for="badge in trustBadges" :key="badge" class="flex items-center gap-1 text-on-surface-variant">
                  <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1">{{ badge.icon }}</span>
                  <span class="text-[10px] font-medium">{{ badge.label }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </AppLayout>
</template>


