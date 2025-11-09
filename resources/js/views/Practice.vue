<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Practice Questions
      </h1>
      
      <div class="card mb-6">
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          Answer 50 practice questions and receive instant AI-powered marking with detailed feedback. Track your progress and improve your exam preparation.
        </p>
        <div class="flex items-center justify-between">
          <div>
            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ answeredCount }}</span>
            <span class="text-gray-600 dark:text-gray-400"> / 50 Questions Completed</span>
          </div>
          <div class="text-right">
            <span class="text-2xl font-bold text-secondary-600 dark:text-secondary-400">{{ averageScore }}%</span>
            <span class="text-gray-600 dark:text-gray-400 block text-sm">Average Score</span>
          </div>
        </div>
      </div>

      <!-- Question List -->
      <div class="space-y-4">
        <div
          v-for="(question, index) in questions"
          :key="index"
          class="card-glow cursor-pointer hover:scale-[1.02] transition-transform duration-200"
          @click="startQuestion(question, index + 1)"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">{{ index + 1 }}</span>
              </div>
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question {{ index + 1 }}</h3>
                  <span v-if="question.completed" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                    ✓ Completed
                  </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ question.title }}</p>
                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                  <span>{{ question.marks }} marks</span>
                  <span>{{ question.type }}</span>
                  <span v-if="question.score !== null" class="font-semibold" :class="question.score >= 70 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'">
                    Score: {{ question.score }}%
                  </span>
                </div>
              </div>
            </div>
            <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import Layout from '@/components/Layout.vue';

// Generate 50 sample questions
const questions = ref(
  Array.from({ length: 50 }, (_, i) => ({
    id: i + 1,
    title: `Practice question ${i + 1} - Financial Analysis and Strategic Planning`,
    marks: Math.floor(Math.random() * 15) + 5,
    type: ['OCS', 'MCS', 'SCS'][Math.floor(Math.random() * 3)],
    completed: Math.random() > 0.7,
    score: Math.random() > 0.7 ? Math.floor(Math.random() * 30) + 60 : null
  }))
);

const answeredCount = computed(() => questions.value.filter(q => q.completed).length);
const averageScore = computed(() => {
  const completedQuestions = questions.value.filter(q => q.score !== null);
  if (completedQuestions.length === 0) return 0;
  const total = completedQuestions.reduce((sum, q) => sum + q.score, 0);
  return Math.round(total / completedQuestions.length);
});

const startQuestion = (question, number) => {
  // TODO: Navigate to question detail page
  console.log('Starting question:', number);
};
</script>
