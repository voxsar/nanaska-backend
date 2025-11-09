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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div 
          v-for="model in theoryModels" 
          :key="model.id"
          @click="selectModel(model)"
          class="card-glow cursor-pointer hover:scale-105 transition-all duration-200"
          :class="selectedModel?.id === model.id ? 'ring-2 ring-primary-600 dark:ring-primary-400' : ''"
        >
          <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <span class="text-white text-lg font-bold">{{ model.icon }}</span>
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ model.name }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ model.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Application Form -->
      <div v-if="selectedModel" class="card">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          Apply {{ selectedModel.name }} to Case Study
        </h2>
        
        <form @submit.prevent="applyModel" class="space-y-4">
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
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from '@/components/Layout.vue';

const theoryModels = ref([
  {
    id: 1,
    name: 'SWOT Analysis',
    icon: '⚖️',
    description: 'Analyze Strengths, Weaknesses, Opportunities, and Threats'
  },
  {
    id: 2,
    name: 'PEST Analysis',
    icon: '🌍',
    description: 'Examine Political, Economic, Social, and Technological factors'
  },
  {
    id: 3,
    name: "Porter's Five Forces",
    icon: '⚡',
    description: 'Assess competitive intensity and industry attractiveness'
  },
  {
    id: 4,
    name: 'Ansoff Matrix',
    icon: '📊',
    description: 'Evaluate growth strategies and market penetration'
  },
  {
    id: 5,
    name: 'BCG Matrix',
    icon: '💎',
    description: 'Analyze product portfolio and business units'
  },
  {
    id: 6,
    name: 'Value Chain Analysis',
    icon: '🔗',
    description: 'Identify value-adding activities in the organization'
  },
  {
    id: 7,
    name: 'McKinsey 7S Framework',
    icon: '🎯',
    description: 'Assess organizational design and effectiveness'
  },
  {
    id: 8,
    name: 'Balanced Scorecard',
    icon: '📈',
    description: 'Measure performance across multiple perspectives'
  },
  {
    id: 9,
    name: 'PESTLE Analysis',
    icon: '🌐',
    description: 'Extended PEST including Legal and Environmental factors'
  },
  {
    id: 10,
    name: 'Stakeholder Analysis',
    icon: '👥',
    description: 'Identify and analyze key stakeholders and their interests'
  }
]);

const selectedModel = ref(null);
const caseContext = ref('');
const specificQuestions = ref('');
const applying = ref(false);
const analysisResult = ref('');

const selectModel = (model) => {
  selectedModel.value = model;
  analysisResult.value = '';
};

const applyModel = async () => {
  applying.value = true;
  
  // TODO: Implement API call to apply model
  // For now, simulate API call
  setTimeout(() => {
    analysisResult.value = `Analysis of ${selectedModel.value.name} applied to the case study:\n\nThis is a placeholder result. The actual analysis will be performed by AI based on your pre-seen documents and the selected theory model.\n\nKey findings will include:\n- Detailed breakdown of each component of ${selectedModel.value.name}\n- Application to specific aspects of your case study\n- Strategic recommendations\n- Areas for further investigation`;
    applying.value = false;
  }, 2000);
};
</script>
