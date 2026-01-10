    <script setup>
    import CustomerLayout from '@/layouts/CustomerLayout.vue';
    import OrdersTable from '@/components/common/orders/OrdersTable.vue';
    import { useAuthStore } from '@/stores/auth'
    const authStore = useAuthStore()

</script>
<template>
    <CustomerLayout>
          <OrdersTable
      :userRole="authStore.role"
      :authUser="authStore.user"
    />
    </CustomerLayout>
</template>
