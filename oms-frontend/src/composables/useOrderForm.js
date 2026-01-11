import { ref, computed, watch } from 'vue'
import api from '@/api/axios'
import { useOrderStore } from '@/stores/order'
import { useNotificationStore } from '@/stores/notification'
const ordersStore = useOrderStore()
export default function useOrderForm() {
  const showModal = ref(false);
  const baseApi = import.meta.env.VITE_API_BASE_URL;
  const editing = ref(false)
  const CHUNK_SIZE = 5 * 1024 * 1024 // 5MB
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
      ordersStore.errors = {}
      // Set state first
      ordersStore.formNotes.order_id = orderId
      ordersStore.formNotes.status_id = status_id
      ordersStore.showNotesModal = true
      // Fetch status history
      const { data } = await api.get(
        `/orders/${orderId}/status-history`
      )
      await ordersStore.fetchOrderFiles(
        ordersStore.formNotes.order_id
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
      ordersStore.showNotesModal = false
      notification.notify('Order Updated successfully', 'success');
      ordersStore.formNotes.notes = ''
      ordersStore.formNotes.order_id = null
    } catch (error) {
      console.error('Failed to save order note:', error)
      notification.notify('Something went wrong', 'error');
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
  async function uploadMultipleFiles(event) {
    const files = Array.from(event.target.files);
    if (!files.length) return
    ordersStore.uploading = true
    ordersStore.uploadProgress = 0
    for (const file of files) {
      await uploadSingleFile(file)
    }
    ordersStore.uploading = false
    await ordersStore.fetchOrderFiles(
      ordersStore.formNotes.order_id
    )
  }
  async function uploadSingleFile(file) {
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE)
    let uploadedChunks = 0
    const payload = {
      order_id: ordersStore.formNotes.order_id,
      file_name: file.name,
      total_chunks: totalChunks,
    }
    const initRes = await api.post('/uploads/init', payload)
    const uploadId = initRes.data.upload_id
    let start = 0
    let index = 0
    while (start < file.size) {
      const chunk = file.slice(start, start + CHUNK_SIZE)
      const form = new FormData()
      form.append('upload_id', String(uploadId))
      form.append('chunk_index', index)
      form.append('chunk', chunk)
      await api.post('/uploads/chunk', form)
      uploadedChunks++
      ordersStore.uploadProgress = Math.round(
        (uploadedChunks / totalChunks) * 100
      )
      index++
      start += CHUNK_SIZE
    }
    await api.post(`/uploads/${uploadId}/complete`)
    ordersStore.uploading = false
  }
  const downloadFile = async (file) => {
    const response = await api.get(
      `${baseApi}/orders/files/${file.id}/download`,
      { responseType: 'blob' }
    )
    // Get filename from headers
    const disposition = response.headers['content-disposition']
    let filename = file.file_name
    if (disposition && disposition.includes('filename=')) {
      filename = disposition.split('filename=')[1].replace(/"/g, '')
    }
    // Create blob with correct mime type
    const blob = new Blob([response.data], {
      type: response.headers['content-type'],
    })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    // Cleanup
    link.remove()
    window.URL.revokeObjectURL(url)
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
    uploadMultipleFiles,
    downloadFile
  }
}
