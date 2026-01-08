    <script setup>
    import DefaultLayout from '@/layouts/DefaultLayout.vue'

</script>
<template>
    <DefaultLayout>
      <div class="bg-white rounded shadow p-6">
  <h2 class="text-xl font-semibold mb-4">Customers</h2>

  <table class="w-full border">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 border">ID</th>
        <th class="p-2 border">Name</th>
        <th class="p-2 border">Email</th>
        <th class="p-2 border">Role</th>
        <th class="p-2 border">Action</th>
      </tr>
    </thead>
    <tbody>
      <tr class="text-center">
        <td class="p-2 border">1</td>
        <td class="p-2 border">Alice</td>
        <td class="p-2 border">alice@mail.com</td>
        <td class="p-2 border">Customer</td>
        <td class="p-2 border">
          <button class="px-3 py-1 bg-red-600 text-white rounded">
            Delete
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

    </DefaultLayout>
</template>
