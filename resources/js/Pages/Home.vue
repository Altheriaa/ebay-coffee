<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from './Layouts/App.vue';

defineProps({ 
  message: String,
  products: {
    type: Array,
    default: () => []
  }
});

const badges = [
  { icon: 'local_shipping',    label: 'Gratis Ongkir',          sub: 'Untuk pesanan di atas Rp 400.000'  },
  { icon: 'timer',             label: 'Baru Disangrai',          sub: 'Dikirim dalam 48 jam' },
  { icon: 'eco',               label: 'Sumber Berkelanjutan',    sub: 'Dari petani langsung'  },
  { icon: 'workspace_premium', label: 'Garansi Kepuasan', sub: 'Atau uang kembali'      },
];

const collections = [
  {
    name: 'Biji Kopi (Whole Beans)',
    tag: 'Fresh & Whole',
    desc: 'Biji kopi pilihan yang baru disangrai. Ideal untuk Anda yang suka menggiling kopi sendiri di rumah.',
    href: '/shop?category=2',
    image: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=600&auto=format&fit=crop',
  },
  {
    name: 'Bubuk Halus (Fine Grind)',
    tag: 'Rich & Intense',
    desc: 'Gilingan halus presisi. Sangat cocok untuk metode seduh Espresso, Moka Pot, atau kopi Tubruk.',
    href: '/shop?category=3',
    image: 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?q=80&w=600&auto=format&fit=crop',
  },
  {
    name: 'Bubuk Kasar (Coarse Grind)',
    tag: 'Bold & Classic',
    desc: 'Gilingan kasar dengan ekstraksi merata. Sempurna untuk French Press maupun Cold Brew.',
    href: '/shop?category=5',
    image: 'https://images.unsplash.com/photo-1511537190424-bbbab87ac5eb?q=80&w=600&auto=format&fit=crop',
  },
];

const stats = [
  { value: '12+',  label: 'Negara asal'  },
  { value: '50k+', label: 'Pelanggan puas'    },
  { value: '48hr', label: 'Dari panggangan ke rumah' },
  { value: '100%', label: 'Asal usul terpercaya'  },
];

const reviews = [
  { name: 'Sarah M.', location: 'New York, USA',  quote: 'The Ethiopian Yirgacheffe is absolutely stunning — floral and bright, unlike anything I\'ve had from a local café.',                                          color: '#2e8b38' },
  { name: 'James K.', location: 'London, UK',     quote: 'I\'ve tried dozens of subscription services. Ebay & Coffee is the only one I keep coming back to. The freshness makes all the difference.',              color: '#1a2e1c' },
  { name: 'Priya R.', location: 'Melbourne, AUS', quote: 'Sustainable sourcing that you can actually taste. Knowing the farm my beans came from makes every sip feel meaningful.',                                   color: '#7a5230' },
];
</script>

<template>
  <AppLayout>

    <!-- ===== HERO ===== -->
    <section class="relative w-full min-h-screen flex items-center overflow-hidden bg-primary">
      <img
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBEwHc3IV0n2jDbBXV60Pw_8uTm65SHxgLgASuvFAoqGUafZCpa4YHWMxEP1EZEAYROMB8t_D2QOKFyfi8QmQPtLW1FJc3hK3JQF9OW5XFPwV711cOPstQpIhPLrKRrZGon5CW1_7uTYR2dnTMx-rYTu98IlmAVlvma4_sr87z_H9Be2de2MznMamJANH67hxiu0Mitr2MYOHgR3PfwVkFaJ_ht7EbnCFiMvPCOk41aS-G80kBC_oKAGjxz5khFvHLSPqqPJvnXaEI"
        alt="Freshly Roasted Coffee Beans"
        class="absolute inset-0 w-full h-full object-cover object-center"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/65 to-primary/20"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-primary/70 via-transparent to-transparent"></div>

      <div class="relative z-10 w-full max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 py-28 sm:py-32 md:py-40">
        <div class="w-full max-w-lg sm:max-w-xl lg:max-w-2xl">

          <!-- Badge -->
          <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/25 rounded-full px-3 py-1.5 mb-6 sm:mb-8">
            <span class="w-2 h-2 bg-secondary rounded-full animate-pulse flex-shrink-0"></span>
            <span class="text-[10px] sm:text-xs font-bold text-on-primary tracking-widest uppercase">PANEN BARU TERSEDIA</span>
          </div>

          <!-- Heading -->
          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-on-primary leading-[1.12] mb-5 sm:mb-6">
            Baru Dipanggang.<br/>
            <span class="text-primary-fixed-dim">Dikirim ke Pintu Anda.</span>
          </h1>

          <!-- Subtitle -->
          <p class="text-sm sm:text-base lg:text-lg text-on-primary/75 mb-8 sm:mb-10 leading-relaxed">
            Temukan secangkir kopi sempurna dengan sangraian artisanal kami — bersumber dari perkebunan berkelanjutan dan dikirim dalam waktu 48 jam setelah dipanggang.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <Link href="/shop" class="inline-flex items-center justify-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl transition-all duration-200 shadow-lg shadow-primary/40 hover:shadow-xl hover:-translate-y-0.5 text-sm sm:text-base w-full sm:w-auto">
              <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">shopping_bag</span>
              Belanja Sekarang
            </Link>
            <button class="inline-flex items-center justify-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-on-primary font-semibold px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl border border-white/30 transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
              <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 0">play_circle</span>
              Cerita Kami
            </button>
          </div>
        </div>
      </div>

      <div class="hidden sm:flex absolute bottom-8 left-1/2 -translate-x-1/2 flex-col items-center gap-1 text-on-primary/40 animate-bounce">
        <span class="text-[10px] tracking-widest uppercase font-medium">GULIR</span>
        <span class="material-symbols-outlined text-sm">keyboard_arrow_down</span>
      </div>
    </section>

    <!-- ===== TRUST BADGES ===== -->
    <section class="bg-surface border-b border-outline-variant/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 lg:divide-x lg:divide-outline-variant/20">
          <div v-for="(badge, i) in badges" :key="badge.label"
            class="flex items-center gap-3 py-5 px-4 sm:px-6"
            :class="i < 2 ? 'border-b border-outline-variant/20 lg:border-b-0' : ''"
          >
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-secondary text-lg sm:text-xl" style="font-variation-settings:'FILL' 1">{{ badge.icon }}</span>
            </div>
            <div class="min-w-0">
              <div class="font-bold text-xs sm:text-sm text-primary leading-tight">{{ badge.label }}</div>
              <div class="text-[11px] sm:text-xs text-on-surface-variant mt-0.5 truncate">{{ badge.sub }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== FEATURED COLLECTIONS ===== -->
    <section class="py-14 sm:py-20 lg:py-28 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-8 sm:mb-12">
          <div>
            <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-1.5">Pilihan Kami</p>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary">Koleksi Pilihan</h2>
          </div>
          <Link href="/shop" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:text-on-secondary-container transition-colors duration-200 self-start sm:self-auto">
            Lihat semua
            <span class="material-symbols-outlined text-base">arrow_forward</span>
          </Link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <Link v-for="col in collections" :key="col.name"
            :href="col.href"
            class="group relative overflow-hidden rounded-2xl cursor-pointer block"
          >
            <div class="aspect-[4/3] overflow-hidden">
              <img :src="col.image" :alt="col.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
              />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-primary/85 via-primary/25 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
              <span class="inline-block text-[10px] sm:text-xs font-bold uppercase tracking-widest text-primary-fixed-dim mb-1">{{ col.tag }}</span>
              <h3 class="text-lg sm:text-xl font-bold text-on-primary">{{ col.name }}</h3>
              <p class="text-xs sm:text-sm text-on-primary/70 mt-1 line-clamp-2">{{ col.desc }}</p>
              <div class="mt-3 sm:mt-4 inline-flex items-center gap-2 bg-white/20 hover:bg-white/35 backdrop-blur-sm text-on-primary text-xs sm:text-sm font-semibold px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl border border-white/30 transition-all duration-200 sm:translate-y-2 sm:opacity-0 sm:group-hover:translate-y-0 sm:group-hover:opacity-100">
                Jelajahi
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
              </div>
            </div>
          </Link>
        </div>
      </div>
    </section>

    <!-- ===== WHY US (PROMO BANNER) ===== -->
    <section class="py-14 sm:py-20 bg-surface-container-low">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-primary rounded-2xl sm:rounded-3xl overflow-hidden relative">
          <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-64 sm:w-96 h-64 sm:h-96 bg-secondary/20 rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-primary-fixed-dim/10 rounded-full translate-y-1/2 -translate-x-1/4"></div>
          </div>

          <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 sm:gap-12 items-center p-7 sm:p-10 lg:p-16">
            <div>
              <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-3">Mengapa Memilih Kami</p>
              <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary mb-4 sm:mb-5 leading-tight">
                Dari Kebun ke Cangkir —<br/>Kualitas Tanpa Kompromi
              </h2>
              <p class="text-on-primary/65 text-sm sm:text-base leading-relaxed mb-6 sm:mb-8">
                Kami bermitra langsung dengan petani kopi. Setiap biji kopi dipilih langsung, bersumber secara etis, dan disangrai dalam porsi kecil untuk memastikan profil rasa yang paling segar.
              </p>
              <button class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 py-3 rounded-xl transition-all duration-200 text-sm shadow-lg shadow-primary/40">
                Pelajari Lebih Lanjut
                <span class="material-symbols-outlined text-base">arrow_forward</span>
              </button>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4">
              <div v-for="stat in stats" :key="stat.value"
                class="bg-white/8 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-5 border border-on-primary/10"
              >
                <div class="text-2xl sm:text-3xl font-bold text-on-primary mb-1">{{ stat.value }}</div>
                <div class="text-xs sm:text-sm text-on-primary/55">{{ stat.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== BEST SELLERS ===== -->
    <section class="py-14 sm:py-20 lg:py-28 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-8 sm:mb-12">
          <div>
            <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-1.5">Pilihan Teratas</p>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary">Paling Laris</h2>
          </div>
          <Link href="/shop" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:text-on-secondary-container transition-colors duration-200 self-start sm:self-auto">
            Lihat semua produk
            <span class="material-symbols-outlined text-base">arrow_forward</span>
          </Link>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
          <div v-for="product in products" :key="product.name"
            class="group bg-surface-container-lowest rounded-xl sm:rounded-2xl overflow-hidden shadow-sm hover:shadow-sm border border-outline-variant/20 flex flex-col"
          >
            <div class="relative aspect-square overflow-hidden bg-surface-container-low">
              <img v-if="product.foto_product" :src="'/storage/' + product.foto_product" :alt="product.name" class="w-full h-full object-cover">
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-container to-surface-container-high">
                <span class="material-symbols-outlined text-outline/40" style="font-size:64px; font-variation-settings:'FILL' 1">{{ product.icon }}</span>
              </div>
            </div>

            <div class="p-3 sm:p-5 flex flex-col flex-grow">
              <div class="flex items-start justify-between gap-1 sm:gap-2 mb-1">
                <h4 class="font-bold text-xs sm:text-sm text-primary leading-snug flex-grow">{{ product.name }}</h4>
                <div class="flex items-center gap-0.5 flex-shrink-0">
                  <span class="material-symbols-outlined text-secondary text-xs" style="font-variation-settings:'FILL' 1">star</span>
                  <span class="text-[10px] sm:text-xs text-on-surface-variant font-medium">{{ product.rating }}</span>
                </div>
              </div>
              <p class="text-[10px] sm:text-xs text-on-surface-variant mb-3">{{ product.weight }}</p>
              <div class="mt-auto flex items-center justify-between">
                <span class="font-bold text-sm sm:text-base text-primary">{{ product.price }}</span>
                <button class="p-1.5 sm:p-2 rounded-lg sm:rounded-xl bg-secondary/10 hover:bg-secondary text-secondary hover:text-on-secondary transition-all duration-200">
                  <span class="material-symbols-outlined text-sm sm:text-base" style="font-variation-settings:'FILL' 1">add_shopping_cart</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== TESTIMONIALS ===== -->
    <section class="py-14 sm:py-20 bg-surface-container-low">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-12">
          <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-1.5">Kecintaan Pelanggan</p>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary">Apa Kata Mereka</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <div v-for="review in reviews" :key="review.name"
            class="bg-surface-container-lowest rounded-xl sm:rounded-2xl p-5 sm:p-7 shadow-sm border border-outline-variant/20 flex flex-col hover:shadow-md transition-shadow duration-300"
          >
            <div class="flex gap-0.5 mb-3 sm:mb-4">
              <span v-for="n in 5" :key="n"
                class="material-symbols-outlined text-secondary text-sm sm:text-base"
                style="font-variation-settings:'FILL' 1">star</span>
            </div>
            <blockquote class="text-xs sm:text-sm text-on-surface-variant leading-relaxed mb-5 sm:mb-6 flex-grow">
              "{{ review.quote }}"
            </blockquote>
            <div class="flex items-center gap-3 pt-4 border-t border-outline-variant/20">
              <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-sm text-on-primary flex-shrink-0"
                :style="{ backgroundColor: review.color }">
                {{ review.name[0] }}
              </div>
              <div class="min-w-0">
                <div class="font-bold text-sm text-primary">{{ review.name }}</div>
                <div class="text-xs text-on-surface-variant truncate">{{ review.location }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </AppLayout>
</template>


