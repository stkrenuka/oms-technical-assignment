<template>
  <div class="relative w-64">
    <input
      v-model="q"
      @input="search"
      placeholder="Search product..."
      class="input w-full"
    />

    <ul v-if="results.length" class="dropdown">
      <li
        v-for="p in results"
        :key="p.id"
        @click="select(p)"
      >
        {{ p.name }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['select'])
const q = ref('')
const results = ref([])

const products = [
  { id: 1, name: 'Product A', price: 125 },
  { id: 2, name: 'Product B', price: 200 },
]

const search = () => {
  if (q.value.length < 2) return (results.value = [])
  results.value = products.filter(p =>
    p.name.toLowerCase().includes(q.value.toLowerCase())
  )
}

const select = p => {
  emit('select', p)
  results.value = []
  q.value = p.name
}
</script>
