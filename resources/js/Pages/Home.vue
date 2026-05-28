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
  { icon: 'local_shipping',    label: 'Free Shipping',          sub: 'On orders over Rp 400.000'  },
  { icon: 'timer',             label: 'Roasted Fresh',          sub: 'Ships within 48 hrs' },
  { icon: 'eco',               label: 'Sustainably Sourced',    sub: 'Direct trade farms'  },
  { icon: 'workspace_premium', label: 'Satisfaction Guarantee', sub: 'Or full refund'      },
];

const collections = [
  {
    name: 'Light Roast',
    tag: 'Bright & Vibrant',
    desc: 'Floral, citrus, and berry notes. Perfect for pour-over and filter brews.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCmZvw5twNUS227wksoxR7lwpIMKw-uNkeYnq_-vD_k2iLW-nUkiebkTEwTURe8W0RomTjAG877jAbE5G3eAsUVJ4uZxrw8hz4sK1WCKaF_wEI9sp120bC0zRYP242J6FMoofokqLISkcztapYTYI1g0Wh21Ikggb6fNFQYwR7scgLyHhL3xJK3Zyl-RllQNBaQ4LkX9MEt0c6NcO8GEJoVnG4ILsHK-re2Kc6ZI-78DAvvY2dZhu5mgVVVSGDzBIRnAnVrcqtyBcg',
  },
  {
    name: 'Medium Roast',
    tag: 'Balanced & Smooth',
    desc: 'Caramel, chocolate, and toasted nuts. A crowd favourite for any brew method.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB2Pb6HcvAmDiZmgfuFS9OencFP5pwh-dJZ13As8Kny5xnwTj674bVxA-qa4qD6_23pfHH4lNa1ZwgA-6TCFlTy5jWrr484inalYHMuhUV9u6fYxXpdG2Bes-XPLEctO8zx08R4uzElOjuh4y6eZclCL5mU9dCx_Bk84ZNuKh6ZYeUGbam_wjyIty7_84ESONIjFAwMEcSy9AEorAZu5VXSTfV_ToC4PMVF7yt8aeTfXtcq65vgjZKPQTrTA_aalH1maWD9fr42Gzo',
  },
  {
    name: 'Dark Roast',
    tag: 'Bold & Intense',
    desc: 'Smoky, dark cocoa and espresso character. Ideal for strong morning brews.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA2_fclPdI7Mf2H1W0RDS2TI4hbvgR5Z3GAAyBKhRpf5aXcThkkG2HfSyIt09-01sngvka4Yd-S31LT2O-8V0rCvj-FOP4lEA0QiHKVpWfOD4OfS_6unOw3gr4v2GoLe558hLxBjR3xC3KrATPGag-O9kZPTQaYOJ8Hx8XxInteKYVpu81e2VRgvDzwRKI5YTyGPTZkhfE_jlmsnGdHigLMK6Z1sP0h33biK1yYonte32uVA69mMZ708l5NmJCXMIEx2LdAUheHvdM',
  },
];

const stats = [
  { value: '12+',  label: 'Countries sourced'  },
  { value: '50k+', label: 'Happy customers'    },
  { value: '48hr', label: 'From roast to door' },
  { value: '100%', label: 'Traceable origins'  },
];

const reviews = [
  { name: 'Sarah M.', location: 'New York, USA',  quote: 'The Ethiopian Yirgacheffe is absolutely stunning — floral and bright, unlike anything I\'ve had from a local café.',                                          color: '#7d562d' },
  { name: 'James K.', location: 'London, UK',     quote: 'I\'ve tried dozens of subscription services. Ebay & Coffee is the only one I keep coming back to. The freshness makes all the difference.',              color: '#271310' },
  { name: 'Priya R.', location: 'Melbourne, AUS', quote: 'Sustainable sourcing that you can actually taste. Knowing the farm my beans came from makes every sip feel meaningful.',                                   color: '#745853' },
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
            <span class="text-[10px] sm:text-xs font-semibold text-on-primary tracking-widest uppercase">New Harvest Available</span>
          </div>

          <!-- Heading -->
          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-on-primary leading-[1.12] mb-5 sm:mb-6">
            Freshly Roasted.<br/>
            <span class="text-primary-fixed-dim">Delivered to Your Door.</span>
          </h1>

          <!-- Subtitle -->
          <p class="text-sm sm:text-base lg:text-lg text-on-primary/75 mb-8 sm:mb-10 leading-relaxed">
            Discover your perfect cup with our artisanal, small-batch roasts — sourced from sustainable farms and delivered within 48 hours of roasting.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <Link href="/shop" class="inline-flex items-center justify-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl transition-all duration-200 shadow-lg shadow-primary/40 hover:shadow-xl hover:-translate-y-0.5 text-sm sm:text-base w-full sm:w-auto">
              <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">shopping_bag</span>
              Shop Now
            </Link>
            <button class="inline-flex items-center justify-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-on-primary font-semibold px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl border border-white/30 transition-all duration-200 text-sm sm:text-base w-full sm:w-auto">
              <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 0">play_circle</span>
              Our Story
            </button>
          </div>
        </div>
      </div>

      <div class="hidden sm:flex absolute bottom-8 left-1/2 -translate-x-1/2 flex-col items-center gap-1 text-on-primary/40 animate-bounce">
        <span class="text-[10px] tracking-widest uppercase font-medium">Scroll</span>
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
            <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-1.5">Our Selection</p>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary">Featured Collections</h2>
          </div>
          <a href="#" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:text-on-secondary-container transition-colors duration-200 self-start sm:self-auto">
            View all
            <span class="material-symbols-outlined text-base">arrow_forward</span>
          </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <div v-for="col in collections" :key="col.name"
            class="group relative overflow-hidden rounded-2xl cursor-pointer"
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
              <button class="mt-3 sm:mt-4 inline-flex items-center gap-2 bg-white/20 hover:bg-white/35 backdrop-blur-sm text-on-primary text-xs sm:text-sm font-semibold px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl border border-white/30 transition-all duration-200 sm:translate-y-2 sm:opacity-0 sm:group-hover:translate-y-0 sm:group-hover:opacity-100">
                Explore
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
              </button>
            </div>
          </div>
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
              <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-3">Why Choose Us</p>
              <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary mb-4 sm:mb-5 leading-tight">
                From Farm to Cup —<br/>Uncompromised Quality
              </h2>
              <p class="text-on-primary/65 text-sm sm:text-base leading-relaxed mb-6 sm:mb-8">
                We partner directly with farmers across Ethiopia, Colombia, Guatemala, and beyond. Every bean is hand-selected, ethically sourced, and roasted in small batches to ensure the freshest possible flavour profile.
              </p>
              <button class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold px-6 py-3 rounded-xl transition-all duration-200 text-sm shadow-lg shadow-primary/40">
                Learn More
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
            <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-1.5">Top Picks</p>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary">Best Sellers</h2>
          </div>
          <Link href="/shop" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:text-on-secondary-container transition-colors duration-200 self-start sm:self-auto">
            View all products
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
          <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary mb-1.5">Customer Love</p>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-primary">What People Are Saying</h2>
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


