<script setup>
import { useOrderStore } from '@/stores/order'
import useOrderForm from '@/composables/useOrderForm'
import OrderModal from '@/components/common/orders/OrderModal.vue'
import OrderNotes from '@/components/common/orders/OrderNotes.vue'
import api from '@/api/axios'
import { useNotificationStore } from '@/stores/notification'
import { onMounted,computed,watch,ref  } from 'vue'

const orderStore = useOrderStore()
const notificationStore = useNotificationStore()
const orderForm = useOrderForm()
const props = defineProps({
  userRole: String,
  authUser: Object
})
const isAdmin = computed(() =>  props.userRole === 'admin')
const authUser = props.authUser
const search = ref('')
let debounceTimer = null
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

watch(search, () => {
  clearTimeout(debounceTimer)

  debounceTimer = setTimeout(() => {
    orderStore.getOrders(search.value)
  }, 400)
})
const editOrder = (order) => {
  orderForm.openEditModal(order)
}
const saveOrder = async () => {
  orderStore.errors = {}

  const isEdit = orderForm.editing.value;

  const payload = {
    customer_id: orderStore.selectedCustomer
      ? orderStore.selectedCustomer
      : authUser.id,
    status: orderForm.form.value.status,
    total: orderForm.calculatedTotal.value,
    items: orderForm.form.value.items.map(item => ({
      product_id: item.product_id,
      qty: item.qty,
      price: item.price,
    })),
  }

  try {
    if (isEdit) {
      // ✅ STATUS CHANGE (EDIT MODE)
      await api.post(
        `/orders/${orderStore.statusForm.order_id}/change-status`,
        {
          status_id: orderStore.statusForm.next_status_id,
          note:"Order Status Changed"
        }
      )
    } else {
      // ✅ CREATE
      await api.post('/orders', payload)
    }

    await orderStore.getAllOrders()
    orderForm.closeModal()

    notificationStore.notify(
      isEdit ? 'Order updated successfully' : 'Order saved successfully',
      'success'
    )

  } catch (error) {
    if (error.response?.status === 422) {
      orderStore.errors = error.response.data.errors
    } else {
      notificationStore.notify(
        error?.response?.data?.message ||
        'Something went wrong',
        'error'
      )
    }
  }
}


const cancelOrder = (id) => {
  orderStore.cancelOrder(id)
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
      <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="orderForm.openCreateModal">
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
        <tr v-for="order in orderStore.paginatedOrders" :key="order.id" class="text-center">
          <td class="p-2 border">#{{ order.id }}</td>
          <td class="p-2 border">{{ order.customer }}</td>
          <td class="p-2 border">₹{{ order.total }}</td>
          <td class="p-2 border">
            {{ order.items?.length || 0 }} Item(s)
          </td>
          <td class="p-2 border">
            <!-- Admin: clickable -->
            <button v-if="isAdmin" class="px-2 py-1 text-sm rounded cursor-pointer" :class="statusClass(order.status)"
              @click="editOrder(order)">
              {{ order.status }}
            </button>

            <!-- Customer: read-only -->
            <span v-else class="px-2 py-1 text-sm rounded" :class="statusClass(order.status)">
              {{ order.status }}
            </span>
          </td>


          <td class="p-2 border space-x-2">
            <!-- <button class="px-3 py-1 bg-blue-600 text-white rounded" @click="editOrder(order)">
              Edit
            </button> -->
            <button class="px-3 py-1 rounded text-white" :class="order.status_id > 3
              ? 'bg-gray-400 cursor-not-allowed'
              : 'bg-red-600 hover:bg-red-700'" :disabled="order.status_id > 3" @click="cancelOrder(order.id)">
              Cancel
            </button>
            <button v-if="isAdmin" class="px-3 py-1 rounded text-white" :class="order.status_id > 2
              ? 'bg-gray-400 cursor-not-allowed'
              : 'bg-red-600 hover:bg-red-700'" :disabled="order.status_id > 2" @click="deleteOrder(order.id)">
              Delete
            </button>
            <!-- Notes -->
            <button class="px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-800"
              @click="orderForm.openNotes(order.id, order.status_id)">
              Notes
            </button>
            <button class="px-3 py-1 bg-green-600 text-white rounded" @click="orderForm.downloadInvoice(order.id)">
              Invoice
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
      <button class="px-3 py-1 border rounded" :disabled="orderStore.page === 1" @click="orderStore.page--">
        Prev
      </button>

      <span class="px-3 py-1">
        {{ orderStore.page }}
      </span>

      <button class="px-3 py-1 border rounded" :disabled="orderStore.page * 5 >= orderStore.filteredOrders.length"
        @click="orderStore.page++">
        Next
      </button>
    </div>

    <OrderModal v-if="orderForm.showModal.value" :form="orderForm.form.value" :editing="orderForm.editing.value"
      :total="orderForm.calculatedTotal.value" :userRole="userRole" :authUser="authUser" @close="orderForm.closeModal"
      @save="saveOrder" @add-item="orderForm.addItem" @remove-item="orderForm.removeItem"
      @product-selected="onProductSelected" />
    <OrderNotes />
  </div>
</template>