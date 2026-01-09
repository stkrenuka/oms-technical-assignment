import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // 🔓 AUTH ROUTES
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/auth/Login.vue'),
      meta: { guest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/pages/auth/Register.vue'),
      meta: { guest: true },
    },
    // {
    //   path: '/logout',
    //   name: 'logout',
    //   component: () => import('@/pages/auth/Logout.vue'),
    //   meta: { requiresAuth: true },
    // },

    // 🔐 ADMIN ROUTES
    {
      path: '/admin',
      meta: { requiresAuth: true, roles: ['admin'] },
      children: [
        {
          path: 'dashboard',
          name: 'admin.dashboard',
          component: () => import('@/pages/admin/Dashboard.vue'),
        },
        {
          path: 'orders',
          name: 'admin.orders',
          component: () => import('@/pages/admin/Orders.vue'),
        },
        {
          path: 'customers',
          name: 'admin.customers',
          component: () => import('@/pages/admin/Customers.vue'),
        },
        {
          path: 'products',
          name: 'admin.products',
          component: () => import('@/pages/admin/Products.vue'),
        },
      ],
    },

    // 🔐 CUSTOMER ROUTES
    {
      path: '/customer',
      meta: { requiresAuth: true, roles: ['customer'] },
      children: [
        {
          path: 'dashboard',
          name: 'customer.dashboard',
          component: () => import('@/pages/customer/Dashboard.vue'),
        },
        {
          path: 'orders',
          name: 'customer.orders',
          component: () => import('@/pages/customer/Orders.vue'),
        },
        {
          path: 'products',
          name: 'customer.products',
          component: () => import('@/pages/customer/Products.vue'),
        },
      ],
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  if (!auth.authenticated && localStorage.getItem('token')) {
    await auth.getUser()
  }

  if (to.meta.requiresAuth && !auth.authenticated) {
    return next({ name: 'login' })
  }

  if (to.meta.roles && !to.meta.roles.includes(auth.user?.role)) {
    return next({ name: 'login' })
  }

  next()
})


export default router
