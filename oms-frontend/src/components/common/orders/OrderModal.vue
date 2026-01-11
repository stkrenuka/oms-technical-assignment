<script setup>
import { ref, onMounted, computed } from 'vue'
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
const allowedStatuses = computed(() => {
  const current = orderStore.statusForm.current_status_id
  if (!current) return []
  const map = {
    1: [2],           // Draft → Pending
    2: [3, 7],        // Pending → Confirmed / Cancelled
    3: [4, 7],        // Confirmed → Processing / Cancelled
    4: [5, 7],        // Processing → Shipped / Cancelled
    5: [6],           // Shipped → Delivered
    6: [],            // Delivered
    7: [],            // Cancelled
  }
  return orderStore.ordersStatus.filter(
    s => map[current]?.includes(s.id)
  )
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
        {{ editing ? 'Change Order Status' : 'Create Order' }}
      </h3>
      <div v-if="!editing">
        <CustomerSearch v-if="userRole === 'admin'" @select="$emit('customer-selected', $event)"
          @clear="$emit('customer-cleared')" />
        <div v-else class="mb-4">
          <label class="block text-sm font-medium mb-1">Customer</label>
          <input class="border px-3 py-2 rounded w-full bg-gray-100" :value="authUser.name" readonly />
        </div>
      </div>
      <select v-if="userRole === 'admin' && !editing" v-model="form.status"
        class="border px-3 py-2 rounded w-full mb-4 mt-4">
        <option v-for="status in orderStore.ordersStatus" :key="status.id" :value="status.id">
          {{ status.name }}
        </option>
      </select>
      <select v-if="editing" v-model="orderStore.statusForm.next_status_id" class="w-full border rounded p-2 mb-3">
        <option v-for="value in allowedStatuses" :key="value.id" :value="value.id">
          {{ value.name }}
        </option>
      </select>
      <div v-if="!editing">
        <OrderItemsTable :items="form.items" @add="$emit('add-item')" @remove="$emit('remove-item', $event)"
          @product-selected="$emit('product-selected', $event)" />
        <p v-if="orderStore.errors.items" class="text-red-500 text-sm mt-1">
          {{ orderStore.errors.items[0] }}
        </p>
        <div class="text-right font-semibold mt-4">
          Total: ${{ total }}
        </div>
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