<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Section -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          Welcome back, <span class="bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">{{ student?.name }}</span>!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Master preseen case studies with AI-powered questioning, multi-model analysis, and comprehensive practice tools.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card-glow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Completed Questions</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center shadow-glow-purple">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="card-glow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Mock Exams</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-secondary-600 to-primary-600 rounded-lg flex items-center justify-center shadow-glow-blue">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="card-glow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Average Score</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">0%</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center shadow-glow-purple">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="card-glow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Study Hours</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">0h</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-secondary-600 to-primary-600 rounded-lg flex items-center justify-center shadow-glow-blue">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <router-link to="/preseen" class="card-glow hover:scale-105 transition-transform duration-200">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">View Pre-seen</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Access CIMA pre-seen case studies</p>
              </div>
            </div>
          </router-link>

          <router-link to="/questions" class="card-glow hover:scale-105 transition-transform duration-200">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-secondary-600 to-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Ask Questions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Get AI-powered answers</p>
              </div>
            </div>
          </router-link>

          <router-link to="/theory-models" class="card-glow hover:scale-105 transition-transform duration-200">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Theory Models</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Apply SWOT, PEST, and more</p>
              </div>
            </div>
          </router-link>

          <router-link to="/past-papers" class="card-glow hover:scale-105 transition-transform duration-200">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-secondary-600 to-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Past Papers</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Review last 10 exam papers</p>
              </div>
            </div>
          </router-link>

          <router-link to="/practice" class="card-glow hover:scale-105 transition-transform duration-200">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Practice Questions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">50 questions with AI feedback</p>
              </div>
            </div>
          </router-link>

          <router-link to="/mock-exams" class="card-glow hover:scale-105 transition-transform duration-200">
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 bg-gradient-to-br from-secondary-600 to-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Mock Exams</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Take timed mock examinations</p>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Features Overview -->
      <div class="card">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Key Features</h2>
        <div class="space-y-4">
          <div class="flex items-start space-x-3">
            <div class="w-6 h-6 bg-gradient-to-br from-primary-600 to-secondary-600 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-white text-xs font-bold">✓</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white">AI-Powered Question Answering</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Ask questions about pre-seen materials and get intelligent, contextual answers powered by advanced AI.</p>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-6 h-6 bg-gradient-to-br from-primary-600 to-secondary-600 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-white text-xs font-bold">✓</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white">Theory Model Application</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Choose from 10 business theory models (SWOT, PEST, Porter's Five Forces, etc.) and apply them to your case studies.</p>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-6 h-6 bg-gradient-to-br from-primary-600 to-secondary-600 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-white text-xs font-bold">✓</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white">Comprehensive Past Paper Analysis</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Access last 10 post-examiner guidelines with questions, answers, marking schemes, and examiner feedback.</p>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-6 h-6 bg-gradient-to-br from-primary-600 to-secondary-600 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
              <span class="text-white text-xs font-bold">✓</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 dark:text-white">Automated Marking & Feedback</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Submit answers to 50 practice questions and receive instant AI-powered marking with detailed feedback.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import Layout from '@/components/Layout.vue';

const authStore = useAuthStore();
const student = computed(() => authStore.student);
</script>
