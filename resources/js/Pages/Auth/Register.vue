<template>
  <div class="min-h-screen bg-background flex">

    <!-- ── Left: Image Panel (hidden on mobile) ── -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-primary">
      <img
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2_fclPdI7Mf2H1W0RDS2TI4hbvgR5Z3GAAyBKhRpf5aXcThkkG2HfSyIt09-01sngvka4Yd-S31LT2O-8V0rCvj-FOP4lEA0QiHKVpWfOD4OfS_6unOw3gr4v2GoLe558hLxBjR3xC3KrATPGag-O9kZPTQaYOJ8Hx8XxInteKYVpu81e2VRgvDzwRKI5YTyGPTZkhfE_jlmsnGdHigLMK6Z1sP0h33biK1yYonte32uVA69mMZ708l5NmJCXMIEx2LdAUheHvdM"
        alt="Coffee background"
        class="absolute inset-0 w-full h-full object-cover opacity-60"
      />
      <div class="absolute inset-0 bg-gradient-to-br from-primary/95 via-primary/80 to-primary/50"></div>
      <div class="relative z-10 flex flex-col justify-between p-12 h-full">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2.5">
          <img src="/assets/Logo Bay Coffee.png" alt="Bay Coffee Logo" class="w-8 h-8 rounded-full">
          <span class="font-bold text-lg text-on-primary tracking-tight" style="font-family:'Playfair Display',serif;">Bay <span class="font-medium italic">Coffee</span></span>
        </Link>
      </div>
    </div>

    <!-- ── Right: Form Panel ── -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-12 lg:px-16 py-12">

      <!-- Mobile logo -->
      <div class="flex lg:hidden justify-center gap-2.5 mb-12">
        <Link href="/" class="flex items-center gap-2 logo-brand">
          <img src="/assets/Logo Bay Coffee.png" alt="Bay Coffee Logo" class="w-8 h-8 rounded-full">
          <span class="font-bold text-xl text-primary tracking-tight" style="font-family:'Playfair Display',serif;">Bay <span class="font-medium italic logo-coffee-gradient">Coffee</span></span>
        </Link>
      </div>

      <div class="w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <p class="text-xs font-bold uppercase tracking-widest text-secondary mb-2">Buat akun baru</p>
          <h1 class="text-2xl sm:text-4xl font-bold text-primary mb-2"">Daftar Sekarang</h1>
          <p class="text-xs text-on-surface-variant">Sudah punya akun?
            <a href="/login" class="font-semibold text-secondary hover:underline">Masuk di sini</a>
          </p>
        </div>

        <!-- Form (No backend logic) -->
        <form @submit.prevent="submit" class="space-y-5">

          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-primary mb-1.5">Nama Lengkap</label>
            <div class="relative">
              <input
                required
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Nama lengkap kamu"
                autocomplete="name"
                class="w-full bg-surface border rounded-xl pl-4 py-3 text-sm text-on-surface placeholder-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all duration-200"
                :class="form.errors.name ? 'border-error focus:border-error focus:ring-error/20' : 'border-outline-variant focus:border-secondary'"
              />
              <p v-if="form.errors.name" class="mt-1 text-xs text-error">{{ form.errors.name }}</p>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-1.5">Email</label>
            <div class="relative">
              <input
                required
                id="email"
                v-model="form.email"
                type="email"
                placeholder="nama@email.com"
                autocomplete="email"
                class="w-full bg-surface border rounded-xl pl-4  py-3 text-sm text-on-surface placeholder-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all duration-200"
                :class="form.errors.email ? 'border-error focus:border-error focus:ring-error/20' : 'border-outline-variant focus:border-secondary'"
              />
              <p v-if="form.errors.email" class="mt-1 text-xs text-error">{{ form.errors.email }}</p>
            </div>
          </div>

          <!-- No HP -->
          <div>
            <label for="no_hp" class="block text-sm font-semibold text-primary mb-1.5">No HP</label>
            <div class="relative">
              <input
                required
                id="no_hp"
                v-model="form.no_hp"
                type="text"
                placeholder="08123456789"
                autocomplete="no_hp"
                class="w-full bg-surface border rounded-xl pl-4  py-3 text-sm text-on-surface placeholder-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all duration-200"
                :class="form.errors.no_hp ? 'border-error focus:border-error focus:ring-error/20' : 'border-outline-variant focus:border-secondary'"
              />
              <p v-if="form.errors.no_hp" class="mt-1 text-xs text-error">{{ form.errors.no_hp }}</p>
            </div>
          </div>

          <!-- Alamat -->
          <div>
            <label for="alamat" class="block text-sm font-semibold text-primary mb-1.5">Alamat</label>
            <div class="relative">
              <input
                required
                id="alamat"
                v-model="form.alamat"
                type="text"
                placeholder="Masukkan Alamat Anda"
                autocomplete="alamat"
                class="w-full bg-surface border rounded-xl pl-4  py-3 text-sm text-on-surface placeholder-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all duration-200"
                :class="form.errors.alamat ? 'border-error focus:border-error focus:ring-error/20' : 'border-outline-variant focus:border-secondary'"
              />
              <p v-if="form.errors.alamat" class="mt-1 text-xs text-error">{{ form.errors.alamat }}</p>
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-primary mb-1.5">Password</label>
            <div class="relative">
              <input
                required
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Minimal 8 karakter"
                autocomplete="new-password"
                class="w-full bg-surface border rounded-xl pl-4 py-3 text-sm text-on-surface placeholder-outline focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all duration-200"
                :class="form.errors.password ? 'border-error focus:border-error focus:ring-error/20' : 'border-outline-variant focus:border-secondary'"
              />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-6.5 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors duration-200">
                <span class="material-symbols-outlined text-base">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>

            <!-- UI Strength indicator -->
            <div v-if="form.password" class="mt-2 flex gap-1">
              <div v-for="n in 4" :key="n" class="h-1 flex-1 rounded-full transition-all duration-300"
                :class="passwordStrength >= n ? strengthColor : 'bg-outline-variant/30'"
              ></div>
            </div>
            <p v-if="form.password" class="mt-1 text-xs" :class="strengthTextColor">{{ strengthLabel }}</p>
            <p v-if="form.errors.password" class="mt-1 text-xs text-error">{{ form.errors.password }}</p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-primary mb-1.5">Konfirmasi Password</label>
            <div class="relative">
              <input
                required
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="Ulangi password"
                autocomplete="new-password"
                class="w-full bg-surface border rounded-xl pl-4 py-3 text-sm text-on-surface placeholder-outline focus:outline-none transition-all duration-200"
                :class="confirmMismatch || form.errors.password_confirmation
                  ? 'border-error focus:border-error focus:ring-2 focus:ring-error/20'
                  : 'border-outline-variant focus:border-secondary focus:ring-2 focus:ring-secondary/20'"
              />
              <button type="button" @click="showConfirm = !showConfirm"
                class="absolute right-3 top-6.5 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors duration-200">
                <span class="material-symbols-outlined text-base">{{ showConfirm ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
            <p v-if="confirmMismatch" class="mt-1.5 text-xs text-error">Password tidak cocok.</p>
            <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-error">{{ form.errors.password_confirmation }}</p>
          </div>

          <!-- Terms -->
          <div class="flex items-start gap-2.5">
            <input id="terms" v-model="agreedToTerms" type="checkbox"
              class="w-4 h-4 rounded border-outline-variant text-secondary focus:ring-secondary/30 cursor-pointer mt-0.5"
            />
            <label for="terms" class="text-xs text-on-surface-variant cursor-pointer leading-relaxed select-none">
              Saya setuju dengan
              <Link href="#" class="font-semibold text-secondary hover:underline">Syarat &amp; Ketentuan</Link>
              dan
              <Link href="#" class="font-semibold text-secondary hover:underline">Kebijakan Privasi</Link>
            </label>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="!agreedToTerms"
            class="w-full flex items-center justify-center gap-2 bg-secondary hover:bg-secondary/90 text-on-secondary font-semibold py-3.5 rounded-xl shadow-md shadow-secondary/20 hover:shadow-md text-sm disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200"
          >
            <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1">how_to_reg</span>
            Buat Akun
          </button>

        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const showPassword = ref(false);
const showConfirm  = ref(false);
const agreedToTerms = ref(false);

const form = useForm({
  name: '',
  email: '',
  no_hp: '',
  alamat: '',
  password: '',
  password_confirmation: '',
});

// Password strength (UI only)
const passwordStrength = computed(() => {
  const p = form.password;
  if (!p) return 0;
  let score = 0;
  if (p.length >= 8)          score++;
  if (/[A-Z]/.test(p))        score++;
  if (/[0-9]/.test(p))        score++;
  if (/[^A-Za-z0-9]/.test(p)) score++;
  return score;
});

const strengthColor = computed(() => {
  if (passwordStrength.value <= 1) return 'bg-error';
  if (passwordStrength.value === 2) return 'bg-yellow-500';
  if (passwordStrength.value === 3) return 'bg-green-400';
  return 'bg-green-600';
});

const strengthTextColor = computed(() => {
  if (passwordStrength.value <= 1) return 'text-error';
  if (passwordStrength.value === 2) return 'text-yellow-600';
  return 'text-green-700';
});

const strengthLabel = computed(() => {
  const labels = ['', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
  return labels[passwordStrength.value] || '';
});

const confirmMismatch = computed(() =>
  form.password_confirmation.length > 0 && form.password !== form.password_confirmation
);

function submit() {
  if (confirmMismatch.value) return;
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}
</script>
