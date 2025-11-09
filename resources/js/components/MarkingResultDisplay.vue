<template>
  <div v-if="result" class="space-y-6">
    <!-- Band and Level Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- CIMA Level -->
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4">
        <p class="text-sm text-blue-700 dark:text-blue-300 font-medium mb-1">CIMA Level</p>
        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
          {{ result.level || 'N/A' }}
        </p>
        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
          {{ getLevelDescription(result.level) }}
        </p>
      </div>

      <!-- Band Level -->
      <div class="rounded-lg p-4" :class="getBandColorClass(result.band_level)">
        <p class="text-sm font-medium mb-1" :class="getBandTextClass(result.band_level)">Band Level</p>
        <p class="text-2xl font-bold" :class="getBandTextClass(result.band_level)">
          Band {{ result.band_level || 'N/A' }}
        </p>
        <p class="text-xs mt-1" :class="getBandTextClass(result.band_level)">
          {{ getBandDescription(result.band_level) }}
        </p>
      </div>

      <!-- Marks -->
      <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4">
        <p class="text-sm text-purple-700 dark:text-purple-300 font-medium mb-1">Marks Obtained</p>
        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
          {{ result.marks_obtained || 0 }} / {{ result.total_marks || 0 }}
        </p>
        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
          {{ getPercentage(result.marks_obtained, result.total_marks) }}%
        </p>
      </div>
    </div>

    <!-- Quality Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="flex items-center p-4 rounded-lg" :class="result.answered_specific_question ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'">
        <svg v-if="result.answered_specific_question" class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
          <p class="font-semibold" :class="result.answered_specific_question ? 'text-green-900 dark:text-green-100' : 'text-red-900 dark:text-red-100'">
            {{ result.answered_specific_question ? 'Answered Specific Question' : 'Did Not Answer Specific Question' }}
          </p>
          <p class="text-sm" :class="result.answered_specific_question ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'">
            Question relevance check
          </p>
        </div>
      </div>

      <div class="flex items-center p-4 rounded-lg" :class="result.structure_ok ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'">
        <svg v-if="result.structure_ok" class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
          <p class="font-semibold" :class="result.structure_ok ? 'text-green-900 dark:text-green-100' : 'text-red-900 dark:text-red-100'">
            {{ result.structure_ok ? 'Good Structure' : 'Structure Needs Improvement' }}
          </p>
          <p class="text-sm" :class="result.structure_ok ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'">
            Answer organization
          </p>
        </div>
      </div>
    </div>

    <!-- Band Explanation -->
    <div v-if="result.band_explanation" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Band Explanation
      </h4>
      <p class="text-blue-800 dark:text-blue-200 whitespace-pre-wrap">{{ result.band_explanation }}</p>
    </div>

    <!-- Genericity Comment -->
    <div v-if="result.genericity_comment" class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-300 mb-2">Specificity vs Generic Assessment</h4>
      <p class="text-amber-800 dark:text-amber-200 whitespace-pre-wrap">{{ result.genericity_comment }}</p>
    </div>

    <!-- Strengths -->
    <div v-if="result.strengths_extracts && result.strengths_extracts.length > 0" class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-green-900 dark:text-green-300 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Strengths in Your Answer
      </h4>
      <ul class="space-y-2">
        <li v-for="(strength, index) in result.strengths_extracts" :key="index" class="flex items-start">
          <span class="inline-block w-2 h-2 bg-green-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
          <p class="text-green-800 dark:text-green-200 text-sm">{{ strength }}</p>
        </li>
      </ul>
    </div>

    <!-- Weaknesses -->
    <div v-if="result.weaknesses_extracts && result.weaknesses_extracts.length > 0" class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-red-900 dark:text-red-300 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        Areas for Improvement
      </h4>
      <ul class="space-y-2">
        <li v-for="(weakness, index) in result.weaknesses_extracts" :key="index" class="flex items-start">
          <span class="inline-block w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
          <p class="text-red-800 dark:text-red-200 text-sm">{{ weakness }}</p>
        </li>
      </ul>
    </div>

    <!-- Points Summary -->
    <div v-if="result.points_summary && result.points_summary.length > 0" class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-300 mb-3">Key Points Analysis</h4>
      <div class="space-y-4">
        <div v-for="(point, index) in result.points_summary" :key="index" class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-200 dark:border-purple-700">
          <div class="flex items-start justify-between mb-2">
            <p class="font-semibold text-gray-900 dark:text-white flex-1">{{ point.point }}</p>
            <span class="ml-2 px-2 py-1 rounded text-xs font-semibold" :class="getPracticalityClass(point.practicality)">
              {{ point.practicality }} practicality
            </span>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Justified with:</strong> {{ point.justified_with }}</p>
        </div>
      </div>
    </div>

    <!-- Improvement Plan -->
    <div v-if="result.improvement_plan && result.improvement_plan.length > 0" class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-300 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Your Improvement Plan
      </h4>
      <ol class="space-y-2 ml-1">
        <li v-for="(action, index) in result.improvement_plan" :key="index" class="flex items-start">
          <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500 text-white text-xs font-bold mr-3 flex-shrink-0 mt-0.5">
            {{ index + 1 }}
          </span>
          <p class="text-indigo-800 dark:text-indigo-200 text-sm flex-1">{{ action }}</p>
        </li>
      </ol>
    </div>

    <!-- Citations -->
    <div v-if="result.citations && result.citations.length > 0" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
      <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-300 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        Source Citations
      </h4>
      <div class="space-y-2">
        <div v-for="(citation, index) in result.citations" :key="index" class="flex items-center text-sm">
          <span class="px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-mono text-xs mr-2">
            {{ citation.type.toUpperCase() }}
          </span>
          <span class="text-gray-600 dark:text-gray-400">
            <template v-if="citation.page">Page {{ citation.page }}</template>
            <template v-if="citation.lines">, {{ citation.lines }}</template>
            <template v-if="citation.question_ref">{{ citation.question_ref }}</template>
            <template v-if="citation.slide">Slide {{ citation.slide }}</template>
          </span>
        </div>
      </div>
    </div>

    <!-- Assumptions -->
    <div v-if="result.assumptions && result.assumptions.length > 0" class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
      <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-300 mb-3">Assumptions Made</h4>
      <ul class="space-y-1">
        <li v-for="(assumption, index) in result.assumptions" :key="index" class="flex items-start text-sm text-yellow-800 dark:text-yellow-200">
          <span class="mr-2">•</span>
          <span>{{ assumption }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  result: {
    type: Object,
    required: true
  }
});

const getLevelDescription = (level) => {
  const descriptions = {
    'OCS': 'Operational Case Study',
    'MCS': 'Management Case Study',
    'SCS': 'Strategic Case Study'
  };
  return descriptions[level] || '';
};

const getBandDescription = (band) => {
  const descriptions = {
    1: 'Identification only',
    2: 'Partial application',
    3: 'Justified & practical'
  };
  return descriptions[band] || '';
};

const getBandColorClass = (band) => {
  const colors = {
    1: 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20',
    2: 'bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20',
    3: 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20'
  };
  return colors[band] || 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20';
};

const getBandTextClass = (band) => {
  const colors = {
    1: 'text-red-700 dark:text-red-300',
    2: 'text-amber-700 dark:text-amber-300',
    3: 'text-green-700 dark:text-green-300'
  };
  return colors[band] || 'text-gray-700 dark:text-gray-300';
};

const getPracticalityClass = (practicality) => {
  const classes = {
    'low': 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
    'medium': 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200',
    'high': 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200'
  };
  return classes[practicality] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-200';
};

const getPercentage = (obtained, total) => {
  if (!total || total === 0) return 0;
  return ((obtained / total) * 100).toFixed(1);
};
</script>
