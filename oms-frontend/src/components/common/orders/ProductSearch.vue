<script setup>
import { ref } from 'vue'
import { useProductStore } from '@/stores/product'
const emit = defineEmits(['select'])
const productStore = useProductStore()
const query = ref('')
const results = ref([])
const loading = ref(false)
const search = async () => {
  if (query.value.length < 2) {
    results.value = []
    return
  }
  loading.value = true
  results.value = await productStore.searchForOrder(query.value)
  loading.value = false
}
const selectProduct = (product) => {
  emit('select', product)
  query.value = product.name
  results.value = []
}
const props = defineProps({
  item: Array,
})
</script>
<template>
  <div class="relative">
    <input v-model="query" @input="search" placeholder="Search product..." class="border px-3 py-2 rounded w-full" />
    <ul v-if="results.length" class="absolute z-10 bg-white border w-full mt-1 max-h-48 overflow-auto rounded">
      <li v-for="product in results" :key="product.id" @click="selectProduct(product)"
        class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
        <div class="font-medium">{{ product.name }}</div>
        <div class="text-xs text-gray-500">₹{{ product.price }}</div>
      </li>
    </ul>
    <div v-if="loading" class="text-xs text-gray-400 mt-1">
      Searching…
    </div>
  </div>
</template>