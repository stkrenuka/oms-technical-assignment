<template>
  <div class="modal">
    <div class="modal-box">
      <h3 class="text-lg font-semibold mb-4">
        {{ order ? 'Edit' : 'Create' }} Order
      </h3>

      <CustomerSearch
        v-if="userRole === 'admin'"
        @select="setCustomer"
      />

      <select v-model="form.status" class="input mb-4">
        <option>Draft</option>
        <option>Confirmed</option>
        <option>Processing</option>
        <option>Dispatched</option>
        <option>Delivered</option>
        <option>Cancelled</option>
      </select>

      <h4 class="font-medium mb-2">Items</h4>

      <div v-for="(item, i) in form.items" :key="i" class="flex gap-2 mb-2">
        <ProductSearch @select="p => setProduct(item, p)" />
        <input type="number" v-model.number="item.qty" min="1" class="input w-20" />
        <input readonly :value="item.price" class="input w-24 bg-gray-100" />
        <span class="font-medium">₹{{ item.qty * item.price }}</span>
        <button @click="remove(i)">✕</button>
      </div>

      <button class="text-blue-600" @click="addItem">+ Add Item</button>

      <div class="text-right font-semibold mt-4">
        Total: ₹{{ total }}
      </div>

      <div class="flex justify-end gap-2 mt-4">
        <button @click="$emit('close')">Cancel</button>
        <button class="btn-green" @click="save">Save</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ProductSearch from './ProductSearch.vue'
import CustomerSearch from './CustomerSearch.vue'

const props = defineProps({
  order: Object,
  userRole: String,
})

const emit = defineEmits(['close', 'save'])

const form = ref(
  props.order
    ? structuredClone(props.order)
    : { customer_id: null, status: 'Draft', items: [] }
)

const addItem = () =>
  form.value.items.push({ product_id: null, qty: 1, price: 0 })

const remove = i => form.value.items.splice(i, 1)

const setProduct = (item, product) => {
  item.product_id = product.id
  item.price = product.price
}

const setCustomer = customer => {
  form.value.customer_id = customer.id
}

const total = computed(() =>
  form.value.items.reduce((s, i) => s + i.qty * i.price, 0)
)

const save = () => {
  emit('save', { ...form.value, total: total.value })
}
</script>
