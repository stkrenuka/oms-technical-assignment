import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'
import { useNotificationStore } from '@/stores/notification'
export const useProductStore = defineStore('product', () => {
  /* =====================
     State
  ===================== */
  const products = ref([])
  const loading = ref(false)
  const errors = ref({})
  const showModal = ref(false)
  const isEdit = ref(false)
  const search = ref('')
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    next_page: null,
    prev_page: null,
  })
  const form = ref({
    id: null,
    name: '',
    description: '',
    price: '',
    stock: '',
    status: 'active',
    image: null,
  })
  /* =====================
     Actions
  ===================== */
  const loadProducts = async (page = 1) => {
    loading.value = true
    try {
      const { data } = await api.get('/admin/products', {
        params: {
          page,
          search: search.value,
        },
      })
      products.value = data.data
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        next_page: data.next_page_url,
        prev_page: data.prev_page_url,
      }
    } finally {
      loading.value = false
    }
  }
  const nextPage = () => {
    if (pagination.value.next_page) {
      loadProducts(pagination.value.current_page + 1)
    }
  }
  const prevPage = () => {
    if (pagination.value.prev_page) {
      loadProducts(pagination.value.current_page - 1)
    }
  }
  const openCreate = () => {
    isEdit.value = false
    errors.value = {}
    form.value = {
      id: null,
      name: '',
      description: '',
      price: '',
      stock: '',
      status: 'active',
      image: null,
    }
    showModal.value = true
  }
  const editProduct = (product) => {
    isEdit.value = true
    errors.value = {}
    form.value = { ...product, image: null }
    showModal.value = true
  }
  const closeModal = () => {
    showModal.value = false
  }
  const saveProduct = async () => {
    errors.value = {}
    const notification = useNotificationStore()
    try {
      const formData = new FormData()
      Object.entries(form.value).forEach(([key, value]) => {
        if (value !== null) {
          formData.append(key, value)
        }
      })
      if (isEdit.value) {
        formData.append('_method', 'PUT')
        await api.post(`/admin/products/${form.value.id}`, formData)
      } else {
        await api.post('/admin/products', formData)
      }
      await loadProducts(pagination.value.current_page)
      notification.notify(
        isEdit.value
          ? 'Product updated successfully'
          : 'Product added successfully',
        'success'
      )
      showModal.value = false
    } catch (error) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      } else {
        notification.notify(
          error?.response?.data?.message ||
          'Something went wrong while saving the product',
          'error'
        )
      }
    }
  }
  const deleteProduct = async (id) => {
    const notification = useNotificationStore();
    try {
      if (!confirm('Delete this product?')) return
      await api.delete(`/admin/products/${id}`)
      await loadProducts(pagination.value.current_page)
      notification.notify('Product Deleted successfully', 'success');
    } catch (error) {
      notification.notify('Something went wrong', 'error');
    }
  }
  const toggleStatus = async (product) => {
    const status =
      product.status === 'active' ? 'inactive' : 'active'
    await api.patch(`/admin/products/${product.id}/status`, { status })
    product.status = status
  }
  const onImageChange = (e) => {
    form.value.image = e.target.files[0]
  }
  const searchForOrder = async (query) => {
    if (!query || query.length < 2) return []
    const { data } = await api.get('/products', {
      params: {
        search: query,
        per_page: 10, // IMPORTANT
      },
    })
    return data.data
  }
  return {
    // state
    products,
    loading,
    errors,
    showModal,
    isEdit,
    search,
    form,
    pagination,
    // actions
    loadProducts,
    nextPage,
    prevPage,
    openCreate,
    editProduct,
    closeModal,
    saveProduct,
    deleteProduct,
    toggleStatus,
    onImageChange,
    searchForOrder
  }
})
