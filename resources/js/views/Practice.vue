<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Practice Questions
      </h1>
      
      <div class="card mb-6">
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Answer practice questions and receive instant AI-powered marking with detailed feedback. Track your progress and improve your exam preparation.
        </p>
        <div class="flex items-center justify-between">
          <div>
            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ answeredCount }}</span>
            <span class="text-gray-600 dark:text-gray-400"> / {{ practiceExams.length }} Practice Sets Completed</span>
          </div>
          <div class="text-right">
            <span class="text-2xl font-bold text-secondary-600 dark:text-secondary-400">{{ averageScore }}%</span>
            <span class="text-gray-600 dark:text-gray-400 block text-sm">Average Score</span>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Practice Exams List -->
      <div v-else-if="practiceExams.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="exam in practiceExams"
          :key="exam.id"
          class="card-glow cursor-pointer hover:scale-105 transition-transform duration-200"
          @click="startPractice(exam)"
        >
          <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ exam.name }}</h3>
              <p v-if="exam.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ exam.description }}</p>
              
              <div class="flex flex-wrap gap-2 mb-3">
                <span class="text-xs px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 rounded">
                  {{ exam.duration_minutes }} minutes
                </span>
                <span v-if="exam.questions" class="text-xs px-2 py-1 bg-secondary-100 dark:bg-secondary-900/30 text-secondary-800 dark:text-secondary-300 rounded">
                  {{ exam.questions.length }} questions
                </span>
                <span v-if="exam.completed" class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded">
                  ✓ Completed
                </span>
              </div>

              <button class="btn-primary text-sm">
                {{ exam.completed ? 'Review Practice' : 'Start Practice' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="card text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No practice questions available</h3>
        <p class="text-gray-600 dark:text-gray-400">Practice questions will appear here once created by your administrator.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const router = useRouter();
const practiceExams = ref([]);
const loading = ref(true);

onMounted(async () => {
  await loadPracticeExams();
});

const loadPracticeExams = async () => {
  loading.value = true;
  try {
    // TODO: Create practice questions API endpoint
    // For now, using mock exams with practice flag or separate endpoint
    const response = await api.get('/practice-exams');
    if (response.data.success) {
      practiceExams.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to load practice exams:', error);
    // Fallback to empty for now
    practiceExams.value = [];
  } finally {
    loading.value = false;
  }
};

const answeredCount = computed(() => practiceExams.value.filter(q => q.completed).length);
const averageScore = computed(() => {
  const completedExams = practiceExams.value.filter(q => q.score !== null);
  if (completedExams.length === 0) return 0;
  const total = completedExams.reduce((sum, q) => sum + q.score, 0);
  return Math.round(total / completedExams.length);
});

const startPractice = (exam) => {
  // Navigate to unified question interface with practice type
  router.push(`/exam/practice/${exam.id}`);
};
</script>
