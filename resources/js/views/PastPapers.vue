<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Past Papers Archive
      </h1>
      
      <div class="card mb-6">
        <p class="text-gray-600 dark:text-gray-400">
          Access the last 5 post-examiner guidelines including past pre-seens, exam papers, student answers, marking schemes, and examiner feedback. Admins can add descriptions and students can ask questions about the papers.
        </p>
      </div>

      <!-- Search and Filter -->
      <div class="card mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <input
            v-model="searchQuery"
            type="text"
            class="input-field flex-1"
            placeholder="Search by topic, service industry, or keyword..."
          />
          <button @click="search" class="btn-primary">
            Search
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Past Papers List -->
      <div v-else-if="pastPapers.length > 0" class="space-y-6">
        <div
          v-for="paper in pastPapers"
          :key="paper.id"
          class="card-glow cursor-pointer hover:scale-[1.02] transition-transform duration-200"
          @click="viewPaper(paper)"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                  {{ paper.year }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary-100 dark:bg-secondary-900/30 text-secondary-800 dark:text-secondary-300">
                  {{ paper.type }}
                </span>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ paper.name }}</h3>
              <p v-if="paper.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ paper.description }}</p>
              
              <div class="flex flex-wrap gap-2">
                <span v-if="paper.question_paper" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">📄 Question Paper</span>
                <span v-if="paper.answer_guide" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">✅ Answer Guide</span>
                <span v-if="paper.marking_guide" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">📝 Marking Guide</span>
                <span v-if="paper.questions && paper.questions.length > 0" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">
                  {{ paper.questions.length }} Questions
                </span>
              </div>
            </div>
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="card text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No past papers found</h3>
        <p class="text-gray-600 dark:text-gray-400">Try adjusting your search criteria or check back later.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const router = useRouter();
const searchQuery = ref('');
const pastPapers = ref([]);
const loading = ref(true);

onMounted(async () => {
  await loadPastPapers();
});

const loadPastPapers = async () => {
  loading.value = true;
  try {
    const response = await api.get('/past-papers');
    if (response.data.success) {
      pastPapers.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to load past papers:', error);
  } finally {
    loading.value = false;
  }
};

const search = () => {
  // TODO: Implement search functionality
  console.log('Searching for:', searchQuery.value);
};

const viewPaper = (paper) => {
  // TODO: Navigate to detail view
  console.log('Viewing paper:', paper);
};
</script>
