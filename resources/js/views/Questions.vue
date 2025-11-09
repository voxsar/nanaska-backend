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
                  Select Pre-seen Document
                </label>
                <select v-model="selectedPreSeenId" class="input-field">
                  <option value="" disabled>Select a document…</option>
                  <option v-for="doc in preSeenDocuments" :key="doc.id" :value="doc.id">
                    {{ doc.name }}
                  </option>
                </select>
              </div>
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
                :disabled="loading || !selectedPreSeenId"
                class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="!loading">Get AI Answer</span>
                <span v-else>Processing...</span>
              </button>
            </form>
          </div>

          <!-- Pending State -->
          <div v-if="pending" class="card">
            <div class="flex items-center space-x-3">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
              <p class="text-gray-700 dark:text-gray-300">Waiting for AI response…</p>
            </div>
          </div>

          <!-- AI Answer (Two rows: Answers then Sources) -->
          <div v-if="aiAnswer" class="space-y-6">
            <div class="card">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">AI Answer</h2>
              <ul class="list-disc pl-6 space-y-2 text-gray-800 dark:text-gray-200">
                <li v-for="(bp, i) in aiAnswer.bullet_point_answers" :key="i">{{ bp }}</li>
              </ul>
            </div>

            <div class="card">
              <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Sources</h2>
              <div class="space-y-3">
                <blockquote
                  class="border-l-4 border-primary-400 pl-4 text-gray-700 dark:text-gray-300"
                  v-for="(qs, i) in aiAnswer.quoted_snippets"
                  :key="i"
                >
                  “{{ qs }}”
                </blockquote>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Questions Sidebar -->
        <div class="lg:col-span-1">
          <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Past Questions</h3>
            <div v-if="loadingPastQuestions" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
            </div>
            <div v-else-if="pastQuestions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
              <p class="text-sm">No questions yet</p>
            </div>
            <div v-else class="space-y-3 max-h-[600px] overflow-y-auto">
              <div
                v-for="q in pastQuestions"
                :key="q.id"
                @click="loadPastQuestion(q)"
                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 mb-2">{{ q.question_text }}</p>
                <div class="flex items-center justify-between text-xs">
                  <span 
                    class="px-2 py-1 rounded"
                    :class="q.status === 'answered' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'"
                  >
                    {{ q.status }}
                  </span>
                  <span class="text-gray-500 dark:text-gray-400">
                    {{ formatDate(q.created_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const question = ref('');
const loading = ref(false);
const loadingPastQuestions = ref(false);
const pastQuestions = ref([]);
const preSeenDocuments = ref([]);
const selectedPreSeenId = ref('');
const pending = ref(false);
const aiAnswer = ref(null);
let pollTimer = null;

onMounted(async () => {
  await loadPreSeenDocuments();
  await loadPastQuestions();
});

const loadPreSeenDocuments = async () => {
  try {
    const res = await api.get('/pre-seen-documents');
    if (res.data.success) preSeenDocuments.value = res.data.data;
  } catch (e) {
    console.error('Failed to load pre-seen documents', e);
  }
};

const loadPastQuestions = async () => {
  loadingPastQuestions.value = true;
  try {
    // TODO: Add student_id filter when auth is implemented
    const res = await api.get('/student-questions');
    if (res.data.success) {
      pastQuestions.value = res.data.data;
    }
  } catch (e) {
    console.error('Failed to load past questions', e);
  } finally {
    loadingPastQuestions.value = false;
  }
};

const submitQuestion = async () => {
  if (!question.value.trim()) return;
  if (!selectedPreSeenId.value) return;

  loading.value = true;
  pending.value = false;
  aiAnswer.value = null;

  try {
    const res = await api.post('/student-questions', {
      question_text: question.value,
      pre_seen_document_id: selectedPreSeenId.value,
      // TODO: include real student_id from auth store
    });
    if (res.data.success) {
      const id = res.data.data.id;
      pending.value = true;
      startPolling(id);
      // Reload past questions to show the new one
      await loadPastQuestions();
    }
  } catch (e) {
    console.error('Failed to submit question', e);
    alert('Failed to submit question.');
  } finally {
    loading.value = false;
  }
};

const loadPastQuestion = (q) => {
  question.value = q.question_text;
  selectedPreSeenId.value = q.pre_seen_document_id || '';
  
  // If the question has been answered, show the response
  if (q.status === 'answered' && q.bullet_point_answers && q.quoted_snippets) {
    aiAnswer.value = {
      bullet_point_answers: q.bullet_point_answers,
      quoted_snippets: q.quoted_snippets,
    };
    pending.value = false;
  } else if (q.status === 'pending') {
    // If still pending, start polling
    aiAnswer.value = null;
    pending.value = true;
    startPolling(q.id);
  } else {
    aiAnswer.value = null;
    pending.value = false;
  }
};

const startPolling = (id) => {
  if (pollTimer) clearInterval(pollTimer);
  pollTimer = setInterval(async () => {
    try {
      const res = await api.get(`/student-questions/${id}`);
      if (res.data.success) {
        const data = res.data.data;
        if (data.status === 'answered') {
          aiAnswer.value = {
            bullet_point_answers: data.bullet_point_answers || [],
            quoted_snippets: data.quoted_snippets || [],
          };
          pending.value = false;
          clearInterval(pollTimer);
          // Reload past questions to update status
          await loadPastQuestions();
        }
      }
    } catch (e) {
      console.error('Polling failed', e);
    }
  }, 2000);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInMs = now - date;
  const diffInMinutes = Math.floor(diffInMs / 60000);
  const diffInHours = Math.floor(diffInMs / 3600000);
  const diffInDays = Math.floor(diffInMs / 86400000);

  if (diffInMinutes < 1) return 'Just now';
  if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
  if (diffInHours < 24) return `${diffInHours}h ago`;
  if (diffInDays < 7) return `${diffInDays}d ago`;
  
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};
</script>
