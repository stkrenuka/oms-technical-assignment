<script setup>
import { useNotificationStore } from '@/stores/notification'
const notification = useNotificationStore()
</script>
<template>
  <div v-if="notification.show" class="fixed top-5 right-5 px-4 py-3 rounded shadow-lg text-white z-50" :class="{
    'bg-green-600': notification.type === 'success',
    'bg-red-600': notification.type === 'error',
    'bg-blue-600': notification.type === 'info',
  }">
    {{ notification.message }}
  </div>
</template>