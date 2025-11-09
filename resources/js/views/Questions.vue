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
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const question = ref('');
const loading = ref(false);
const recentQuestions = ref([]);
const preSeenDocuments = ref([]);
const selectedPreSeenId = ref('');
const pending = ref(false);
const aiAnswer = ref(null);
let pollTimer = null;

onMounted(async () => {
  await loadPreSeenDocuments();
});

const loadPreSeenDocuments = async () => {
  try {
    const res = await api.get('/pre-seen-documents');
    if (res.data.success) preSeenDocuments.value = res.data.data;
  } catch (e) {
    console.error('Failed to load pre-seen documents', e);
  }
};

const submitQuestion = async () => {
  if (!question.value.trim()) return;
  if (!selectedPreSeenId.value) return;

  loading.value = true;
  pending.value = false;
  aiAnswer.value = null;
  
  // Add to recent questions
  if (!recentQuestions.value.includes(question.value)) {
    recentQuestions.value.unshift(question.value);
    if (recentQuestions.value.length > 5) {
      recentQuestions.value.pop();
    }
  }

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
    }
  } catch (e) {
    console.error('Failed to submit question', e);
    alert('Failed to submit question.');
  } finally {
    loading.value = false;
  }
};

const loadQuestion = (q) => {
  question.value = q;
  aiAnswer.value = null;
  pending.value = false;
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
        }
      }
    } catch (e) {
      console.error('Polling failed', e);
    }
  }, 2000);
};
</script>
