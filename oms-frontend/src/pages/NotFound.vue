<!-- src/pages/NotFound.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-6">
    <div class="text-center max-w-md">
      <!-- 404 -->
      <h1 class="text-7xl font-extrabold text-gray-900 mb-4">
        404
      </h1>
      <!-- Message -->
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">
        Page not found
      </h2>
      <p class="text-gray-600 mb-6">
        Sorry, the page you are looking for doesn’t exist or has been moved.
      </p>
      <!-- Action -->
      <router-link to="/"
        class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-medium text-white  bg-blue-600 hover:bg-blue-700 transition">
        Go back home
      </router-link>
    </div>
  </div>
</template>
<script setup></script>