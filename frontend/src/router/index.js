import { createRouter, createWebHashHistory } from 'vue-router';
import DoctorManagement from '../components/DoctorManagement.vue';
import PatientManagement  from '@/components/PatientManagement.vue';
import AppointmentManagement from '@/components/AppointmentManagement.vue';
import UserManagement from '@/components/UserManagement.vue';
import MedicalRecordManagement from '@/components/MedicalRecordManagement.vue';

const routes = [
  {
    path: '/',
    name: 'landing',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/RegisterView.vue')
  },
  {
    path: '/doctors',
    name: 'DoctorManagement',
    component: DoctorManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/patients',
    name: 'PatientManagement',
    component: PatientManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/appointments',
    name: 'AppointmentManagement',
    component: AppointmentManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/medicalrecords',
    name: 'MedicalRecordManagement',
    component: MedicalRecordManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'UserManagement',
    component: UserManagement,
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/logout',
    name: 'logout',
    component: () => import('../views/LogoutView.vue'),
    meta: { requiresAuth: true }
  },
  // Add other protected routes here
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

// Navigation Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.matched.some(record => record.meta.requiresAuth) && !token) {
    next({ name: 'login' }) // Redirect to login if no token is found
  } else {
    next() // Proceed to route
  }
})

export default router
