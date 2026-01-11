<template>
  <!-- Sidebar -->
  <aside class="w-64 bg-gray-900 text-white">
    <div class="p-6 text-xl font-bold border-b border-gray-700">
      Customer Panel
    </div>
    <nav class="p-4 space-y-2">
      <router-link :to="{ name: 'customer.dashboard' }" class="block px-4 py-2 rounded hover:bg-gray-800"
        active-class="bg-gray-800">
        Dashboard
      </router-link>
      <router-link :to="{ name: 'customer.orders' }" class="block px-4 py-2 rounded hover:bg-gray-800">
        Orders
      </router-link>
      <LogOut />
    </nav>
  </aside>
</template>
<script setup>
import LogOut from '@/components/common/LogOut.vue'
</script>