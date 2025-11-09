<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Theory Models Application
      </h1>
      
      <div class="card mb-6">
        <p class="text-gray-600 dark:text-gray-400">
          Select a business theory model and apply it to your case study. The AI will analyze your pre-seen material using the selected framework and provide comprehensive insights.
        </p>
      </div>

      <!-- Theory Models Grid -->
      <div v-if="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div 
          v-for="model in theoryModels" 
          :key="model.id"
          @click="selectModel(model)"
          class="card-glow cursor-pointer hover:scale-105 transition-all duration-200"
          :class="selectedModel?.id === model.id ? 'ring-2 ring-primary-600 dark:ring-primary-400' : ''"
        >
          <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <span class="text-white text-lg font-bold">{{ getModelIcon(model.name) }}</span>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ model.name }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ model.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading theory models...</p>
      </div>

      <!-- Application Form -->
      <div v-if="selectedModel" class="card">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          Apply {{ selectedModel.name }} to Case Study
        </h2>
        
        <form @submit.prevent="applyModel" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Pre-Seen Document (Optional)
            </label>
            <select
              v-model="selectedPreSeenId"
              class="input-field"
            >
              <option :value="null">None - Use general context only</option>
              <option 
                v-for="doc in preSeenDocuments" 
                :key="doc.id"
                :value="doc.id"
              >
                {{ doc.name }}
              </option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Select a pre-seen document to analyze with this theory model
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Case Study Context (Optional)
            </label>
            <textarea
              v-model="caseContext"
              rows="4"
              class="input-field"
              placeholder="Provide additional context or specific areas you'd like to focus on..."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Specific Questions (Optional)
            </label>
            <textarea
              v-model="specificQuestions"
              rows="3"
              class="input-field"
              placeholder="Any specific questions about applying this model?"
            ></textarea>
          </div>

          <button
            type="submit"
            :disabled="applying"
            class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!applying">Apply Model</span>
            <span v-else>Analyzing...</span>
          </button>
        </form>
      </div>

      <!-- Results Section -->
      <div v-if="analysisResult" class="card mt-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          Analysis Results
        </h2>
        <div class="prose dark:prose-invert max-w-none">
          <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ analysisResult }}</p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="card mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">Error</h3>
        <p class="text-red-600 dark:text-red-300">{{ errorMessage }}</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const theoryModels = ref([]);
const preSeenDocuments = ref([]);
const selectedModel = ref(null);
const selectedPreSeenId = ref(null);
const caseContext = ref('');
const specificQuestions = ref('');
const applying = ref(false);
const loading = ref(true);
const analysisResult = ref('');
const errorMessage = ref('');

// Icon mapping for theory models
const iconMap = {
  'SWOT Analysis': '⚖️',
  'PEST Analysis': '🌍',
  "Porter's Five Forces": '⚡',
  'Ansoff Matrix': '📊',
  'BCG Matrix': '💎',
  'Value Chain Analysis': '🔗',
  'McKinsey 7S Framework': '🎯',
  'Balanced Scorecard': '📈',
  'PESTLE Analysis': '🌐',
  'Stakeholder Analysis': '👥',
};

const getModelIcon = (name) => {
  return iconMap[name] || '📋';
};

// Fetch theory models from API
const fetchTheoryModels = async () => {
  try {
    const response = await api.get('/theory-models');
    if (response.data.success) {
      theoryModels.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching theory models:', error);
    errorMessage.value = 'Failed to load theory models. Please refresh the page.';
  } finally {
    loading.value = false;
  }
};

// Fetch pre-seen documents
const fetchPreSeenDocuments = async () => {
  try {
    const response = await api.get('/pre-seen-documents');
    if (response.data.success) {
      preSeenDocuments.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching pre-seen documents:', error);
  }
};

const selectModel = (model) => {
  selectedModel.value = model;
  analysisResult.value = '';
  errorMessage.value = '';
};

const applyModel = async () => {
  applying.value = true;
  errorMessage.value = '';
  analysisResult.value = '';
  
  try {
    const response = await api.post('/theory-models/apply', {
      theory_model_id: selectedModel.value.id,
      pre_seen_document_id: selectedPreSeenId.value,
      case_context: caseContext.value,
      specific_questions: specificQuestions.value,
    });

    if (response.data.success) {
      analysisResult.value = `Analysis request submitted successfully!\n\nTheory Model: ${selectedModel.value.name}\n\nThe analysis is being processed by our AI system. Results will be available shortly.\n\nN8N Response Status: ${response.data.n8n_responses.map(r => r.status || 'submitted').join(', ')}`;
    }
  } catch (error) {
    console.error('Error applying model:', error);
    errorMessage.value = error.response?.data?.message || 'Failed to apply theory model. Please try again.';
  } finally {
    applying.value = false;
  }
};

onMounted(() => {
  fetchTheoryModels();
  fetchPreSeenDocuments();
});
</script>
