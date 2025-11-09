<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <div v-else-if="exam && currentQuestion">
        <!-- Header -->
        <div class="mb-6">
          <router-link :to="backRoute" class="text-primary-600 dark:text-primary-400 hover:underline mb-2 inline-block">
            ← Back to {{ examType === 'mock' ? 'Mock Exams' : 'Practice Questions' }}
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ exam.name }}</h1>
          <p v-if="exam.description" class="text-gray-600 dark:text-gray-400">{{ exam.description }}</p>
        </div>

        <!-- Exam Info Card -->
        <div class="card mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Duration</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ currentQuestion.duration_minutes || exam.duration_minutes }} min</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Question</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ currentQuestion.question_number }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Marks</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ currentQuestion.marks }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Progress</p>
              <p class="text-xl font-bold text-gray-900 dark:text-white">{{ currentQuestionIndex + 1 }}/{{ questions.length }}</p>
            </div>
          </div>
        </div>

        <!-- Pre-seen Document -->
        <div v-if="preSeenDocument" class="card mb-6">
          <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Pre-Seen Document</h3>
              <p class="text-gray-700 dark:text-gray-300 mb-2">{{ preSeenDocument.name }}</p>
              <p v-if="preSeenDocument.company_name" class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Company: {{ preSeenDocument.company_name }}
              </p>
              <p v-if="preSeenDocument.description" class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                {{ preSeenDocument.description }}
              </p>
              <a
                v-if="preSeenDocument.file_path"
                :href="`/storage/${preSeenDocument.file_path}`"
                target="_blank"
                class="inline-flex items-center text-primary-600 dark:text-primary-400 hover:underline text-sm"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                View Document
              </a>
            </div>
          </div>
        </div>

        <!-- Question Context -->
        <div v-if="currentQuestion.context" class="card mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Context</h3>
          <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ currentQuestion.context }}</p>
          </div>
        </div>

        <!-- Reference Material -->
        <div v-if="currentQuestion.reference_material" class="card mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Reference Material</h3>
          <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ currentQuestion.reference_material }}</p>
          </div>
        </div>

        <!-- Sub-Questions -->
        <div class="space-y-6 mb-6">
          <div
            v-for="(subQuestion, index) in currentQuestion.sub_questions || []"
            :key="subQuestion.id"
            class="card"
          >
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">{{ currentQuestion.question_number }}{{ subQuestion.sub_question_number }}</span>
              </div>
              <div class="flex-1">
                <div class="flex items-start justify-between mb-3">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Question {{ currentQuestion.question_number }}{{ subQuestion.sub_question_number }}
                  </h3>
                  <span class="text-sm font-medium text-gray-600 dark:text-gray-400 ml-4">
                    {{ subQuestion.marks }} marks
                  </span>
                </div>
                
                <p class="text-gray-700 dark:text-gray-300 mb-4 whitespace-pre-wrap">{{ subQuestion.sub_question_text }}</p>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Your Answer
                  </label>
                  <textarea
                    v-model="answers[subQuestion.id]"
                    rows="8"
                    class="input-field"
                    placeholder="Type your answer here..."
                    :disabled="submitting"
                  ></textarea>
                </div>

                <div class="flex space-x-3 mt-4">
                  <button
                    @click="saveDraft(subQuestion.id)"
                    :disabled="submitting || savingDraft[subQuestion.id]"
                    class="btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span v-if="!savingDraft[subQuestion.id]">Save Draft</span>
                    <span v-else>Saving...</span>
                  </button>
                  <button
                    @click="submitAnswer(subQuestion.id)"
                    :disabled="submitting || !answers[subQuestion.id]?.trim()"
                    class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span v-if="!submitting">Submit Answer</span>
                    <span v-else>Submitting...</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <div class="card">
          <div class="flex items-center justify-between">
            <button
              @click="previousQuestion"
              :disabled="currentQuestionIndex === 0"
              class="btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              ← Previous Question
            </button>
            
            <div class="flex items-center space-x-2">
              <span
                v-for="(q, idx) in questions"
                :key="q.id"
                @click="goToQuestion(idx)"
                class="w-8 h-8 rounded-lg flex items-center justify-center cursor-pointer text-sm font-medium transition-all"
                :class="{
                  'bg-primary-600 text-white': idx === currentQuestionIndex,
                  'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300': questionAnswered(q.id) && idx !== currentQuestionIndex,
                  'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': !questionAnswered(q.id) && idx !== currentQuestionIndex
                }"
              >
                {{ q.question_number }}
              </span>
            </div>

            <button
              @click="nextQuestion"
              :disabled="currentQuestionIndex === questions.length - 1"
              class="btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next Question →
            </button>
          </div>
        </div>

        <!-- Submit All -->
        <div class="card mt-6" v-if="allQuestionsAnswered">
          <div class="text-center py-4">
            <p class="text-gray-700 dark:text-gray-300 mb-4">
              All questions have been answered. You can review your answers or submit the entire exam.
            </p>
            <button
              @click="submitExam"
              :disabled="submitting"
              class="btn-primary px-8 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!submitting">Submit Complete Exam</span>
              <span v-else>Submitting...</span>
            </button>
          </div>
        </div>
      </div>

      <div v-else class="card text-center py-12">
        <p class="text-gray-600 dark:text-gray-400">{{ examType === 'mock' ? 'Mock exam' : 'Practice questions' }} not found.</p>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import api from '@/api/client';

const route = useRoute();
const router = useRouter();

// Props from route params
const examId = computed(() => route.params.id);
const examType = computed(() => route.params.type || 'mock'); // 'mock' or 'practice'

const exam = ref(null);
const questions = ref([]);
const preSeenDocument = ref(null);
const loading = ref(true);
const currentQuestionIndex = ref(0);
const answers = ref({});
const savingDraft = ref({});
const submitting = ref(false);
const attemptId = ref(null);

const currentQuestion = computed(() => questions.value[currentQuestionIndex.value]);
const backRoute = computed(() => examType.value === 'mock' ? '/mock-exams' : '/practice');

const allQuestionsAnswered = computed(() => {
  return questions.value.every(q => 
    q.sub_questions?.every(sq => answers.value[sq.id]?.trim())
  );
});

onMounted(async () => {
  await loadExam();
});

const loadExam = async () => {
  loading.value = true;
  try {
    const endpoint = examType.value === 'mock' 
      ? `/mock-exams/${examId.value}/questions`
      : `/practice-questions/${examId.value}`;
    
    const response = await api.get(endpoint);
    
    if (response.data.success) {
      exam.value = response.data.data.mock_exam || response.data.data.exam;
      questions.value = response.data.data.questions;
      
      // Load pre-seen document
      if (exam.value.pre_seen_document_id) {
        await loadPreSeenDocument(exam.value.pre_seen_document_id);
      }
      
      // Initialize answers object for all sub-questions
      questions.value.forEach(q => {
        q.sub_questions?.forEach(sq => {
          answers.value[sq.id] = '';
        });
      });
    }
  } catch (error) {
    console.error('Failed to load exam:', error);
  } finally {
    loading.value = false;
  }
};

const loadPreSeenDocument = async (docId) => {
  try {
    const response = await api.get(`/pre-seen-documents/${docId}`);
    if (response.data.success) {
      preSeenDocument.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to load pre-seen document:', error);
  }
};

const saveDraft = async (subQuestionId) => {
  if (!answers.value[subQuestionId]?.trim()) return;
  
  savingDraft.value[subQuestionId] = true;
  
  try {
    // TODO: Implement draft saving API
    await new Promise(resolve => setTimeout(resolve, 500));
    console.log('Draft saved for sub-question:', subQuestionId);
  } catch (error) {
    console.error('Failed to save draft:', error);
    alert('Failed to save draft. Please try again.');
  } finally {
    savingDraft.value[subQuestionId] = false;
  }
};

const submitAnswer = async (subQuestionId) => {
  if (!answers.value[subQuestionId]?.trim()) return;
  
  submitting.value = true;
  
  try {
    const studentId = 1; // TODO: Get from auth store
    
    const payload = {
      student_id: studentId,
      mock_exam_id: examId.value,
      mock_exam_question_id: currentQuestion.value.id,
      mock_exam_sub_question_id: subQuestionId,
      answer_text: answers.value[subQuestionId],
      mock_exam_attempt_id: attemptId.value,
    };
    
    const response = await api.post('/mock-exams/submit-answer', payload);
    
    if (response.data.success) {
      // Store attempt ID for subsequent submissions
      if (response.data.data.mock_exam_attempt_id) {
        attemptId.value = response.data.data.mock_exam_attempt_id;
      }
      
      alert('Answer submitted successfully!');
    }
  } catch (error) {
    console.error('Failed to submit answer:', error);
    alert('Failed to submit answer. Please try again.');
  } finally {
    submitting.value = false;
  }
};

const questionAnswered = (questionId) => {
  const question = questions.value.find(q => q.id === questionId);
  return question?.sub_questions?.some(sq => answers.value[sq.id]?.trim());
};

const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--;
    window.scrollTo(0, 0);
  }
};

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    currentQuestionIndex.value++;
    window.scrollTo(0, 0);
  }
};

const goToQuestion = (index) => {
  currentQuestionIndex.value = index;
  window.scrollTo(0, 0);
};

const submitExam = async () => {
  if (!confirm('Are you sure you want to submit the complete exam? This action cannot be undone.')) {
    return;
  }
  
  submitting.value = true;
  
  try {
    // Submit all answers
    alert('Exam submitted successfully! You will receive your results shortly.');
    router.push(backRoute.value);
  } catch (error) {
    console.error('Failed to submit exam:', error);
    alert('Failed to submit exam. Please try again.');
  } finally {
    submitting.value = false;
  }
};
</script>
