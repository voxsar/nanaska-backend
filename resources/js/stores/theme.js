import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(false);

  // Initialize from localStorage
  const initTheme = () => {
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark';
    updateDocumentClass();
  };

  // Update document class for Tailwind dark mode
  const updateDocumentClass = () => {
    if (isDark.value) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  // Toggle theme
  const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    updateDocumentClass();
  };

  // Watch for changes and persist
  watch(isDark, (newValue) => {
    localStorage.setItem('theme', newValue ? 'dark' : 'light');
    updateDocumentClass();
  });

  // Initialize on store creation
  initTheme();

  return {
    isDark,
    toggleTheme,
  };
});
