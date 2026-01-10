<script setup>
import { useOrderStore } from '@/stores/order'
import useOrderForm from '@/composables/useOrderForm'
import OrderModal from '@/components/common/orders/OrderModal.vue'
import api from '@/api/axios'
import {useNotificationStore} from '@/stores/notification'
import { onMounted } from 'vue'
const orderStore = useOrderStore()
const notificationStore = useNotificationStore()
const orderForm = useOrderForm()
const{ search } = orderStore
const props = defineProps({
  userRole: String,
  authUser:Object
})
const userRole = props.userRole
const authUser = props.authUser
/* ---------------- Helpers ---------------- */
const statusClass = (status) => ({
  Draft: 'bg-yellow-100 text-yellow-700',
  Confirmed: 'bg-blue-100 text-blue-700',
  Processing: 'bg-purple-100 text-purple-700',
  Dispatched: 'bg-indigo-100 text-indigo-700',
  Delivered: 'bg-green-100 text-green-700',
  Cancelled: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700')

/* ---------------- Actions ---------------- */
const editOrder = (order) => {
  orderForm.openEditModal(order)
}
const saveOrder = async () => {
  // 1️⃣ Frontend validation
  // if (!orderForm.form.value.items.length) {
  //   orderStore.errors = {
  //     items: ['Please add at least one product to the order.'],
  //   }
  //   return
  // }

  // 2️⃣ Reset previous errors
  orderStore.errors = {}

  const payload = {
    customer_id: orderStore.selectedCustomer? orderStore.selectedCustomer:authUser.id,
    status: orderForm.form.value.status,
    total: orderForm.calculatedTotal.value,
    items: orderForm.form.value.items.map(item => ({
      product_id: item.product_id,
      qty: item.qty,
      price: item.price,
    })),
  }

  try {
    // 3️⃣ API call
    const { data } = await api.post('/orders', payload)

    // 4️⃣ Update UI
  orderStore.addOrder({
  ...data.data,
  customer: data.data.customer?.name ?? '—',
  items: data.data.items ?? orderForm.form.value.items, // ✅ ADD THIS
  total: data.data.total,
})

    // 5️⃣ Close modal
      orderStore.getAllOrders()

    orderForm.closeModal()

    // 6️⃣ Success notification
    notificationStore.notify(
      'Order saved successfully',
      'success'
    )

  } catch (error) {
    console.error('Error saving order:', error);
    // ❌ ONLY runs on error
    if (error.response?.status === 422) {
      orderStore.errors = error.response.data.errors
    } else {
      notificationStore.notify(
        error?.response?.data?.message ||
          'Something went wrong while saving the order',
        'error'
      )
    }
  }
}

const deleteOrder = (id) => {
  orderStore.deleteOrder(id)
}

const onProductSelected = ({ product, item }) => {
  item.product_id = product.id
  item.name = product.name
  item.price = product.price
}

onMounted(() => {
  orderStore.getAllOrders()
})
</script>

<template>
    <div class="bg-white rounded shadow p-6">

      <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Orders</h2>
      <button class="px-4 py-2 bg-blue-600 text-white rounded"  @click="orderForm.openCreateModal">
        + Create Order
      </button>
    </div>
    <!-- Search -->
    <div class="mb-4">
      <input v-model="search" type="text" placeholder="Search order or customer..."
        class="border px-3 py-2 rounded w-64" />
    </div>
      <!-- Orders Table (YOUR TABLE, UNCHANGED) -->
    <table class="w-full border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 border">Order ID</th>
          <th class="p-2 border">Customer</th>
          <th class="p-2 border">Total</th>
          <th class="p-2 border">Items</th>
          <th class="p-2 border">Status</th>
          <th class="p-2 border">Action</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="order in orderStore.paginatedOrders"
          :key="order.id"
          class="text-center"
        >
          <td class="p-2 border">#{{ order.id }}</td>
          <td class="p-2 border">{{ order.customer }}</td>
          <td class="p-2 border">₹{{ order.total }}</td>
<td class="p-2 border">
  {{ order.items?.length || 0 }} Item(s)
</td>
          <td class="p-2 border">
            <span
              class="px-2 py-1 text-sm rounded"
              :class="statusClass(order.status)"
            >
              {{ order.status }}
            </span>
          </td>

          <td class="p-2 border space-x-2">
            <button
              class="px-3 py-1 bg-blue-600 text-white rounded"
              @click="editOrder(order)"
            >
              Edit
            </button>
            <button
              class="px-3 py-1 bg-red-600 text-white rounded"
              @click="deleteOrder(order.id)"
            >
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="orderStore.paginatedOrders.length === 0">
          <td colspan="6" class="p-4 text-center text-gray-500">
            No orders found
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="flex justify-end mt-4 space-x-2">
      <button
        class="px-3 py-1 border rounded"
        :disabled="orderStore.page === 1"
        @click="orderStore.page--"
      >
        Prev
      </button>

      <span class="px-3 py-1">
        {{ orderStore.page }}
      </span>

      <button
        class="px-3 py-1 border rounded"
        :disabled="orderStore.page * 5 >= orderStore.filteredOrders.length"
        @click="orderStore.page++"
      >
        Next
      </button>
    </div>

  <OrderModal
    v-if="orderForm.showModal.value"
    :form="orderForm.form.value"
    :editing="orderForm.editing.value"
    :total="orderForm.calculatedTotal.value"
    :userRole="userRole"
    :authUser="authUser"
    @close="orderForm.closeModal"
    @save="saveOrder"
    @add-item="orderForm.addItem"
    @remove-item="orderForm.removeItem"
    @product-selected="onProductSelected"
  />
  </div>
</template>
