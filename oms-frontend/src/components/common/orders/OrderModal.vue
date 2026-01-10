<script setup>
import { ref, onMounted } from 'vue'
import CustomerSearch from '@/components/common/orders/CustomerSearch.vue'
import OrderItemsTable from './OrderItemsTable.vue'
import { useOrderStore } from '@/stores/order'
import useOrderForm from '@/composables/useOrderForm'
const orderForm = useOrderForm()
const orderStore = useOrderStore()

defineProps({
  form: Object,
  editing: Boolean,
  total: Number,
  userRole: String,
  authUser: Object,
})

defineEmits(['close', 'save', 'add-item', 'remove-item', 'product-selected', 'customer-selected', 'customer-cleared'])
onMounted(async () => {
  const statuses = await orderForm.getOrderStatuses();
  orderStore.setOrderStatuses(statuses)

})
</script>

<template>

  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-[700px]">
      <h3 class="text-lg font-semibold mb-4">
        {{ editing ? 'Edit Order' : 'Create Order' }}
      </h3>
      <CustomerSearch v-if="userRole === 'admin'" @select="$emit('customer-selected', $event)"
        @clear="$emit('customer-cleared')" />


      <div v-else class="mb-4">
        <label class="block text-sm font-medium mb-1">Customer</label>
        <input class="border px-3 py-2 rounded w-full bg-gray-100" :value="authUser.name" readonly />
      </div>

      <select  v-if="userRole === 'admin'" v-model="form.status" class="border px-3 py-2 rounded w-full mb-4 mt-4">
        <option v-for="status in orderStore.ordersStatus" :key="status.id" :value="status.id">
          {{ status.name }}
        </option>
      </select>

      <OrderItemsTable :items="form.items" @add="$emit('add-item')" @remove="$emit('remove-item', $event)"
        @product-selected="$emit('product-selected', $event)" />
      <p v-if="orderStore.errors.customer_id" class="text-red-500 text-sm mt-1">
        {{ orderStore.errors.items[0] }}
      </p>

      <div class="text-right font-semibold mt-4">
        Total: ₹{{ total }}
      </div>

      <div class="flex justify-end space-x-2 mt-6">
        <button class="px-4 py-2 border rounded" @click="$emit('close')">
          Cancel
        </button>
        <button class="px-4 py-2 bg-green-600 text-white rounded" @click="$emit('save')">
          Save
        </button>
      </div>

    </div>
  </div>
</template>