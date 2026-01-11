import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'
import { useNotificationStore } from '@/stores/notification'
export const useOrderStore = defineStore('order', () => {
  /* --------------------
     STATE
  -------------------- */
  const notification = useNotificationStore();
  const uploadId = ref(null);
  const uploadedFiles = ref([])
  const orders = ref([])
  const errors = ref({})
  const ordersStatus = ref([])
  const selectedCustomer = ref(null)
  const statusHistory = ref([])
  const uploading = ref(false)
  const uploadProgress = ref(0)
  const showNotesModal = ref(false)
  const formNotes = ref({
    order_id: null,
    notes: '',
    status_id: null
  })
  const statusForm = ref({
    order_id: null,
    current_status_id: null,
    next_status_id: null,
    note: '',
  })
  const search = ref('')
  const page = ref(1);
  const perPage = 5;
  const stats = ref({
    total_orders: 0,
    total_revenue: 0,
    total_customers: 0
  })
  const selectedOrder = ref(null)
  /* --------------------
     COMPUTED
  -------------------- */
  const filteredOrders = computed(() => {
    const term = search.value.toLowerCase()
    return orders.value.filter(o => {
      const customer = (o.customer?.name || '').toLowerCase()
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
  const getOrders = async (search = '') => {
    const { data } = await api.get('/orders', {
      params: { search }
    })
    orders.value = data.data.map(order => ({
      id: order.id,
      customer: order.customer?.name || '',
      customer_id: order.customer_id,
      status: order.status?.name || '',
      status_id: order.status_id,
      total: Number(order.total),
      created_at: order.created_at,
      items: order.items.map(item => ({
        product_id: item.product_id,
        name: item.product?.name || '',
        qty: item.quantity,
        price: Number(item.price),
      })),
      // optional: keep notes for search
      status_histories: order.status_histories || [],
    }))
  }
  const setOrderStatuses = (statuses) => {
    ordersStatus.value = statuses
  }
  const addOrder = (order) => {
    orders.value.unshift({
      ...order,
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
  const cancelOrder = async (orderId) => {
    const confirmed = window.confirm(
      'Are you sure you want to cancel this order? This action cannot be undone.'
    )
    if (!confirmed) return
    try {
      const { data } = await api.post(`/orders/${orderId}/cancel`)
      getAllOrders();
      notification.notify(
        data?.message || 'You are not allowed to cancel this order.',
        'success'
      )
    } catch (error) {
      console.error('Cancel order failed:', error)
      if (error.response?.status === 403) {
        notification.notify(
          error.response.data?.message || 'You are not allowed to cancel this order.',
          'error'
        )
      } else {
        notification.notify(
          'Something went wrong. Please try again.',
          'error'
        )
      }
    }
  }
  const deleteOrder = async (orderId) => {
    const confirmed = window.confirm(
      'Are you sure you want to delete this order? This action can be reversed by admin.'
    )
    if (!confirmed) return
    try {
      await api.delete(`/orders/${orderId}`)
      notification.notify('Order deleted successfully', 'success')
      getAllOrders();
    } catch (error) {
      console.error('Delete order failed:', error)
      if (error.response?.status === 403) {
        notification.notify(
          error.response.data?.message || 'You are not allowed to delete this order.',
          'error'
        )
      } else {
        notification.notify('Something went wrong.', 'error')
      }
    }
  }
  const getAllOrders = async () => {
    try {
      const { data } = await api.get('/orders')
      orders.value = data.data.map(order => ({
        id: order.id,
        customer: order.customer?.name ?? '',
        customer_id: order.customer_id,
        status: order.status?.name ?? '',
        status_id: order.status?.id ?? '',
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
  async function fetchOrderFiles(orderId) {
    const res = await api.get(`/orders/${orderId}/files`)
    uploadedFiles.value = res.data
  }
  return {
    // state
    orders,
    errors,
    search,
    page,
    selectedCustomer,
    ordersStatus,
    showNotesModal,
    formNotes,
    statusHistory,
    uploading,
    uploadProgress,
    // computed
    filteredOrders,
    paginatedOrders,
    stats,
    selectedOrder,
    statusForm,
    uploadId,
    uploadedFiles,
    // actions
    addOrder,
    getOrders,
    updateOrder,
    cancelOrder,
    getAllOrders,
    setOrderStatuses,
    getStats,
    deleteOrder,
    fetchOrderFiles
  }
})
