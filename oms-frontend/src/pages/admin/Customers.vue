<script setup>
import { ref } from 'vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import DeleteButton from '@/components/common/DeleteButton.vue'
import useCommonWork from '@/composables/common'
const {
  customers,
  loading,
  loadCustomers,
  deleteCustomer,
  updateCustomer,
  createCustomer
} = useCommonWork()
const showEditModal = ref(false)
const showAddModal = ref(false)
const editForm = ref({
  id: null,
  name: '',
  email: '',
  role: ''
})
const openAdd = () => {
  editForm.value = {
    id: null,
    name: '',
    email: '',
    role: ''
  }
  errors.value = {}
  showAddModal.value = true
}
const errors = ref({})
const submitAdd = async () => {
  if (!validateForm()) return
  try {
    await createCustomer({
      name: editForm.value.name,
      email: editForm.value.email,
      role: editForm.value.role
    })
    showAddModal.value = false
  } catch (err) {
    errors.value = err
  }
}
const openEdit = (customer) => {
  editForm.value = { ...customer }
  errors.value = {}
  showEditModal.value = true
}
const submitEdit = async () => {
  if (!validateForm()) return
  try {
    await updateCustomer(editForm.value.id, {
      name: editForm.value.name,
      email: editForm.value.email,
      role: editForm.value.role
    })
    showEditModal.value = false
  } catch (error) {
    // Laravel validation (422)
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    }
  }
}
const validateForm = () => {
  errors.value = {}
  if (!editForm.value.name) {
    errors.value.name = 'Name is required'
  }
  if (!editForm.value.email) {
    errors.value.email = 'Email is required'
  } else if (!/^\S+@\S+\.\S+$/.test(editForm.value.email)) {
    errors.value.email = 'Enter a valid email address'
  }
  if (!editForm.value.role) {
    errors.value.role = 'Role is required'
  }
  return Object.keys(errors.value).length === 0
}
loadCustomers()
</script>
<template>
  <DefaultLayout>
    <div class="bg-white rounded shadow p-6">
      <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold mb-4">Customers</h2>
        <button class="px-4 py-2 bg-green-600 text-white rounded" @click="openAdd">
          + Add Customer
        </button>
      </div>
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
            <td class="p-2 border space-x-2">
              <button class="px-3 py-1 text-sm bg-blue-500 text-white rounded" @click="openEdit(customer)">
                Edit
              </button>
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
    <!-- ADD MODAL -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white p-6 rounded w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Add Customer</h3>
        <input v-model="editForm.name" type="text" placeholder="Name" class="w-full border p-2 rounded"
          :class="errors.name ? 'border-red-500' : ''" />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">
          {{ errors.name }}
        </p>
        <div class="mt-4">
          <input v-model="editForm.email" type="email" placeholder="Email" class="w-full border p-2 rounded"
            :class="errors.email ? 'border-red-500' : ''" />
          <p v-if="errors.email" class="text-red-500 text-sm mt-1">
            {{ errors.email }}
          </p>
        </div>
        <div class="mt-4">
          <select v-model="editForm.role" class="w-full border p-2 rounded"
            :class="errors.role ? 'border-red-500' : ''">
            <option value="">Select role</option>
            <option value="customer">Customer</option>
            <option value="admin">Admin</option>
          </select>
          <p v-if="errors.role" class="text-red-500 text-sm mt-1">
            {{ errors.role }}
          </p>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <button class="px-4 py-2 border rounded" @click="showAddModal = false">
            Cancel
          </button>
          <button class="px-4 py-2 bg-green-600 text-white rounded" @click="submitAdd">
            Add
          </button>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>