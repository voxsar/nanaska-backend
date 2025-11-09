<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="mockExam">
        <!-- Header -->
        <div class="mb-6">
          <router-link to="/mock-exams" class="text-primary-600 dark:text-primary-400 hover:underline mb-2 inline-block">
            ← Back to Mock Exams
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ mockExam.name }}</h1>
          <p v-if="mockExam.description" class="text-gray-600 dark:text-gray-400">{{ mockExam.description }}</p>
        </div>

        <!-- Exam Info Card -->
        <div class="card mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Duration</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ mockExam.duration_minutes }} minutes</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Questions</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ mockExam.questions?.length || 0 }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
              <p class="text-xl font-bold" :class="mockExam.is_active ? 'text-green-600' : 'text-red-600'">
                {{ mockExam.is_active ? 'Active' : 'Inactive' }}
              </p>
            </div>
          </div>

          <!-- Pre-seen Document Info -->
          <div v-if="mockExam.pre_seen_document" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Pre-Seen Document</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ mockExam.pre_seen_document.name }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Start Exam Button -->
        <div class="card text-center py-8">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Ready to Begin?</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Once you start the exam, you'll see each question with its context, references, and sub-questions. 
            Make sure you have enough time to complete it.
          </p>
          <button @click="startExam" class="btn-primary">
            Start Mock Exam
          </button>
        </div>
      </div>

      <div v-else class="card text-center py-12">
        <p class="text-gray-600 dark:text-gray-400">Mock exam not found.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const route = useRoute();
const router = useRouter();

const mockExam = ref(null);
const loading = ref(true);

onMounted(async () => {
  await loadMockExam();
});

const loadMockExam = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/mock-exams/${route.params.id}`);
    if (response.data.success) {
      mockExam.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to load mock exam:', error);
  } finally {
    loading.value = false;
  }
};

const startExam = () => {
  // Navigate to unified question interface
  router.push(`/exam/mock/${route.params.id}`);
};
</script>
