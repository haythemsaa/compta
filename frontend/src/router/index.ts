import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/auth/LoginView.vue')
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/expense-reports',
    name: 'ExpenseReports',
    component: () => import('../views/ExpenseReportsView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/expense-reports/new',
    name: 'ExpenseReportNew',
    component: () => import('../views/ExpenseReportFormView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/expense-reports/:id',
    name: 'ExpenseReportDetail',
    component: () => import('../views/ExpenseReportDetailView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/vehicles',
    name: 'Vehicles',
    component: () => import('../views/VehiclesView.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token')

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'Login' })
  } else {
    next()
  }
})

export default router
