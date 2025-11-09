<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="attempt">
        <!-- Header -->
        <div class="mb-6">
          <router-link to="/mock-exams" class="text-primary-600 dark:text-primary-400 hover:underline mb-2 inline-block">
            ← Back to Mock Exams
          </router-link>
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ attempt.mock_exam?.name || 'Mock Exam Attempt' }}
              </h1>
              <p class="text-gray-600 dark:text-gray-400">
                Attempt from {{ formatDate(attempt.started_at) }}
              </p>
            </div>
            <router-link 
              :to="'/mock-exams/attempts/' + attempt.id + '/summary'" 
              class="btn btn-primary flex items-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              View Results Summary
            </router-link>
          </div>
        </div>

        <!-- Overall Results Card -->
        <div class="card mb-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Overall Results</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
              <span class="inline-block px-3 py-1 rounded text-sm font-semibold" :class="getStatusBadge(attempt.status)">
                {{ attempt.status }}
              </span>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Score</p>
              <p class="text-2xl font-bold" :class="getScoreColor(attempt.percentage)">
                {{ attempt.percentage !== null ? `${attempt.percentage}%` : 'N/A' }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Marks</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ attempt.total_marks_obtained !== null ? `${attempt.total_marks_obtained} / ${attempt.total_marks_available}` : 'Not yet marked' }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Answers Submitted</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ attempt.answers?.length || 0 }}
              </p>
            </div>
          </div>
        </div>

        <!-- Answers Section -->
        <div v-if="attempt.answers && attempt.answers.length > 0" class="space-y-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Answers</h2>
          
          <div
            v-for="(answer, index) in attempt.answers"
            :key="answer.id"
            class="card"
          >
            <!-- Question Header -->
            <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Question {{ index + 1 }}
                <span v-if="answer.sub_question" class="text-sm font-normal text-gray-600 dark:text-gray-400">
                  - Sub-question {{ answer.sub_question.question_number }}
                </span>
              </h3>
              <p v-if="answer.question" class="text-gray-700 dark:text-gray-300 mb-2">
                {{ answer.question.question_text }}
              </p>
              <p v-if="answer.sub_question" class="text-gray-600 dark:text-gray-400 text-sm">
                {{ answer.sub_question.question_text }}
              </p>
            </div>

            <!-- Your Answer -->
            <div class="mb-4">
              <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Your Answer:</h4>
              <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ answer.answer_text }}</p>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Submitted: {{ formatDate(answer.submitted_at) }}
              </p>
            </div>

            <!-- Marking Results -->
                        <!-- Marking Results -->
            <div v-if="answer.status === 'marked'" class="border-t border-gray-200 dark:border-gray-700 pt-4">
              <!-- Detailed Marking Result Component -->
              <MarkingResultDisplay v-if="answer.marking_result" :result="answer.marking_result" />
              
              <!-- Fallback for basic feedback if detailed results not available -->
              <div v-else-if="answer.feedback" class="space-y-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                  <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Feedback:</h4>
                  <p class="text-blue-800 dark:text-blue-200 whitespace-pre-wrap">{{ answer.feedback }}</p>
                </div>
              </div>

              <!-- AI Response (if available) -->
              <div v-if="answer.ai_response" class="mt-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-300 mb-2">AI Analysis:</h4>
                <pre class="text-purple-800 dark:text-purple-200 whitespace-pre-wrap text-sm">{{ formatAiResponse(answer.ai_response) }}</pre>
              </div>
            </div>

            <!-- Pending Marking -->
            <div v-else class="border-t border-gray-200 dark:border-gray-700 pt-4">
              <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 text-center">
                <p class="text-yellow-800 dark:text-yellow-200">
                  <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  This answer is pending marking
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- No Answers -->
        <div v-else class="card text-center py-12">
          <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No answers submitted</h3>
          <p class="text-gray-600 dark:text-gray-400">This attempt doesn't have any answers yet.</p>
        </div>
      </div>

      <div v-else class="card text-center py-12">
        <p class="text-gray-600 dark:text-gray-400">Attempt not found.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Layout from '@/components/Layout.vue';
import MarkingResultDisplay from '@/components/MarkingResultDisplay.vue';
import api from '@/api/client';

const route = useRoute();
const attempt = ref(null);
const loading = ref(true);

onMounted(async () => {
  await loadAttempt();
});

const loadAttempt = async () => {
  loading.value = true;
  try {
    // Get student from localStorage
    const studentData = localStorage.getItem('student');
    if (!studentData) {
      console.error('No student data found');
      return;
    }
    
    const student = JSON.parse(studentData);
    const response = await api.get(`/mock-exams/attempts/${student.id}`);
    
    if (response.data.success) {
      // Find the specific attempt
      const attempts = response.data.data;
      attempt.value = attempts.find(a => a.id === parseInt(route.params.id));
    }
  } catch (error) {
    console.error('Failed to load attempt:', error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getScoreColor = (percentage) => {
  if (percentage === null) return 'text-gray-500';
  if (percentage >= 80) return 'text-green-600 dark:text-green-400';
  if (percentage >= 60) return 'text-yellow-600 dark:text-yellow-400';
  return 'text-red-600 dark:text-red-400';
};

const getStatusBadge = (status) => {
  const badges = {
    'in_progress': 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
    'completed': 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
    'submitted': 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
  };
  return badges[status] || 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300';
};

const getAnswerStatusBadge = (status) => {
  const badges = {
    'submitted': 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
    'marked': 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
    'pending': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
  };
  return badges[status] || 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300';
};

const formatAiResponse = (aiResponse) => {
  if (typeof aiResponse === 'string') return aiResponse;
  return JSON.stringify(aiResponse, null, 2);
};
</script>
