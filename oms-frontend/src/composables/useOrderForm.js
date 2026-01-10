import { ref, computed } from 'vue'
import api from '@/api/axios'
import { useOrderStore } from '@/stores/order'
const ordersStore = useOrderStore()
export default function useOrderForm() {
  const showModal = ref(false)
  const editing = ref(false)

  const form = ref({
    id: null,
    customer_id: null,
    status: 2,
    items: [],
  })
const getOrderStatuses = async () => {
  const res = await api.get('/orders/statuses')
  return res.data
}


  const calculatedTotal = computed(() =>
    form.value.items.reduce((sum, i) => sum + i.qty * i.price, 0)
  )
  const getAllOrders = async () => {
    try {
      const { data } = await api.get('/orders')
      ordersStore.orders.value = data.data.map(order => ({
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
      return data.data
    } catch (error) {
      console.error(error)
    }
  }
  const openCreateModal = () => {
    editing.value = false
    form.value = {
      id: null,
      customer_id: null,
      status: 2,
      items: [],
    }
    showModal.value = true
  }

  const openEditModal = (order) => {
    editing.value = true
    form.value = JSON.parse(JSON.stringify(order))
    showModal.value = true
  }

  const closeModal = () => {
    showModal.value = false
  }

  const addItem = () => {
    form.value.items.push({
      product_id: null,
      name: '',
      qty: 1,
      price: 0,
    })
  }

  const removeItem = (index) => {
    form.value.items.splice(index, 1)
  }

  return {
    showModal,
    editing,
    form,
    calculatedTotal,
    openCreateModal,
    openEditModal,
    closeModal,
    addItem,
    removeItem,
    getAllOrders,
    getOrderStatuses
  }
}
