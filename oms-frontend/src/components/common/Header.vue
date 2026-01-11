<template>
  <!-- Right Side -->
  <div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
      <!-- Left: Page title -->
      <div>
        <h1 class="text-xl font-semibold text-gray-800">
          {{ pageTitle }}
        </h1>
        <p class="text-sm text-gray-500">
          Welcome back
        </p>
      </div>
      <!-- Right: User info -->
      <div class="flex items-center space-x-4">
        <div class="text-right">
          <p class="text-sm font-medium text-gray-800">
            {{ userName }}
          </p>
          <p class="text-xs text-gray-500">
          </p>
        </div>
        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 font-bold">
          {{ initials }}
        </div>
      </div>
    </header>
    <!-- Page Content -->
    <main class="flex-1 p-6">
      <slot />
    </main>
  </div>
</template>
<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRoute, useRouter } from 'vue-router'
const auth = useAuthStore()
const route = useRoute()
const router = useRouter()
// User info
const userName = computed(() => auth.user?.name ?? '')
const userRole = computed(() => auth.user?.role ?? '')
// Initials (JD from John Doe)
const initials = computed(() => {
  if (!userName.value) return ''
  return userName.value
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
})
// Dynamic page title (based on route)
const pageTitle = computed(() => {
  switch (route.name) {
    case 'admin.dashboard':
      return 'Admin Dashboard'
    case 'customer.dashboard':
      return 'Customer Dashboard'
    default:
      return 'Dashboard'
  }
})
</script>