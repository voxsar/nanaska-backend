<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Ask Questions
      </h1>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Question Input Section -->
        <div class="lg:col-span-2">
          <div class="card mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Ask AI About Your Case Study</h2>
            <form @submit.prevent="submitQuestion" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Your Question
                </label>
                <textarea
                  v-model="question"
                  rows="4"
                  class="input-field"
                  placeholder="Ask anything about your pre-seen case study or how it relates to real-world industries..."
                  required
                ></textarea>
              </div>

              <button
                type="submit"
                :disabled="loading"
                class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="!loading">Get AI Answer</span>
                <span v-else>Processing...</span>
              </button>
            </form>
          </div>

          <!-- Answer Section -->
          <div v-if="answer" class="card">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">AI Response</h2>
            <div class="bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-gray-900 dark:to-gray-800 rounded-lg p-4 border border-primary-200 dark:border-gray-700">
              <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ answer }}</p>
            </div>
          </div>
        </div>

        <!-- Recent Questions Sidebar -->
        <div class="lg:col-span-1">
          <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Questions</h3>
            <div v-if="recentQuestions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
              <p class="text-sm">No questions yet</p>
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="(q, index) in recentQuestions"
                :key="index"
                @click="loadQuestion(q)"
                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ q }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from '@/components/Layout.vue';

const question = ref('');
const answer = ref('');
const loading = ref(false);
const recentQuestions = ref([]);

const submitQuestion = async () => {
  if (!question.value.trim()) return;

  loading.value = true;
  
  // Add to recent questions
  if (!recentQuestions.value.includes(question.value)) {
    recentQuestions.value.unshift(question.value);
    if (recentQuestions.value.length > 5) {
      recentQuestions.value.pop();
    }
  }

  // TODO: Implement actual API call
  setTimeout(() => {
    answer.value = `Thank you for your question: "${question.value}"\n\nThis is a placeholder response. The actual AI-powered answer will analyze your pre-seen documents and provide contextual, detailed answers based on:\n\n1. The content of your case study materials\n2. Relevant business theory and frameworks\n3. Real-world industry applications\n4. CIMA examination requirements\n\nThe system can also help you:\n- Connect case study scenarios to real-world examples\n- Understand complex business concepts\n- Apply theoretical models to practical situations\n- Prepare comprehensive answers for your examination`;
    loading.value = false;
  }, 2000);
};

const loadQuestion = (q) => {
  question.value = q;
  answer.value = '';
};
</script>
