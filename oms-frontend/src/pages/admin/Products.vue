<script setup>
import { onMounted ,watch } from 'vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import { useProductStore } from '@/stores/product'

const productStore = useProductStore()
const API_URL = import.meta.env.VITE_API_URL
onMounted(productStore.loadProducts)
let debounceTimer = null;
watch(
  () => productStore.search,
  () => {
    clearTimeout(debounceTimer)

    debounceTimer = setTimeout(() => {
      productStore.loadProducts(1) // reset to page 1
    }, 400) // ⏱ debounce delay
  }
)
</script>



<template>
  <DefaultLayout>
    <div class="bg-white rounded shadow p-6">

      <!-- Header -->
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Products</h2>
        <input v-model="productStore.search" type="text" placeholder="Search by product name or description..."
          class="w-full md:w-1/3 border p-2 rounded" />
        <button class="px-4 py-2 bg-green-600 text-white rounded" @click="productStore.openCreate">
          + Add Product
        </button>
      </div>

      <!-- Table -->
      <table class="w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Product</th>
            <th class="p-2 border">Price</th>
            <th class="p-2 border">Stock</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">Image</th>
            <th class="p-2 border">Action</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="product in productStore.products" :key="product.id" class="text-center">
            <td class="p-2 border">{{ product.id }}</td>
            <td class="p-2 border">{{ product.name }}</td>
            <td class="p-2 border">₹{{ product.price }}</td>
            <td class="p-2 border">{{ product.stock }}</td>
            <!-- Status -->
            <td class="p-2 border">
              <button class="px-3 py-1 rounded text-white" :class="product.status === 'active'
                ? 'bg-green-600'
                : 'bg-gray-500'" @click="productStore.toggleStatus(product)">
                {{ product.status }}
              </button>
            </td>
            <td class="p-2 border"><img :src="`${API_URL}/storage/${product.image}`"
                class="w-12 h-12 object-cover rounded" /></td>


            <!-- Actions -->
            <td class="p-2 border space-x-2">
              <button class="px-3 py-1 bg-blue-600 text-white rounded" @click="productStore.editProduct(product)">
                Edit
              </button>
              <button class="px-3 py-1 bg-red-600 text-white rounded" @click="productStore.deleteProduct(product.id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
<div class="flex justify-between mt-4">
  <button
    class="px-4 py-2 bg-gray-300 rounded"
    :disabled="!productStore.pagination.prev_page"
    @click="productStore.prevPage"
  >
    Previous
  </button>

  <span>
    Page {{ productStore.pagination.current_page }}
    of {{ productStore.pagination.last_page }}
  </span>

  <button
    class="px-4 py-2 bg-gray-300 rounded"
    :disabled="!productStore.pagination.next_page"
    @click="productStore.nextPage"
  >
    Next
  </button>
</div>


      <!-- Modal -->
<div v-if="productStore.showModal"
  class="fixed inset-0 bg-black/50 flex items-center justify-center">
  <div class="bg-white p-6 rounded w-96">

    <h3 class="text-lg font-semibold mb-4">
      {{ productStore.isEdit ? 'Edit Product' : 'Add Product' }}
    </h3>

    <input v-model="productStore.form.name"
      placeholder="Product name"
      class="w-full mb-2 border p-2 rounded" />
      <p v-if="productStore.errors.name" class="text-red-500 text-sm mt-1">
  {{ productStore.errors.name[0] }}
</p>

    <input v-model="productStore.form.price"
      type="number"
      placeholder="Price"
      class="w-full mb-2 border p-2 rounded" />
<p v-if="productStore.errors.price" class="text-red-500 text-sm mt-1">
  {{ productStore.errors.price[0] }}
</p>
    <input v-model="productStore.form.stock"
      type="number"
      placeholder="Stock"
      class="w-full mb-2 border p-2 rounded" />
<p v-if="productStore.errors.stock" class="text-red-500 text-sm mt-1">
  {{ productStore.errors.stock[0] }}
</p>

    <textarea v-model="productStore.form.description"
      placeholder="Description"
      class="w-full mb-3 border p-2 rounded">
    </textarea>

    <!-- ✅ Existing image preview (edit mode only) -->
    <div v-if="productStore.isEdit && productStore.form.image_url" class="mb-3">
      <p class="text-sm text-gray-600 mb-1">Current Image</p>
      <img
        :src="productStore.form.image_url"
        class="w-20 h-20 object-cover rounded border"
      />
    </div>

    <!-- ✅ Image upload -->
    <input
      type="file"
      accept="image/*"
      @change="onImageChange"
      class="w-full mb-4"
    />

    <div class="flex justify-end space-x-2">
      <button class="px-4 py-2 bg-gray-400 rounded"
        @click="productStore.closeModal">
        Cancel
      </button>

      <button class="px-4 py-2 bg-green-600 text-white rounded"
        @click="productStore.saveProduct">
        Save
      </button>
    </div>

  </div>
</div>


    </div>
  </DefaultLayout>
</template>