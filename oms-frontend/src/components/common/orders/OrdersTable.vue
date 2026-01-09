<template>
  <div class="bg-white rounded shadow p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Orders</h2>
      <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="openCreateModal">
        + Create Order
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input v-model="search" type="text" placeholder="Search order or customer..."
        class="border px-3 py-2 rounded w-64" />
    </div>

    <!-- Orders Table -->
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
        <tr v-for="order in paginatedOrders" :key="order.id" class="text-center">
          <td class="p-2 border">#{{ order.id }}</td>
          <td class="p-2 border">{{ order.customer }}</td>
          <td class="p-2 border">₹{{ order.total }}</td>
          <td class="p-2 border">1 Item</td>
          <td class="p-2 border">
            <span class="px-2 py-1 text-sm rounded" :class="statusClass(order.status)">
              {{ order.status }}
            </span>
          </td>
          <td class="p-2 border space-x-2">
            <button class="px-3 py-1 bg-blue-600 text-white rounded" @click="editOrder(order)">
              Edit
            </button>
            <button class="px-3 py-1 bg-red-600 text-white rounded" @click="deleteOrder(order.id)">
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="paginatedOrders.length === 0">
          <td colspan="5" class="p-4 text-center text-gray-500">
            No orders found
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="flex justify-end mt-4 space-x-2">
      <button class="px-3 py-1 border rounded" :disabled="page === 1" @click="page--">
        Prev
      </button>
      <span class="px-3 py-1">{{ page }}</span>
      <button class="px-3 py-1 border rounded" :disabled="endIndex >= filteredOrders.length" @click="page++">
        Next
      </button>
    </div>

    <!-- Create / Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white p-6 rounded w-[700px]">
        <h3 class="text-lg font-semibold mb-4">
          {{ editing ? 'Edit Order' : 'Create Order' }}
        </h3>
        <!-- Customer Search (Admin only) -->
        <div class="relative" v-if="userRole === 'admin'">
          <label class="block text-sm font-medium mb-1">Customer</label>

          <input v-model="customerSearch" @input="searchCustomers" type="text"
            placeholder="Search customer by name or email" class="border px-3 py-2 rounded w-full" />

          <!-- Dropdown -->
          <ul v-if="customerResults.length"
            class="absolute z-10 bg-white border w-full mt-1 max-h-48 overflow-auto rounded">
            <li v-for="customer in customerResults" :key="customer.id" @click="selectCustomer(customer)"
              class="px-3 py-2 cursor-pointer hover:bg-gray-100">
              <div class="font-medium">{{ customer.name }}</div>
              <div class="text-xs text-gray-500">{{ customer.email }}</div>
            </li>
          </ul>

          <!-- Selected Customer -->
          <div v-if="selectedCustomer" class="mt-2 p-2 border rounded bg-gray-50 flex justify-between items-center">
            <div>
              <div class="font-medium">{{ selectedCustomer.name }}</div>
              <div class="text-xs text-gray-500">{{ selectedCustomer.email }}</div>
            </div>
            <button class="text-red-600 text-sm" @click="clearCustomer">
              Change
            </button>
          </div>
        </div>


        <!-- Customer (Customer view) -->
        <div v-else class="mb-4">
          <label class="block text-sm font-medium mb-1">Customer</label>
          <input type="text" class="border px-3 py-2 rounded w-full bg-gray-100" :value="authUser.name" readonly />
        </div>

<br>
        <!-- Status -->
        <select v-model="form.status" class="border px-3 py-2 rounded w-full mb-4">
          <option>Draft</option>
          <option>Confirmed</option>
          <option>Processing</option>
          <option>Dispatched</option>
          <option>Delivered</option>
          <option>Cancelled</option>
        </select>

        <!-- Order Items -->
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

            <tr v-for="(item, i) in form.items" :key="i">
              <!-- Product Select -->
              <ProductSearch />

              <!-- Quantity -->
              <td class="border p-2">
                <input v-model.number="item.qty" type="number" min="1" class="border px-2 py-1 rounded w-20" />
              </td>

              <!-- Price (Readonly) -->
              <td class="border p-2">
                <input type="number" class="border px-2 py-1 rounded w-24 bg-gray-100" :value="item.price" readonly />
              </td>

              <!-- Line Total -->
              <td class="border p-2 font-medium">
                ₹{{ item.qty * item.price }}
              </td>

              <!-- Remove -->
              <td class="border p-2">
                <button class="text-red-600" @click="removeItem(i)">✕</button>
              </td>
            </tr>
          </tbody>
        </table>

        <button class="text-blue-600 mb-4" @click="addItem">
          + Add Item
        </button>


        <!-- Total -->
        <div class="text-right font-semibold mb-4">
          Total: ₹{{ calculatedTotal }}
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-2">
          <button class="px-4 py-2 border rounded" @click="closeModal">
            Cancel
          </button>
          <button class="px-4 py-2 bg-green-600 text-white rounded" @click="saveOrder">
            Save
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ProductSearch from '@/components/common/orders/ProductSearch.vue';


/* ---------------- State ---------------- */
const orders = ref([
  {
    id: 1001,
    customer: 'John Doe',
    status: 'Completed',
    items: [{ name: 'Product A', qty: 2, price: 125 }],
    total: 250,
  },
])
const userRole = ref('admin')
const search = ref('')
const page = ref(1)
const perPage = 5

const showModal = ref(false)
const editing = ref(false)

const form = ref({
  customer_id: null,
  status: 'Draft',
  items: [],
})

/* ---------------- Computed ---------------- */
const filteredOrders = computed(() =>
  orders.value.filter(o =>
    o.customer.toLowerCase().includes(search.value.toLowerCase()) ||
    o.id.toString().includes(search.value)
  )
)

const startIndex = computed(() => (page.value - 1) * perPage)
const endIndex = computed(() => startIndex.value + perPage)

const paginatedOrders = computed(() =>
  filteredOrders.value.slice(startIndex.value, endIndex.value)
)

const calculatedTotal = computed(() =>
  form.value.items.reduce((sum, i) => sum + i.qty * i.price, 0)
)

/* ---------------- Methods ---------------- */
const openCreateModal = () => {
  editing.value = false
  form.value = { id: null, customer: '', status: 'Draft', items: [] }
  showModal.value = true
}

const editOrder = (order) => {
  editing.value = true
  form.value = JSON.parse(JSON.stringify(order))
  showModal.value = true
}

const saveOrder = () => {
  form.value.total = calculatedTotal.value

  if (editing.value) {
    const index = orders.value.findIndex(o => o.id === form.value.id)
    orders.value[index] = { ...form.value }
  } else {
    form.value.id = Date.now()
    orders.value.push({ ...form.value })
  }

  closeModal()
}

const deleteOrder = (id) => {
  orders.value = orders.value.filter(o => o.id !== id)
}

const closeModal = () => {
  showModal.value = false
}

const addItem = () => {
  form.value.items.push({
    product_id: null,
    search: '',
    results: [],
    qty: 1,
    price: 0,
  })
}


const removeItem = (i) => {
  form.value.items.splice(i, 1)
}
const onProductChange = (item) => {
  const product = products.value.find(p => p.id === item.product_id)

  if (product) {
    item.price = product.price
  } else {
    item.price = 0
  }
}

const searchProducts = async (item) => {
  if (item.search.length < 2) {
    item.results = []
    return
  }

  // API CALL (replace later)
  item.results = products.value
    .filter(p =>
      p.name.toLowerCase().includes(item.search.toLowerCase())
    )
    .slice(0, 10)
}

const selectProduct = (item, product) => {
  item.product_id = product.id
  item.search = product.name
  item.price = product.price
  item.results = []
}
const authUser = ref({
  id: 5,
  name: 'John Doe',
})

const customers = ref([
  { id: 1, name: 'John Doe' },
  { id: 2, name: 'Jane Smith' },
  { id: 3, name: 'Alice Johnson' },
])
const customerSearch = ref('')
const customerResults = ref([])
const selectedCustomer = ref(null)

const searchCustomers = async () => {
  const q = customerSearch.value?.toLowerCase() || ''

  if (q.length < 2) {
    customerResults.value = []
    return
  }

  customerResults.value = customers.value
    .filter(c => {
      const name = (c.name || '').toLowerCase()
      const email = (c.email || '').toLowerCase()

      return name.includes(q) || email.includes(q)
    })
    .slice(0, 8)
}

const selectCustomer = (customer) => {
  selectedCustomer.value = customer
  form.value.customer_id = customer.id
  customerSearch.value = customer.name
  customerResults.value = []
}

const clearCustomer = () => {
  selectedCustomer.value = null
  form.value.customer_id = null
  customerSearch.value = ''
}


const products = ref([
  { id: 1, name: 'Product A', price: 125 },
  { id: 2, name: 'Product B', price: 200 },
  { id: 3, name: 'Product C', price: 350 },
])
const statusClass = (status) => ({
  Draft: 'bg-yellow-100 text-yellow-700',
  Confirmed: 'bg-blue-100 text-blue-700',
  Processing: 'bg-purple-100 text-purple-700',
  Dispatched: 'bg-indigo-100 text-indigo-700',
  Delivered: 'bg-green-100 text-green-700',
  Cancelled: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700')
</script>