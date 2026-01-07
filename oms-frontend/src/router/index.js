import { createRouter, createWebHistory } from 'vue-router'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
  {
    path: '/login',
    name: 'login',
     component: () => import('@/pages/Login.vue'),
  },
   {
    path: '/register',
    name: 'register',
    component: () => import('@/pages/Register.vue'),
  },
  {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/pages/Dashboard.vue'),
      meta: { auth: true },
    },
],
})

export default router
