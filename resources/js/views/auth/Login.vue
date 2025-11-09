<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-primary-50 via-secondary-50 to-primary-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-md w-full">
      <!-- Logo and Title -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-2xl shadow-glow-purple-lg mb-4 animate-pulse-slow">
          <span class="text-white font-bold text-3xl">N</span>
        </div>
        <h2 class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
          Welcome to Nanaska AI
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Master preseen case studies with AI-powered learning
        </p>
      </div>

      <!-- Login Card -->
      <div class="card-glow">
        <h3 class="text-2xl font-bold text-center mb-6 text-gray-900 dark:text-white">
          Sign In
        </h3>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg text-sm">
          {{ errorMessage }}
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Email Address
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              class="input-field"
              placeholder="your@email.com"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Password
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              class="input-field"
              placeholder="••••••••"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Sign In</span>
            <span v-else>Signing in...</span>
          </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Don't have an account?
            <router-link to="/register" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
              Register here
            </router-link>
          </p>
        </div>
      </div>

      <!-- Theme Toggle -->
      <div class="mt-6 text-center">
        <button
          @click="toggleTheme"
          class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:shadow-glow-purple transition-all duration-200"
        >
          <svg v-if="isDark" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <svg v-else class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
          </svg>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ isDark ? 'Light Mode' : 'Dark Mode' }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from '@/stores/theme';

const router = useRouter();
const authStore = useAuthStore();
const themeStore = useThemeStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const isDark = computed(() => themeStore.isDark);

const toggleTheme = () => {
  themeStore.toggleTheme();
};

const handleLogin = async () => {
  if (!email.value || !password.value) {
    errorMessage.value = 'Please fill in all fields';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  const result = await authStore.login(email.value, password.value);

  loading.value = false;

  if (result.success) {
    router.push('/dashboard');
  } else {
    errorMessage.value = result.message;
  }
};
</script>
