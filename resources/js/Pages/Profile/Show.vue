<script setup>
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '../Layouts/App.vue';

const props = defineProps({
    user: Object,
    errors: Object
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    no_hp: props.user.customer.no_hp,
    alamat: props.user.customer.alamat,
});

const formPassword = useForm({
    old_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(`/profile`, {
        preserveScroll: true,
    });
};

const submitPassword = () => {
    formPassword.patch(`/profile/password`, {
        preserveScroll: true,
        onSuccess: () => formPassword.reset(),
    });
};

const page = usePage();
const authUser = page.props.auth.user || {};

const activeMenu = ref('profile');

const menus = [
  { id: 'profile',   label: 'Informasi Profil', icon: 'account_circle' },
  { id: 'security',  label: 'Ubah Password',    icon: 'lock' },
];

const userInitials = computed(() => {
  const parts = form.name ? form.name.split(' ') : [];
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return parts[0] ? parts[0][0].toUpperCase() : 'U';
});
</script>

<template>
  <AppLayout>
    <section class="bg-primary py-10 sm:py-14">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-on-primary/50 mb-4">
          <Link href="/" class="hover:text-on-primary transition-colors duration-200">Home</Link>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-on-primary font-medium">Profil Saya</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-primary">Profil Saya</h1>
        <p class="text-sm text-on-primary/60 mt-1">Kelola data informasi diri, alamat, dan keamanan akun Anda</p>
      </div>
    </section>

    <section class="py-8 sm:py-12 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
          
          <!-- SIDEBAR: AVATAR CARD & MENU TABS -->
          <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-6">
            
            <!-- Loyalty & Avatar Card -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-5 text-center shadow-sm">
              <div class="w-20 h-20 mx-auto rounded-full bg-secondary/15 border-2 border-secondary flex items-center justify-center text-secondary font-bold text-2xl mb-4">
                {{ userInitials }}
              </div>
              <h3 class="text-base sm:text-lg font-bold text-primary">{{ form.name }}</h3>
              <p class="text-xs text-on-surface-variant mb-4">{{ form.email }}</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-2 shadow-sm">
              <nav class="flex flex-col gap-1">
                <button v-for="menu in menus" :key="menu.id"
                  @click="activeMenu = menu.id"
                  class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-left transition-all duration-200"
                  :class="activeMenu === menu.id
                    ? 'bg-secondary/10 text-secondary'
                    : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                >
                  <span class="material-symbols-outlined text-lg" :style="activeMenu === menu.id ? 'font-variation-settings:\'FILL\' 1' : ''">{{ menu.icon }}</span>
                  <span class="flex-grow">{{ menu.label }}</span>
                  <span class="material-symbols-outlined text-xs text-outline/50">chevron_right</span>
                </button>
              </nav>
            </div>

          </div>

          <!-- DETAIL CONTENT PANEL -->
          <div class="flex-grow bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-5 sm:p-8 shadow-sm">
            
            <!-- PANEL: INFORMASI PROFIL -->
            <div v-show="activeMenu === 'profile'">
              <h2 class="text-lg sm:text-xl font-bold text-primary mb-1">Detail Informasi Diri</h2>
              <p class="text-xs sm:text-sm text-on-surface-variant mb-6 border-b border-outline-variant/20 pb-4">Pastikan data kontak Anda aktif untuk kelancaran pengiriman kopi.</p>
              
              <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nama Lengkap</label>
                    <input type="text" v-model="form.name" required
                      class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Email</label>
                    <input type="email" v-model="form.email" required
                      class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" 
                      :class="form.errors.email ? 'border-error focus:border-error focus:ring-error/20' : 'border-outline-variant focus:border-secondary'"/>
                    <p v-if="form.errors.email" class="mt-1 text-xs text-error">{{ form.errors.email }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">No Handphone</label>
                    <input type="text" v-model="form.no_hp" required
                      class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Alamat Pengiriman Utama</label>
                  <textarea rows="3" v-model="form.alamat" required
                    class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200 placeholder-outline"></textarea>
                </div>

                <div class="pt-4 border-t border-outline-variant/20 flex justify-end">
                  <button type="submit" :disabled="form.processing"
                    class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-bold px-6 py-3 rounded-xl transition-all duration-200 text-sm shadow-sm disabled:opacity-60">
                    <span class="material-symbols-outlined text-base">save</span>
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- PANEL: UBAH PASSWORD -->
            <div v-show="activeMenu === 'security'">
              <h2 class="text-lg sm:text-xl font-bold text-primary mb-1">Ubah Password</h2>
              <p class="text-xs sm:text-sm text-on-surface-variant mb-6 border-b border-outline-variant/20 pb-4">Gunakan password yang kuat demi menjaga keamanan akun Anda.</p>
              
              <form @submit.prevent="submitPassword" class="space-y-5">
                <div class="max-w-md space-y-5">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Password Lama</label>
                    <input type="password" v-model="formPassword.old_password" required
                      class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Password Baru</label>
                    <input type="password" v-model="formPassword.password" required
                      class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Konfirmasi Password Baru</label>
                    <input type="password" v-model="formPassword.password_confirmation" required
                      class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all duration-200" />
                  </div>
                </div>

                <div class="pt-4 border-t border-outline-variant/20 flex justify-end">
                  <button type="submit" :disabled="formPassword.processing"
                    class="inline-flex items-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-bold px-6 py-3 rounded-xl transition-all duration-200 text-sm shadow-sm disabled:opacity-60">
                    <span class="material-symbols-outlined text-base">lock_reset</span>
                    {{ formPassword.processing ? 'Mengubah...' : 'Perbarui Password' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>


