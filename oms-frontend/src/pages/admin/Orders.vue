<script setup>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import OrdersTable from '@/components/common/orders/OrdersTable.vue'
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()
</script>
<template>
  <DefaultLayout>
    <OrdersTable :userRole="authStore.role" :authUser="authStore.user" />
  </DefaultLayout>
</template>