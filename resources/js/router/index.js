import { createRouter, createWebHistory } from 'vue-router';

// Auth guard
const requireAuth = (to, from, next) => {
	const token = localStorage.getItem('token');
	if (!token) {
		next('/login');
	} else {
		next();
	}
};

const router = createRouter({
	history: createWebHistory(),
	routes: [
		{
			path: '/',
			redirect: '/dashboard',
		},
		{
			path: '/login',
			name: 'Login',
			component: () => import('@/views/auth/Login.vue'),
			meta: { guest: true },
		},
		{
			path: '/register',
			name: 'Register',
			component: () => import('@/views/auth/Register.vue'),
			meta: { guest: true },
		},
		{
			path: '/dashboard',
			name: 'Dashboard',
			component: () => import('@/views/Dashboard.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/preseen',
			name: 'Preseen',
			component: () => import('@/views/Preseen.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/questions',
			name: 'Questions',
			component: () => import('@/views/Questions.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/business-models',
			name: 'BusinessModels',
			component: () => import('@/views/BusinessModels.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/past-papers',
			name: 'PastPapers',
			component: () => import('@/views/PastPapers.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/practice',
			name: 'Practice',
			component: () => import('@/views/Practice.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/mock-exams',
			name: 'MockExams',
			component: () => import('@/views/MockExams.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/mock-exams/:id',
			name: 'MockExamDetail',
			component: () => import('@/views/MockExamDetail.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/mock-exams/attempt/:id',
			name: 'MockExamAttemptView',
			component: () => import('@/views/MockExamAttemptView.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/mock-exams/attempts/:id/summary',
			name: 'MockExamAttemptSummary',
			component: () => import('@/views/MockExamAttemptSummary.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
		},
		{
			path: '/exam/:type/:id',
			name: 'QuestionInterface',
			component: () => import('@/views/QuestionInterface.vue'),
			beforeEnter: requireAuth,
			meta: { requiresAuth: true },
			props: true,
		},
	],
});

// Global navigation guard
router.beforeEach((to, from, next) => {
	const token = localStorage.getItem('token');

	if (to.meta.guest && token) {
		next('/dashboard');
	} else {
		next();
	}
});

export default router;

