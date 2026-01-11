import { ref, computed, watch } from 'vue'
import api from '@/api/axios'
import { useOrderStore } from '@/stores/order'
import { useNotificationStore } from '@/stores/notification'

const ordersStore = useOrderStore()
export default function useOrderForm() {
  const showModal = ref(false);

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
    const orderData = JSON.parse(JSON.stringify(order))
    ordersStore.statusForm.current_status_id = orderData.status_id
    ordersStore.statusForm.next_status_id = orderData.status_id
    ordersStore.statusForm.order_id = orderData.id
    console.log('hhhjhj', ordersStore.statusForm)

    showModal.value = true
  }

  const closeModal = () => {
    showModal.value = false;
    editing.value = false
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
  const openNotes = async (orderId, status_id) => {
    try {
      ordersStore.errors={}
      // Set state first
      ordersStore.formNotes.order_id = orderId
      ordersStore.formNotes.status_id = status_id
      ordersStore.showNotesModal = true

      // Fetch status history
      const { data } = await api.get(
        `/orders/${orderId}/status-history`
      )


      ordersStore.statusHistory = data

    } catch (error) {
      console.error('Failed to load order status history:', error)

      // Rollback UI state if API fails
      ordersStore.showNotesModal = false
      ordersStore.formNotes.order_id = null
      ordersStore.formNotes.status_id = null


    }
  }

  const closeNotes = () => {
    ordersStore.showNotesModal = false
  }
  const saveNotes = async () => {
    if (ordersStore.formNotes.notes == '') {
     ordersStore.errors = {
  notes: 'Please add something'
}
      return
    }
    const notification = useNotificationStore();

    try {


      const payload = {
        order_id: ordersStore.formNotes.order_id,
        note: ordersStore.formNotes.notes,
        status_id: ordersStore.formNotes.status_id,
      }

      await api.post(
        `/orders/${ordersStore.formNotes.order_id}/change-status`,
        payload
      )

      // ✅ Close modal AFTER success
      ordersStore.showNotesModal = false
      notification.notify('Order Updated successfully', 'success');


      // ✅ Reset form safely
      ordersStore.formNotes.notes = ''
      ordersStore.formNotes.order_id = null

    } catch (error) {
      console.error('Failed to save order note:', error)
      notification.notify('Something went wrong', 'error');

      // Optional: show toast / alert
      // toast.error(error.response?.data?.message || 'Something went wrong')

    } finally {
      // Optional: stop loader
      // ordersStore.isSaving = false
    }
  }
  const downloadInvoice = async (orderId) => {
    const notification = useNotificationStore();

    try {

      const response = await api.get(
        `/orders/${orderId}/invoice`,
        { responseType: 'blob' }
      )

      const blob = new Blob([response.data], { type: 'application/pdf' })
      const link = document.createElement('a')

      link.href = window.URL.createObjectURL(blob)
      link.download = `invoice-${orderId}.pdf`
      link.click()

    } catch (error) {
      if (error.response?.status === 403) {
        notification.notify(
          'You are not allowed to download this invoice',
          'warning'
        )
      } else {
        notification.notify('Failed to download invoice', 'error')
      }
    }
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
    getOrderStatuses,
    openNotes,
    closeNotes,
    saveNotes,
    downloadInvoice,

  }
}
