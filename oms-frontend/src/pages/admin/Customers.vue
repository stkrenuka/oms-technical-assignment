    <script setup>
    import DefaultLayout from '@/layouts/DefaultLayout.vue'
    import DeleteButton from '@/components/common/DeleteButton.vue';


    import useCommonWork from '@/composables/common';

    const { customers, loading, loadCustomers,deleteCustomer } = useCommonWork()


    loadCustomers();



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
          <tr v-for="customer in customers" :key="customer.id" class="text-center">
            <td class="p-2 border">{{ customer.id }}</td>
            <td class="p-2 border">{{ customer.name }}</td>
            <td class="p-2 border">{{ customer.email }}</td>
            <td class="p-2 border capitalize">{{ customer.role }}</td>
            <td class="p-2 border">
              <DeleteButton @confirm="deleteCustomer(customer.id)" />
            </td>
          </tr>

          <tr v-if="!customers.length && !loading">
            <td colspan="5" class="p-4 text-center text-gray-500">
              No customers found
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </DefaultLayout>
</template>
<script setup>
</script>