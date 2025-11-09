<template>
  <Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
        Mock Examinations
      </h1>

      <div class="card mb-6">
        <p class="text-gray-600 dark:text-gray-400">
          Take timed mock examinations with system-generated questions designed to test
          your knowledge and memory. Get immediate AI-powered feedback on your
          performance.
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"
        ></div>
      </div>

      <!-- Mock Exams List -->
      <div v-else-if="mockExams.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="exam in mockExams"
          :key="exam.id"
          class="card-glow cursor-pointer hover:scale-105 transition-transform duration-200"
          @click="startExam(exam)"
        >
          <div class="flex items-start space-x-4">
            <div
              class="w-12 h-12 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center flex-shrink-0"
            >
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                ></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {{ exam.name }}
              </h3>
              <p
                v-if="exam.description"
                class="text-sm text-gray-600 dark:text-gray-400 mb-3"
              >
                {{ exam.description }}
              </p>

              <div class="flex flex-wrap gap-2 mb-3">
                <span
                  class="text-xs px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300 rounded"
                >
                  {{ exam.duration_minutes }} minutes
                </span>
                <span
                  v-if="exam.questions"
                  class="text-xs px-2 py-1 bg-secondary-100 dark:bg-secondary-900/30 text-secondary-800 dark:text-secondary-300 rounded"
                >
                  {{ exam.questions.length }} questions
                </span>
                <span
                  v-if="exam.is_active"
                  class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded"
                >
                  Active
                </span>
              </div>

              <button class="btn-primary text-sm">Start Exam</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="card text-center py-12">
        <svg
          class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          ></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
          No mock exams available
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
          Mock exams will appear here once created by your administrator.
        </p>
      </div>

      <!-- My Attempts Section -->
      <div v-if="myAttempts.length > 0" class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
          My Past Attempts
        </h2>
        <div class="space-y-4">
          <div
            v-for="attempt in myAttempts"
            :key="attempt.id"
            class="card cursor-pointer hover:shadow-lg transition-shadow"
            @click="viewAttempt(attempt)"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                  {{ attempt.mock_exam?.name || "Mock Exam" }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                  {{ formatDate(attempt.started_at) }}
                </p>
                <div class="flex flex-wrap gap-2">
                  <span
                    class="text-xs px-2 py-1 rounded"
                    :class="getStatusBadge(attempt.status)"
                  >
                    {{ attempt.status }}
                  </span>
                  <span
                    v-if="attempt.answers && attempt.answers.length > 0"
                    class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded"
                  >
                    {{ attempt.answers.length }} answers submitted
                  </span>
                </div>
              </div>
              <div class="text-right">
                <div
                  v-if="attempt.percentage !== null"
                  class="text-2xl font-bold"
                  :class="getScoreColor(attempt.percentage)"
                >
                  {{ attempt.percentage }}%
                </div>
                <p
                  v-if="attempt.total_marks_obtained !== null"
                  class="text-sm text-gray-600 dark:text-gray-400"
                >
                  {{ attempt.total_marks_obtained }} / {{ attempt.total_marks_available }}
                </p>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                  Not yet marked
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import Layout from "@/components/Layout.vue";
import api from "@/api/client";

const router = useRouter();
const mockExams = ref([]);
const myAttempts = ref([]);
const loading = ref(true);

onMounted(async () => {
  await loadMockExams();
  await loadMyAttempts();
});

const loadMockExams = async () => {
  loading.value = true;
  try {
    const response = await api.get("/mock-exams");
    if (response.data.success) {
      mockExams.value = response.data.data;
    }
  } catch (error) {
    console.error("Failed to load mock exams:", error);
  } finally {
    loading.value = false;
  }
};

const loadMyAttempts = async () => {
  try {
    // Get student from localStorage
    const studentData = localStorage.getItem("student");
    if (!studentData) return;

    const student = JSON.parse(studentData);
    const response = await api.get(`/mock-exams/attempts/${student.id}`);

    if (response.data.success) {
      myAttempts.value = response.data.data;
    }
  } catch (error) {
    console.error("Failed to load my attempts:", error);
  }
};

const startExam = (exam) => {
  router.push(`/mock-exams/${exam.id}`);
};

const viewAttempt = (attempt) => {
  router.push(`/mock-exams/attempt/${attempt.id}`);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const getScoreColor = (percentage) => {
  if (percentage >= 80) return "text-green-600 dark:text-green-400";
  if (percentage >= 60) return "text-yellow-600 dark:text-yellow-400";
  return "text-red-600 dark:text-red-400";
};

const getStatusBadge = (status) => {
  const badges = {
    in_progress: "bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300",
    completed: "bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300",
    submitted: "bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300",
  };
  return (
    badges[status] || "bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
  );
};
</script>
