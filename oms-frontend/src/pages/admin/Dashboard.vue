<!-- src/pages/admin/Dashboard.vue -->
<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import { useOrderStore } from '@/stores/order'
const orderStore = useOrderStore()

orderStore.getStats()

</script>
<template>
  <DefaultLayout>
     <!-- Main Content -->
  <main class="flex-1 p-6">

    <!-- Header -->
    <h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <!-- Orders -->
      <div class="bg-white p-6 rounded shadow">
        <p class="text-gray-500">Total Orders</p>
        <p class="text-3xl font-bold mt-2">{{ orderStore.stats. total_orders}}</p>
      </div>

      <!-- Customers -->
      <div class="bg-white p-6 rounded shadow">
        <p class="text-gray-500">Total Customers</p>
        <p class="text-3xl font-bold mt-2">58</p>
      </div>

      <!-- Revenue -->
      <div class="bg-white p-6 rounded shadow">
        <p class="text-gray-500">Total Revenue</p>
        <p class="text-3xl font-bold mt-2">$18,450</p>
      </div>

    </div>

    <!-- Optional: Recent Activity -->
    <section class="mt-8 bg-white p-6 rounded shadow">
      <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
      <ul class="space-y-2 text-sm text-gray-700">
        <li>✔ Order #1024 placed by John Doe</li>
        <li>✔ New customer registered: Alice</li>
        <li>✔ Product "iPhone 15" added</li>
      </ul>
    </section>

  </main>
  </DefaultLayout>
</template>
