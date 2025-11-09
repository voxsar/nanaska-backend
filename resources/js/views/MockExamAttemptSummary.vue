<template>
  <Layout>
    <div class="container mx-auto px-4 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>

      <!-- Content -->
      <div v-else-if="attempt">
        <!-- Header -->
        <div class="mb-6">
          <router-link 
            :to="'/mock-exams/' + attempt.mock_exam.id" 
            class="text-blue-600 dark:text-blue-400 hover:underline mb-2 inline-block"
          >
            ← Back to Mock Exam
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            {{ attempt.mock_exam.title }} - Results Summary
          </h1>
          <p class="text-gray-600 dark:text-gray-400">Comprehensive overview of your exam performance</p>
        </div>

        <!-- Overall Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <!-- Total Score -->
          <div class="card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-800">
            <h3 class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-1">Overall Score</h3>
            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">
              {{ calculateTotalScore() }}
            </p>
            <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
              {{ calculatePercentage() }}%
            </p>
          </div>

          <!-- Average Band Level -->
          <div class="card" :class="getAverageBandColorClass()">
            <h3 class="text-sm font-semibold mb-1" :class="getAverageBandTextClass()">Average Band</h3>
            <p class="text-3xl font-bold" :class="getAverageBandTextClass()">
              Band {{ calculateAverageBand() }}
            </p>
            <p class="text-sm mt-1" :class="getAverageBandTextClass()">
              {{ getMarkedCount() }} answers marked
            </p>
          </div>

          <!-- Questions Answered -->
          <div class="card bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-purple-200 dark:border-purple-800">
            <h3 class="text-sm font-semibold text-purple-700 dark:text-purple-300 mb-1">Questions</h3>
            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">
              {{ getMarkedCount() }} / {{ attempt.answers.length }}
            </p>
            <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">
              Marked
            </p>
          </div>

          <!-- Strengths Count -->
          <div class="card bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-800">
            <h3 class="text-sm font-semibold text-green-700 dark:text-green-300 mb-1">Strengths</h3>
            <p class="text-3xl font-bold text-green-900 dark:text-green-100">
              {{ countTotalStrengths() }}
            </p>
            <p class="text-sm text-green-600 dark:text-green-400 mt-1">
              Identified
            </p>
          </div>
        </div>

        <!-- Band Distribution -->
        <div class="card mb-6">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Band Level Distribution</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="band in [1, 2, 3]" :key="band" class="p-4 rounded-lg" :class="getBandColorClass(band)">
              <div class="flex items-center justify-between mb-2">
                <span class="font-semibold" :class="getBandTextClass(band)">Band {{ band }}</span>
                <span class="text-2xl font-bold" :class="getBandTextClass(band)">
                  {{ countBand(band) }}
                </span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="h-2 rounded-full" :class="getBandBarClass(band)" :style="{ width: (countBand(band) / getMarkedCount() * 100) + '%' }"></div>
              </div>
              <p class="text-xs mt-1" :class="getBandTextClass(band)">
                {{ ((countBand(band) / getMarkedCount()) * 100).toFixed(0) }}% of answers
              </p>
            </div>
          </div>
        </div>

        <!-- Quality Metrics -->
        <div class="card mb-6">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quality Metrics</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Answered Specific Question -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Answered Specific Question</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                  {{ countAnsweredSpecific() }} / {{ getMarkedCount() }}
                </span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" :style="{ width: (countAnsweredSpecific() / getMarkedCount() * 100) + '%' }"></div>
              </div>
              <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">
                {{ ((countAnsweredSpecific() / getMarkedCount()) * 100).toFixed(0) }}% relevance rate
              </p>
            </div>

            <!-- Good Structure -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Good Structure</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                  {{ countGoodStructure() }} / {{ getMarkedCount() }}
                </span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" :style="{ width: (countGoodStructure() / getMarkedCount() * 100) + '%' }"></div>
              </div>
              <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">
                {{ ((countGoodStructure() / getMarkedCount()) * 100).toFixed(0) }}% well-structured
              </p>
            </div>
          </div>
        </div>

        <!-- Common Strengths -->
        <div v-if="getAllStrengths().length > 0" class="card mb-6 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800">
          <h2 class="text-xl font-semibold text-green-900 dark:text-green-300 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Key Strengths Across Your Exam
          </h2>
          <div class="space-y-2">
            <div v-for="(strength, index) in getAllStrengths().slice(0, 5)" :key="index" class="flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg">
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-500 text-white text-xs font-bold mr-3 flex-shrink-0">
                {{ index + 1 }}
              </span>
              <p class="text-green-900 dark:text-green-100">{{ strength }}</p>
            </div>
          </div>
        </div>

        <!-- Common Weaknesses -->
        <div v-if="getAllWeaknesses().length > 0" class="card mb-6 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800">
          <h2 class="text-xl font-semibold text-red-900 dark:text-red-300 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Priority Areas for Improvement
          </h2>
          <div class="space-y-2">
            <div v-for="(weakness, index) in getAllWeaknesses().slice(0, 5)" :key="index" class="flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg">
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white text-xs font-bold mr-3 flex-shrink-0">
                {{ index + 1 }}
              </span>
              <p class="text-red-900 dark:text-red-100">{{ weakness }}</p>
            </div>
          </div>
        </div>

        <!-- Overall Improvement Plan -->
        <div v-if="getAllImprovements().length > 0" class="card mb-6 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800">
          <h2 class="text-xl font-semibold text-indigo-900 dark:text-indigo-300 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Your Overall Improvement Plan
          </h2>
          <div class="space-y-2">
            <div v-for="(action, index) in getAllImprovements().slice(0, 8)" :key="index" class="flex items-start p-3 bg-white dark:bg-gray-800 rounded-lg">
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500 text-white text-xs font-bold mr-3 flex-shrink-0">
                {{ index + 1 }}
              </span>
              <p class="text-indigo-900 dark:text-indigo-100">{{ action }}</p>
            </div>
          </div>
        </div>

        <!-- Individual Question Results -->
        <div class="card">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Question-by-Question Results</h2>
            <router-link 
              :to="'/mock-exams/attempts/' + attempt.id" 
              class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
            >
              View detailed answers →
            </router-link>
          </div>

          <div class="space-y-3">
            <div 
              v-for="(answer, index) in attempt.answers" 
              :key="answer.id" 
              class="p-4 rounded-lg border" 
              :class="answer.marking_result ? 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800'"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-2">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                      Question {{ index + 1 }}
                    </span>
                    <span 
                      v-if="answer.marking_result" 
                      class="px-2 py-1 rounded text-xs font-semibold"
                      :class="getBandBadgeClass(answer.marking_result.band_level)"
                    >
                      Band {{ answer.marking_result.band_level }}
                    </span>
                    <span 
                      v-else
                      class="px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200"
                    >
                      Pending
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                    {{ answer.question_text || 'Question text not available' }}
                  </p>
                </div>
                <div v-if="answer.marking_result" class="ml-4 text-right">
                  <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ answer.marking_result.marks_obtained }}
                  </p>
                  <p class="text-xs text-gray-600 dark:text-gray-400">
                    / {{ answer.marking_result.total_marks }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Not Found -->
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
    const studentData = localStorage.getItem('student');
    if (!studentData) {
      console.error('No student data found');
      return;
    }
    
    const student = JSON.parse(studentData);
    const response = await api.get(`/mock-exams/attempts/${student.id}`);
    
    if (response.data.success) {
      const attempts = response.data.data;
      attempt.value = attempts.find(a => a.id === parseInt(route.params.id));
    }
  } catch (error) {
    console.error('Failed to load attempt:', error);
  } finally {
    loading.value = false;
  }
};

const calculateTotalScore = () => {
  if (!attempt.value) return '0/0';
  const marked = attempt.value.answers.filter(a => a.marking_result);
  const obtained = marked.reduce((sum, a) => sum + (a.marking_result.marks_obtained || 0), 0);
  const total = marked.reduce((sum, a) => sum + (a.marking_result.total_marks || 0), 0);
  return `${obtained}/${total}`;
};

const calculatePercentage = () => {
  if (!attempt.value) return 0;
  const marked = attempt.value.answers.filter(a => a.marking_result);
  const obtained = marked.reduce((sum, a) => sum + (a.marking_result.marks_obtained || 0), 0);
  const total = marked.reduce((sum, a) => sum + (a.marking_result.total_marks || 0), 0);
  return total > 0 ? ((obtained / total) * 100).toFixed(1) : 0;
};

const calculateAverageBand = () => {
  if (!attempt.value) return 'N/A';
  const marked = attempt.value.answers.filter(a => a.marking_result && a.marking_result.band_level);
  if (marked.length === 0) return 'N/A';
  const sum = marked.reduce((acc, a) => acc + a.marking_result.band_level, 0);
  return (sum / marked.length).toFixed(1);
};

const getMarkedCount = () => {
  if (!attempt.value) return 0;
  return attempt.value.answers.filter(a => a.marking_result).length;
};

const countBand = (band) => {
  if (!attempt.value) return 0;
  return attempt.value.answers.filter(a => a.marking_result && a.marking_result.band_level === band).length;
};

const countAnsweredSpecific = () => {
  if (!attempt.value) return 0;
  return attempt.value.answers.filter(a => a.marking_result && a.marking_result.answered_specific_question).length;
};

const countGoodStructure = () => {
  if (!attempt.value) return 0;
  return attempt.value.answers.filter(a => a.marking_result && a.marking_result.structure_ok).length;
};

const countTotalStrengths = () => {
  if (!attempt.value) return 0;
  return attempt.value.answers.reduce((count, a) => {
    if (a.marking_result && a.marking_result.strengths_extracts) {
      return count + a.marking_result.strengths_extracts.length;
    }
    return count;
  }, 0);
};

const getAllStrengths = () => {
  if (!attempt.value) return [];
  const strengths = [];
  attempt.value.answers.forEach(a => {
    if (a.marking_result && a.marking_result.strengths_extracts) {
      strengths.push(...a.marking_result.strengths_extracts);
    }
  });
  return strengths;
};

const getAllWeaknesses = () => {
  if (!attempt.value) return [];
  const weaknesses = [];
  attempt.value.answers.forEach(a => {
    if (a.marking_result && a.marking_result.weaknesses_extracts) {
      weaknesses.push(...a.marking_result.weaknesses_extracts);
    }
  });
  return weaknesses;
};

const getAllImprovements = () => {
  if (!attempt.value) return [];
  const improvements = [];
  attempt.value.answers.forEach(a => {
    if (a.marking_result && a.marking_result.improvement_plan) {
      improvements.push(...a.marking_result.improvement_plan);
    }
  });
  return improvements;
};

const getBandColorClass = (band) => {
  const colors = {
    1: 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-200 dark:border-red-800',
    2: 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-amber-200 dark:border-amber-800',
    3: 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-800'
  };
  return colors[band] || 'bg-gray-50 dark:bg-gray-800';
};

const getBandTextClass = (band) => {
  const colors = {
    1: 'text-red-700 dark:text-red-300',
    2: 'text-amber-700 dark:text-amber-300',
    3: 'text-green-700 dark:text-green-300'
  };
  return colors[band] || 'text-gray-700 dark:text-gray-300';
};

const getBandBarClass = (band) => {
  const colors = {
    1: 'bg-red-500',
    2: 'bg-amber-500',
    3: 'bg-green-500'
  };
  return colors[band] || 'bg-gray-500';
};

const getBandBadgeClass = (band) => {
  const classes = {
    1: 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
    2: 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200',
    3: 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200'
  };
  return classes[band] || 'bg-gray-100 text-gray-800';
};

const getAverageBandColorClass = () => {
  const avg = parseFloat(calculateAverageBand());
  if (isNaN(avg)) return 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700';
  if (avg >= 2.5) return 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-800';
  if (avg >= 1.5) return 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-amber-200 dark:border-amber-800';
  return 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-200 dark:border-red-800';
};

const getAverageBandTextClass = () => {
  const avg = parseFloat(calculateAverageBand());
  if (isNaN(avg)) return 'text-gray-700 dark:text-gray-300';
  if (avg >= 2.5) return 'text-green-700 dark:text-green-300';
  if (avg >= 1.5) return 'text-amber-700 dark:text-amber-300';
  return 'text-red-700 dark:text-red-300';
};
</script>
