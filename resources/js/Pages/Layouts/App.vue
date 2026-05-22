<template>
  <div class="bg-background text-on-background antialiased min-h-screen flex flex-col">

    <!-- ===== NAVBAR ===== -->
    <header class="shadow-sm bg-surface sticky top-0 z-50">
      <div class="flex justify-between items-center w-full h-20 px-6 max-w-7xl mx-auto">

        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2.5">
          <span class="font-bold text-lg sm:text-xl text-primary" style="font-family:'Playfair Display',serif;">Bay &amp; Coffee</span>
        </Link>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-6">
          <Link :class="page.url === '/' ? 'text-sm font-semibold text-secondary relative pb-1 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-secondary after:transition-all after:duration-300' : 'text-sm font-semibold text-on-surface-variant hover:text-secondary relative pb-1 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:w-0 hover:after:w-full after:h-[2px] after:bg-secondary after:transition-all after:duration-300 cursor-pointer'" href="/">Home</Link>
          <Link :class="page.url.startsWith('/shop') ? 'text-sm font-semibold text-secondary relative pb-1 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-secondary after:transition-all after:duration-300' : 'text-sm font-semibold text-on-surface-variant hover:text-secondary relative pb-1 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:w-0 hover:after:w-full after:h-[2px] after:bg-secondary after:transition-all after:duration-300 cursor-pointer'" href="/shop">Shop</Link>
          <Link :class="page.url.startsWith('/transaksi') ? 'text-sm font-semibold text-secondary relative pb-1 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-secondary after:transition-all after:duration-300' : 'text-sm font-semibold text-on-surface-variant hover:text-secondary relative pb-1 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:w-0 hover:after:w-full after:h-[2px] after:bg-secondary after:transition-all after:duration-300 cursor-pointer'" href="/transaksi">Transaksi</Link>
        </nav>

        <!-- Right Icons -->
        <div class="flex items-center gap-4 text-primary">
          <!-- Shopping Bag Icon aligned perfectly -->
          <Link href="/cart" class="hover:text-secondary transition-colors duration-200 flex items-center justify-center w-10 h-10 rounded-full hover:bg-on-surface-variant/5">
            <span class="material-symbols-outlined">shopping_bag</span>
          </Link>

          <!-- Masuk (Login) Button or User Profile Dropdown -->
          <Link v-if="!page.props.auth.user" href="/login" class="hidden md:flex items-center justify-center px-4 py-2 text-sm font-bold text-on-secondary bg-secondary hover:bg-secondary/90 rounded-full transition-colors duration-200">
            Masuk
          </Link>

          <!-- Sleek Dropdown for logged in user -->
          <div v-else class="relative hidden md:block">
            <button 
              @click.stop="dropdownOpen = !dropdownOpen" 
              class="flex items-center gap-1.5 hover:text-secondary transition-colors duration-200 focus:outline-none py-1.5 px-3 rounded-full hover:bg-on-surface-variant/5 cursor-pointer"
            >
              <span class="material-symbols-outlined">person</span>
              <span class="text-xs font-semibold max-w-[100px] truncate">{{ page.props.auth.user.name }}</span>
              <span class="material-symbols-outlined text-xs transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <!-- Floating Dropdown Menu -->
            <div 
              v-show="dropdownOpen" 
              class="absolute right-0 mt-2.5 w-56 bg-surface-container-highest/95 backdrop-blur-md border border-outline-variant/30 rounded-2xl p-2.5 shadow-xl z-50 flex flex-col gap-1 transition-all duration-200 origin-top-right text-left"
            >
              <div class="px-3 py-2 border-b border-outline-variant/20 mb-1">
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">Masuk sebagai</p>
                <p class="text-sm font-extrabold text-primary truncate">{{ page.props.auth.user.name }}</p>
                <p class="text-xs text-on-surface-variant truncate">{{ page.props.auth.user.email }}</p>
              </div>
              
              <Link 
                href="/profile" 
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface hover:bg-secondary/10 hover:text-secondary transition-all duration-200 text-left cursor-pointer"
                @click="dropdownOpen = false"
              >
                <span class="material-symbols-outlined text-sm">account_circle</span>
                Profil Saya
              </Link>
              
              <Link 
                href="/transaksi" 
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-on-surface hover:bg-secondary/10 hover:text-secondary transition-all duration-200 text-left cursor-pointer"
                @click="dropdownOpen = false"
              >
                <span class="material-symbols-outlined text-sm">receipt_long</span>
                Riwayat Transaksi
              </Link>
              
              <Link 
                href="/logout" 
                method="post" 
                as="button" 
                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-error hover:bg-error/10 transition-all duration-200 text-left border-0 bg-transparent cursor-pointer"
                @click="dropdownOpen = false"
              >
                <span class="material-symbols-outlined text-sm">logout</span>
                Keluar
              </Link>
            </div>
          </div>

          <!-- Hamburger mobile -->
          <button class="md:hidden hover:text-secondary transition-colors duration-200 flex items-center justify-center w-10 h-10 rounded-full hover:bg-on-surface-variant/5" @click="mobileOpen = !mobileOpen">
            <span class="material-symbols-outlined">{{ mobileOpen ? 'close' : 'menu' }}</span>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-show="mobileOpen" class="md:hidden bg-surface border-t border-outline-variant/20">
        <nav class="flex flex-col px-6 py-4 gap-1">
          <Link v-for="link in navLinks" :key="link.label"
            :href="link.href"
            class="py-3 px-2 text-sm font-semibold border-b border-outline-variant/10 last:border-0 transition-colors duration-200"
            :class="page.url === link.href ? 'text-secondary' : 'text-on-surface-variant hover:text-secondary'"
            @click="mobileOpen = false"
          >{{ link.label }}</Link>
          <Link v-if="!page.props.auth.user" href="/login" class="py-3 px-2 text-sm font-semibold text-on-surface-variant hover:text-secondary transition-colors duration-200" @click="mobileOpen = false">Login</Link>
          <Link v-else href="/logout" method="post" as="button" class="w-full text-left py-3 px-2 text-sm font-semibold text-error hover:text-error/80 transition-colors duration-200 border-0 bg-transparent cursor-pointer" @click="mobileOpen = false">Keluar</Link>
        </nav>
      </div>
    </header>

    <!-- ===== PAGE CONTENT ===== -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-primary text-on-primary">
      <div class="max-w-7xl mx-auto px-6 sm:px-8 py-12 sm:py-16">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-8 lg:gap-12 pb-10 border-b border-white/10">

          <!-- Left side: Brand and description -->
          <div class="max-w-md">
            <div class="flex items-center gap-2.5 mb-4">
              <span class="font-bold text-xl tracking-tight text-on-primary" style="font-family:'Playfair Display',serif;">
                Bay &amp; Coffee
              </span>
            </div>
            <p class="text-sm text-on-primary/70 leading-relaxed mb-6">
              Artisanal small-batch roasters. Sourced from sustainable farms around the world, delivered fresh to your door.
            </p>
            <!-- Navigation links -->
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
              <Link href="/" class="text-sm font-semibold text-on-primary/70 hover:text-on-primary transition-colors duration-200">Home</Link>
              <Link href="/shop" class="text-sm font-semibold text-on-primary/70 hover:text-on-primary transition-colors duration-200">Shop</Link>
              <a href="#" class="text-sm font-semibold text-on-primary/70 hover:text-on-primary transition-colors duration-200">Our Story</a>
              <a href="#" class="text-sm font-semibold text-on-primary/70 hover:text-on-primary transition-colors duration-200">Contact</a>
            </div>
          </div>

          <!-- Right side: Sleek Newsletter & Socials -->
          <div class="w-full lg:w-80">
            <h4 class="text-sm font-bold uppercase tracking-widest text-on-primary/50 mb-3">Newsletter</h4>
            <p class="text-xs text-on-primary/70 mb-4">Subscribe for exclusive offers and fresh roast updates.</p>
            <form @submit.prevent class="flex gap-2">
              <input
                type="email"
                placeholder="your@email.com"
                class="flex-grow bg-white/10 border border-white/20 rounded-xl px-4 py-2 text-sm text-on-primary placeholder-on-primary/40 focus:outline-none focus:border-secondary focus:bg-white/15 transition-all duration-200"
              />
              <button type="submit" class="bg-secondary hover:bg-secondary/90 text-on-secondary text-sm font-semibold px-4 rounded-xl transition-all duration-200">
                Join
              </button>
            </form>

            <!-- Socials -->
            <div class="flex items-center gap-3 mt-6">
              <a v-for="s in socials" :key="s.label"
                href="#"
                class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/15 flex items-center justify-center transition-all duration-200 group"
                :aria-label="s.label"
              >
                <span class="material-symbols-outlined text-sm text-on-primary/70 group-hover:text-on-primary" style="font-variation-settings:'FILL' 1">{{ s.icon }}</span>
              </a>
            </div>
          </div>

        </div>

        <!-- Bottom bar -->
        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
          <p class="text-xs text-on-primary/40">© {{ new Date().getFullYear() }} Bay Coffee. All rights reserved.</p>
          <div class="flex items-center gap-5">
            <a href="#" class="text-xs text-on-primary/40 hover:text-on-primary transition-colors duration-200">Privacy Policy</a>
            <a href="#" class="text-xs text-on-primary/40 hover:text-on-primary transition-colors duration-200">Terms of Service</a>
          </div>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, watch } from 'vue';
import Swal from 'sweetalert2';

const page = usePage()

const mobileOpen = ref(false);
const dropdownOpen = ref(false);

// SweetAlert2 Toast instance
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  },
});

// Watch for flash session messages from Laravel
watch(
  () => page.props.flash,
  (flash) => {
    if (flash && flash.success) {
      Toast.fire({ icon: 'success', title: flash.success });
    } else if (flash && flash.error) {
      Toast.fire({ icon: 'error', title: flash.error });
    } else if (flash && flash.warning) {
      Toast.fire({ icon: 'warning', title: flash.warning });
    } else if (flash && flash.message) {
      Toast.fire({ icon: 'info', title: flash.message });
    }
  },
  { deep: true, immediate: true }
);

// Watch for validation errors
watch(
  () => page.props.errors,
  (errors) => {
    if (errors && Object.keys(errors).length > 0) {
      const firstErrorKey = Object.keys(errors)[0];
      Toast.fire({ icon: 'error', title: errors[firstErrorKey] });
    }
  },
  { deep: true }
);

// Close dropdown on click outside
const closeDropdown = (e) => {
  if (!e.target.closest('.relative')) {
    dropdownOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  window.removeEventListener('click', closeDropdown);
});

const navLinks = [
  { label: 'Home', href: '/' },
  { label: 'Shop', href: '/shop' },
  { label: 'Transaksi', href: '/transaksi' },
  { label: 'Profil Saya', href: '/profile' },
];

const socials = [
  { label: 'Instagram', icon: 'photo_camera' },
  { label: 'Facebook',  icon: 'thumb_up'     },
  { label: 'Twitter',   icon: 'chat'          },
];
</script>

<style scoped>
.h-18 { height: 4.5rem; }
</style>