<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '../Layouts/App.vue';

// const categories = ['Asal Tunggal', 'Racikan', 'Tanpa Kafein', 'Beraroma'];
// const roastLevels = ['Ringan', 'Sedang', 'Sedang-Gelap', 'Gelap'];
const priceRanges = [
  { key: 'all', label: 'Semua Harga' },
  { key: 'under_150', label: 'Di bawah Rp 150.000' },
  { key: '150_200', label: 'Rp 150.000 – Rp 200.000' },
  { key: '200_300', label: 'Rp 200.000 – Rp 300.000' },
  { key: 'above_300', label: 'Di atas Rp 300.000' }
];

const props = defineProps({
    products: Array,
    categories: Array,
    error: Object,
    selectedCategory: String,
    selectedPrice: String,
})

const activeCategories = computed(() => {
    return props.selectedCategory ? props.selectedCategory.split(',').map(Number) : [];
});

const toggleCategory = (id) => {
    let ids = [...activeCategories.value];
    const index = ids.indexOf(id);
    if (index > -1) {
        ids.splice(index, 1);
    } else {
        ids.push(id);
    }
    
    router.get('/shop', {
        category: ids.length > 0 ? ids.join(',') : undefined,
        price: props.selectedPrice !== 'all' ? props.selectedPrice : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const filterPrice = (key) => {
    router.get('/shop', {
        category: activeCategories.value.length > 0 ? activeCategories.value.join(',') : undefined,
        price: key !== 'all' ? key : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const formatRupiah = (value) => {
  return 'Rp ' + value.toLocaleString('id-ID');
};

const loadingProductId = ref(null);

const addToCart = (productId) => {
    loadingProductId.value = productId;
    router.post('/cart', {
        product_id: productId,
        qty: 1
    }, {
        preserveScroll: true,
        onFinish: () => {
            loadingProductId.value = null;
        }
    })
}

</script>

<template>
  <AppLayout>
    <!-- ===== PAGE HEADER ===== -->
    <section class="bg-primary py-12 sm:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-on-primary/50 mb-4">
          <Link href="/" class="hover:text-on-primary transition-colors duration-200">Home</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-on-primary font-medium">Belanja</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary">Kopi Kami</h1>
        <p class="text-sm sm:text-base text-on-primary/65 mt-2 max-w-xl">Jelajahi pilihan kopi yang baru disangrai, single-origin, dan racikan pilihan kami.</p>
      </div>
    </section>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="py-8 sm:py-12 lg:py-16 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">

          <!-- SIDEBAR FILTERS -->
          <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-5 sm:p-6 sticky top-28">
              <h3 class="text-sm font-bold text-primary mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">tune</span>
                Filter
              </h3>

              <!-- Category -->
              <!-- <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Kategori</h4>
                <div class="flex flex-col gap-2">
                  <label v-for="cat in categories" :key="cat" class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="checkbox" class="w-4 h-4 rounded border-outline-variant text-secondary accent-secondary" />
                    <span class="text-sm text-on-surface-variant group-hover:text-primary transition-colors duration-200">{{ cat }}</span>
                  </label>
                </div>
              </div> -->

              <!-- Roast Level -->
              <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Kategori</h4>
                <div class="flex flex-col gap-2">
                  <label v-for="roast in categories" :key="roast.id" class="flex items-center gap-2.5 cursor-pointer group">
                    <input 
                      type="checkbox" 
                      class="w-4 h-4 rounded border-outline-variant text-secondary accent-secondary cursor-pointer"
                      :checked="activeCategories.includes(roast.id)"
                      @change="toggleCategory(roast.id)"
                    />
                    <span class="text-sm text-on-surface-variant group-hover:text-primary transition-colors duration-200">{{ roast.nama_kategori }}</span>
                  </label>
                </div>
              </div>

              <!-- Price Range -->
              <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">Rentang Harga</h4>
                <div class="flex flex-col gap-2">
                  <label v-for="range in priceRanges" :key="range.key" class="flex items-center gap-2.5 cursor-pointer group">
                    <input 
                      type="radio" 
                      name="price" 
                      class="w-4 h-4 border-outline-variant text-secondary accent-secondary cursor-pointer" 
                      :value="range.key"
                      :checked="(selectedPrice || 'all') === range.key"
                      @change="filterPrice(range.key)"
                    />
                    <span class="text-sm text-on-surface-variant group-hover:text-primary transition-colors duration-200">{{ range.label }}</span>
                  </label>
                </div>
              </div>
            </div>
          </aside>

          <!-- PRODUCT GRID AREA -->
          <div class="flex-grow">

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
              <div v-for="product in products" :key="product.product_name"
                class="group bg-surface-container-lowest rounded-xl sm:rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-outline-variant/20 flex flex-col transition-shadow duration-300"
              >
                    <div class="relative aspect-square overflow-hidden bg-surface-container-low">
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                          <img loading="lazy" :src="product.foto_product ? `/storage/${product.foto_product}` : null" alt="" class="w-full h-full object-cover">
                    </div>
                    <!-- <span v-if="product.badge"
                        class="absolute top-2 sm:top-3 left-2 sm:left-3 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full"
                        :class="product.badge === 'Best Seller' ? 'bg-secondary text-on-secondary' : 'bg-primary text-on-primary'"
                    >Badges</span> -->
                    </div>

                    <div class="p-3 sm:p-4 flex flex-col flex-grow">
                    <Link :href="`/shop/${product.id}`">
                      <h4 class="font-bold text-xs sm:text-sm text-primary leading-snug mb-0.5">{{ product.nama_product }}</h4>
                    </Link>
                    <p class="text-[10px] sm:text-xs text-on-surface-variant mb-3">{{ product.weight + ' ' + product.satuan }}</p>
                    <div class="mt-auto flex items-center justify-between">
                      <span class="font-bold text-sm sm:text-base text-primary">{{ formatRupiah(product.harga) }}</span>
                      <button :disabled="loadingProductId === product.id" @click="addToCart(product.id)" class="p-1.5 sm:p-2 rounded-lg sm:rounded-xl bg-secondary/10 hover:bg-secondary text-secondary hover:text-on-secondary transition-all duration-200 disabled:opacity-50 flex items-center justify-center min-w-[32px] sm:min-w-[40px] h-[32px] sm:h-[40px]">
                          <span v-if="loadingProductId === product.id" class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full" role="status"></span>
                          <span v-else class="material-symbols-outlined text-sm sm:text-base" style="font-variation-settings:'FILL' 1">add_shopping_cart</span>
                      </button>
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


