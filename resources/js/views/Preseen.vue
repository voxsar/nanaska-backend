<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Pre-seen Case Studies
      </h1>
      
      <div class="card mb-6">
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Access CIMA pre-seen documents and case studies. These materials are provided before the examination and form the basis for your case study preparation.
        </p>
        <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
          <p class="text-sm text-primary-800 dark:text-primary-300">
            <span class="font-semibold">Note:</span> Pre-seen documents will be loaded by your administrator. Check back regularly for updates.
          </p>
        </div>
      </div>

      <!-- Documents List -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="documents.length === 0" class="card text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No documents available</h3>
        <p class="text-gray-600 dark:text-gray-400">Pre-seen documents will appear here once uploaded by your administrator.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="doc in documents" :key="doc.id" class="card-glow hover:scale-105 transition-transform duration-200">
          <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ doc.name }}</h3>
              <p v-if="doc.company_name" class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ doc.company_name }}</p>
              <p v-if="doc.description" class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ doc.description }}</p>
              <p v-if="doc.page_count" class="text-xs text-gray-500 dark:text-gray-500">{{ doc.page_count }} pages</p>
              <div v-if="doc.file_path" class="mt-3">
                <a :href="`/storage/${doc.file_path}`" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">View Document</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const documents = ref([]);
const loading = ref(true);

onMounted(async () => {
  await loadPreSeenDocuments();
});

const loadPreSeenDocuments = async () => {
  loading.value = true;
  try {
    const response = await api.get('/pre-seen-documents');
    if (response.data.success) {
      documents.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to load pre-seen documents:', error);
  } finally {
    loading.value = false;
  }
};
</script>
