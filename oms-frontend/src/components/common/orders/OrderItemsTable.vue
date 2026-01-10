<script setup>
import ProductSearch from '@/components/common/orders/ProductSearch.vue'

defineProps({
  items: { type: Array, default: () => [] }
})

const emit = defineEmits(['add', 'remove', 'product-selected'])
</script>

<template>
  <h4 class="font-medium mb-2">Order Items</h4>

  <table class="w-full text-sm mb-2">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 border">Search Products</th>
        <th class="p-2 border">Qty</th>
        <th class="p-2 border">Price</th>
        <th class="p-2 border">Total</th>
        <th class="p-2 border"></th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="(item, i) in items" :key="i">
        <td class="border p-2">
          <ProductSearch
            @select="product => emit('product-selected', { product, item })"
          />
        </td>

        <td class="border p-2">
          <input v-model.number="item.qty" type="number" min="1"
            class="border px-2 py-1 rounded w-20" />
        </td>

        <td class="border p-2">
          <input type="number" :value="item.price"
            class="border px-2 py-1 rounded w-24 bg-gray-100" readonly />
        </td>

        <td class="border p-2 font-medium">
          ₹{{ item.qty * item.price }}
        </td>

        <td class="border p-2">
          <button class="text-red-600" @click="$emit('remove', i)">✕</button>
        </td>
      </tr>

      <tr v-if="items.length === 0">
        <td colspan="5" class="p-4 text-center text-gray-500">
          No items added
        </td>
      </tr>
    </tbody>
  </table>

  <button class="text-blue-600" @click="$emit('add')">
    + Add Item
  </button>
</template>
