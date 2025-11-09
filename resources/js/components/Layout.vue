<template>
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <!-- Logo -->
            <router-link to="/dashboard" class="flex items-center space-x-2">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center shadow-glow-purple">
                <span class="text-white font-bold text-xl">N</span>
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                Nanaska AI
              </span>
            </router-link>

            <!-- Navigation Links -->
            <div class="hidden md:flex ml-10 space-x-4">
              <router-link 
                v-for="link in navLinks" 
                :key="link.path"
                :to="link.path"
                class="px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                :class="isActive(link.path) 
                  ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' 
                  : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
              >
                {{ link.name }}
              </router-link>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
              :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
            >
              <svg v-if="isDark" class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
              <svg v-else class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
              </svg>
            </button>

            <!-- User Menu -->
            <div class="relative" v-if="student">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-medium">{{ student.name.charAt(0).toUpperCase() }}</span>
                </div>
                <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ student.name }}</span>
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-1 z-50"
              >
                <button
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                >
                  Logout
                </button>
              </div>
            </div>

            <!-- Mobile Menu Button -->
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="showMobileMenu" class="md:hidden py-4 border-t border-gray-200 dark:border-gray-700 mt-2">
          <router-link
            v-for="link in navLinks"
            :key="link.path"
            :to="link.path"
            @click="showMobileMenu = false"
            class="block px-3 py-2 rounded-md text-base font-medium mb-1 transition-all duration-200"
            :class="isActive(link.path)
              ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20'
              : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
          >
            {{ link.name }}
          </router-link>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
          © {{ new Date().getFullYear() }} Nanaska AI. Master preseen case studies with AI-powered learning.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from '@/stores/theme';

const route = useRoute();
const authStore = useAuthStore();
const themeStore = useThemeStore();

const showUserMenu = ref(false);
const showMobileMenu = ref(false);

const student = computed(() => authStore.student);
const isDark = computed(() => themeStore.isDark);

const navLinks = [
  { name: 'Dashboard', path: '/dashboard' },
  { name: 'Pre-seen', path: '/preseen' },
  { name: 'Questions', path: '/questions' },
  { name: 'Theory Models', path: '/theory-models' },
  { name: 'Past Papers', path: '/past-papers' },
  { name: 'Practice', path: '/practice' },
  { name: 'Mock Exams', path: '/mock-exams' },
];

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/');
};

const toggleTheme = () => {
  themeStore.toggleTheme();
};

const handleLogout = () => {
  showUserMenu.value = false;
  authStore.logout();
};
</script>
