<template>
  <AppLayout>

    <!-- ===== BREADCRUMB ===== -->
    <section class="bg-surface border-b border-outline-variant/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-on-surface-variant">
          <Link href="/" class="hover:text-secondary transition-colors duration-200">Home</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <Link href="/shop" class="hover:text-secondary transition-colors duration-200">Shop</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-primary font-medium">{{ product.name }}</span>
        </nav>
      </div>
    </section>

    <!-- ===== PRODUCT DETAIL ===== -->
    <section class="py-8 sm:py-12 lg:py-16 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-14">

          <!-- IMAGE GALLERY -->
          <div class="w-full lg:w-1/2">
            <div class="sticky top-28">
              <!-- Main Image -->
              <div class="aspect-square rounded-2xl overflow-hidden bg-surface-container-low border border-outline-variant/20 mb-3 sm:mb-4">
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                  <span class="material-symbols-outlined text-outline/30" style="font-size:120px; font-variation-settings:'FILL' 1">{{ product.icon }}</span>
                </div>
              </div>
              <!-- Thumbnail Row -->
              <div class="grid grid-cols-4 gap-2 sm:gap-3">
                <div v-for="n in 4" :key="n"
                  class="aspect-square rounded-xl overflow-hidden bg-surface-container-low border-2 cursor-pointer transition-all duration-200"
                  :class="n === 1 ? 'border-secondary' : 'border-outline-variant/20 hover:border-secondary/50'"
                >
                  <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                    <span class="material-symbols-outlined text-outline/30" style="font-size:32px; font-variation-settings:'FILL' 1">{{ product.icon }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- PRODUCT INFO -->
          <div class="w-full lg:w-1/2">

            <!-- Badge -->
            <div v-if="product.badge" class="mb-3">
              <span class="text-[10px] sm:text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full"
                :class="product.badge === 'Best Seller' ? 'bg-secondary/15 text-secondary' : 'bg-primary/10 text-primary'"
              >{{ product.badge }}</span>
            </div>

            <!-- Title & Rating -->
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary mb-2">{{ product.name }}</h1>
            <div class="flex items-center gap-3 mb-4">
              <div class="flex items-center gap-0.5">
                <span v-for="n in 5" :key="n"
                  class="material-symbols-outlined text-secondary text-sm"
                  style="font-variation-settings:'FILL' 1">star</span>
              </div>
              <span class="text-sm text-on-surface-variant">{{ product.rating }} · {{ product.reviews }} reviews</span>
            </div>

            <!-- Price -->
            <div class="flex items-baseline gap-3 mb-6">
              <span class="text-2xl sm:text-3xl font-bold text-primary">{{ product.price }}</span>
              <span v-if="product.oldPrice" class="text-base text-on-surface-variant line-through">{{ product.oldPrice }}</span>
            </div>

            <!-- Description -->
            <p class="text-sm sm:text-base text-on-surface-variant leading-relaxed mb-6 pb-6 border-b border-outline-variant/20">
              {{ product.description }}
            </p>

            <!-- Details -->
            <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-outline-variant/20">
              <div v-for="detail in product.details" :key="detail.label">
                <div class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-1">{{ detail.label }}</div>
                <div class="text-sm font-semibold text-primary">{{ detail.value }}</div>
              </div>
            </div>

            <!-- Quantity -->
            <div class="mb-6">
              <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2 block">Quantity</label>
              <div class="inline-flex items-center border border-outline-variant/30 rounded-xl overflow-hidden">
                <button @click="qty > 1 && qty--"
                  class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors duration-200">
                  <span class="material-symbols-outlined text-base">remove</span>
                </button>
                <span class="w-12 h-10 flex items-center justify-center text-sm font-semibold text-primary border-x border-outline-variant/30">{{ qty }}</span>
                <button @click="qty++"
                  class="w-10 h-10 flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors duration-200">
                  <span class="material-symbols-outlined text-base">add</span>
                </button>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 mb-8">
              <button class="flex-grow inline-flex items-center justify-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-secondary/20 hover:shadow-xl text-sm sm:text-base">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">shopping_cart</span>
                Add to Cart
              </button>
            </div>

            <!-- Perks -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-4 sm:p-5">
              <div class="flex flex-col gap-3">
                <div v-for="perk in perks" :key="perk.label" class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings:'FILL' 1">{{ perk.icon }}</span>
                  </div>
                  <div>
                    <div class="text-xs sm:text-sm font-semibold text-primary">{{ perk.label }}</div>
                    <div class="text-[11px] text-on-surface-variant">{{ perk.sub }}</div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- ===== RELATED PRODUCTS ===== -->
    <!-- <section class="py-10 sm:py-14 bg-surface-container-low">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl sm:text-2xl font-bold text-primary mb-6 sm:mb-8">You May Also Like</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
          <Link v-for="item in related" :key="item.name" :href="`/shop/${item.slug}`"
            class="group bg-surface-container-lowest rounded-xl sm:rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-outline-variant/20 flex flex-col transition-shadow duration-300"
          >
            <div class="aspect-square overflow-hidden bg-surface-container-low">
              <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                <span class="material-symbols-outlined text-outline/40" style="font-size:48px; font-variation-settings:'FILL' 1">{{ item.icon }}</span>
              </div>
            </div>
            <div class="p-3 sm:p-4">
              <h4 class="font-bold text-xs sm:text-sm text-primary leading-snug mb-0.5">{{ item.name }}</h4>
              <p class="text-[10px] sm:text-xs text-on-surface-variant mb-2">{{ item.weight }}</p>
              <span class="font-bold text-sm text-primary">{{ item.price }}</span>
            </div>
          </Link>
        </div>
      </div>
    </section> -->
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../Layouts/App.vue';

const qty = ref(1);

// Dummy data — will be replaced with props from backend
const product = {
  name: 'cek-cart',
  icon: 'coffee',
  badge: 'Best Seller',
  rating: '4.9',
  reviews: '128',
  price: 'Rp 180.000',
  oldPrice: 'Rp 220.000',
  description: 'A bright, complex single-origin coffee with delicate floral aromatics, vibrant citrus acidity, and a silky sweet finish. Grown at high altitudes in the Yirgacheffe region of Ethiopia, this light roast showcases the best of African coffee traditions.',
  details: [
    { label: 'Origin',  value: 'Yirgacheffe, Ethiopia' },
    { label: 'Roast',   value: 'Light' },
    { label: 'Weight',  value: '12 oz (340g)' },
    { label: 'Process', value: 'Washed' },
  ],
};

const perks = [
  { icon: 'local_shipping', label: 'Free Shipping',     sub: 'On orders over Rp 400.000' },
  { icon: 'timer',          label: 'Roasted to Order',  sub: 'Ships within 48 hours' },
  { icon: 'undo',           label: 'Easy Returns',      sub: '30-day money-back guarantee' },
];

const related = [
  { name: 'Guatemala Antigua',    weight: '12oz · Medium Roast', price: 'Rp 165.000', icon: 'local_cafe',   slug: 'guatemala-antigua' },
  { name: 'House Blend – Medium', weight: '16oz · Medium Roast', price: 'Rp 210.000', icon: 'coffee_maker', slug: 'house-blend-medium' },
  { name: 'Colombian Supremo',    weight: '12oz · Medium Roast', price: 'Rp 170.000', icon: 'local_cafe',   slug: 'colombian-supremo' },
  { name: 'French Roast Dark',    weight: '16oz · Dark Roast',   price: 'Rp 210.000', icon: 'inventory_2',  slug: 'french-roast-dark' },
];
</script>
