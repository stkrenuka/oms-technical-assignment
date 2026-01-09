<!-- components/DeleteButton.vue -->
<template>
  <button
    @click="$emit('confirm')"
    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
  >
    Delete
  </button>
</template>
