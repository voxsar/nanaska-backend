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
        </div>

        <!-- Start Exam Button -->
        <div v-if="!examStarted" class="card text-center py-8">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Ready to Begin?</h2>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Once you start the exam, the timer will begin. Make sure you have enough time to complete it.
          </p>
          <button @click="startExam" class="btn-primary">
            Start Mock Exam
          </button>
        </div>

        <!-- Exam Questions -->
        <div v-else class="space-y-6">
          <div
            v-for="(question, index) in mockExam.questions"
            :key="question.id"
            class="card"
          >
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">{{ question.question_number }}</span>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  Question {{ question.question_number }}
                </h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4 whitespace-pre-wrap">{{ question.question_text }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Marks: {{ question.marks }}</p>
                
                <textarea
                  v-model="answers[question.id]"
                  rows="6"
                  class="input-field"
                  placeholder="Type your answer here..."
                ></textarea>
              </div>
            </div>
          </div>

          <div class="card">
            <button
              @click="submitExam"
              :disabled="submitting"
              class="btn-primary w-full disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!submitting">Submit Exam</span>
              <span v-else>Submitting...</span>
            </button>
          </div>
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
const examStarted = ref(false);
const answers = ref({});
const submitting = ref(false);

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
  examStarted.value = true;
  // TODO: Start timer
};

const submitExam = async () => {
  submitting.value = true;
  
  // TODO: Submit answers to API
  setTimeout(() => {
    submitting.value = false;
    alert('Exam submitted successfully! You will receive your results shortly.');
    router.push('/mock-exams');
  }, 2000);
};
</script>
