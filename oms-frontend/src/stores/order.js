import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios' // adjust path if needed

export const useOrderStore = defineStore('order', () => {
  /* --------------------
     STATE
  -------------------- */
  const orders = ref([])
  const errors = ref({})
 const ordersStatus = ref([])
  const selectedCustomer = ref(null)

  const search = ref('')
  const page = ref(1);
  const perPage = 5;
 const stats = ref({
  total_orders: 0,
  total_revenue: 0,
})


  /* --------------------
     COMPUTED
  -------------------- */
  const filteredOrders = computed(() => {
    const term = search.value.toLowerCase()

    return orders.value.filter(o => {
      const customer = (o.customer || '').toLowerCase()
      const id = o.id?.toString() || ''

      return customer.includes(term) || id.includes(term)
    })
  })

  const paginatedOrders = computed(() => {
    const start = (page.value - 1) * perPage
    return filteredOrders.value.slice(start, start + perPage)
  })

  /* --------------------
     ACTIONS
  -------------------- */
   const setOrderStatuses = (statuses) => {
    ordersStatus.value = statuses
  }
  const addOrder = (order) => {
    orders.value.unshift({
      ...order,
      id: Date.now(),
    })
  }
  const getStats = async () => {
  const { data } = await api.get('/dashboard/stats')
  stats.value = data
}

  const updateOrder = (order) => {
    const index = orders.value.findIndex(o => o.id === order.id)
    if (index !== -1) {
      orders.value[index] = order
    }
  }

  const deleteOrder = (id) => {
    orders.value = orders.value.filter(o => o.id !== id)
  }

  const getAllOrders = async () => {
    try {
      const { data } = await api.get('/orders')

      orders.value = data.data.map(order => ({
        id: order.id,
        customer: order.customer?.name ?? '',
        customer_id: order.customer_id,
        status: order.status?.name ?? '',
        total: Number(order.total),
        created_at: order.created_at,
        items: order.items.map(item => ({
          product_id: item.product_id,
          name: item.product?.name ?? '',
          qty: item.quantity,
          price: Number(item.price),
        })),
      }))

      return orders.value
    } catch (error) {
      console.error(error)
      errors.value = error.response?.data ?? {}
      throw error
    }
  }

  return {
    // state
    orders,
    errors,
    search,
    page,
    selectedCustomer,
    ordersStatus,


    // computed
    filteredOrders,
    paginatedOrders,
    stats,
    // actions
    addOrder,
    updateOrder,
    deleteOrder,
    getAllOrders,
    setOrderStatuses,
    getStats
  }
})
