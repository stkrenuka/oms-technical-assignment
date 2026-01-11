<script setup>
import { ref } from 'vue';
import { useCustomerStore } from '@/stores/customer'
import { useOrderStore } from '@/stores/order'
const orderStore = useOrderStore()
const emit = defineEmits(['select', 'clear'])
const query = ref('')
const results = ref([])
const selectedCustomer = ref(null)
const loading = ref(false)
const search = async () => {
  const customerStore = useCustomerStore()
  if (query.value.length < 2) {
    results.value = []
    return
  }
  loading.value = true
  results.value = await customerStore.searchForOrder(query.value)
  loading.value = false
}
const selectCustomer = (customer) => {
  orderStore.selectedCustomer = customer.id
  selectedCustomer.value = customer
  emit('select', customer)
  query.value = customer.name
  results.value = []
}
const clear = () => {
  selectedCustomer.value = null
  query.value = ''
  results.value = []
  emit('clear')
}
</script>
<template>
  <div class="relative">
    <label class="block text-sm font-medium mb-1">Customer</label>
    <input v-model="query" @input="search" placeholder="Search customer by name or email"
      class="border px-3 py-2 rounded w-full" />
    <p v-if="orderStore.errors.customer_id" class="text-red-500 text-sm mt-1">
      {{ orderStore.errors.customer_id[0] }}
    </p>
    <!-- Dropdown -->
    <ul v-if="results.length" class="absolute z-10 bg-white border w-full mt-1 max-h-48 overflow-auto rounded">
      <li v-for="customer in results" :key="customer.id" @click="selectCustomer(customer)"
        class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
        <div class="font-medium">{{ customer.name }}</div>
        <div class="text-xs text-gray-500">{{ customer.email }}</div>
      </li>
    </ul>
    <!-- Selected -->
    <div v-if="selectedCustomer" class="mt-2 p-2 border rounded bg-gray-50 flex justify-between items-center">
      <div>
        <div class="font-medium">{{ selectedCustomer.name }}</div>
        <div class="text-xs text-gray-500">{{ selectedCustomer.email }}</div>
      </div>
      <button class="text-red-600 text-sm" @click="clear">
        Change
      </button>
    </div>
    <div v-if="loading" class="text-xs text-gray-400 mt-1">
      Searching…
    </div>
  </div>
</template>