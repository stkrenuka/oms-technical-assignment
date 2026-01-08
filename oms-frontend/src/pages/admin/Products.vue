    <script setup>
    import DefaultLayout from '@/layouts/DefaultLayout.vue'

</script>
<template>
    <DefaultLayout>
       <div class="bg-white rounded shadow p-6">
  <div class="flex justify-between mb-4">
    <h2 class="text-xl font-semibold">Products</h2>
    <button class="px-4 py-2 bg-green-600 text-white rounded">
      + Add Product
    </button>
  </div>

  <table class="w-full border">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 border">ID</th>
        <th class="p-2 border">Product</th>
        <th class="p-2 border">Price</th>
        <th class="p-2 border">Stock</th>
        <th class="p-2 border">Action</th>
      </tr>
    </thead>
    <tbody>
      <tr class="text-center">
        <td class="p-2 border">101</td>
        <td class="p-2 border">iPhone 15</td>
        <td class="p-2 border">$1200</td>
        <td class="p-2 border">15</td>
        <td class="p-2 border space-x-2">
          <button class="px-3 py-1 bg-blue-600 text-white rounded">Edit</button>
          <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>


    </DefaultLayout>
</template>
