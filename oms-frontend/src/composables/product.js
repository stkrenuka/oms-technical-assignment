// oms-frontend/src/composables/product.js
import { ref } from 'vue'
import api from '@/api/axios'
import { useNotificationStore } from '@/stores/notification'
export default function useProducts() {
  const products = ref([])
  const loading = ref(false)
  const loadProducts = async () => {
    loading.value = true
    try {
      const { data } = await api.get('/admin/products')
      products.value = data.data
    } finally {
      loading.value = false
    }
  }
  const saveProduct = async (form, isEdit) => {
    if (isEdit) {
      await api.put(`/admin/products/${form.id}`, form)
    } else {
      await api.post('/admin/products', form)
    }
    await loadProducts()
  }
  const deleteProduct = async (id) => {
    const notification = useNotificationStore();
    try {
      await api.delete(`/admin/products/${id}`)
      products.value = products.value.filter(p => p.id !== id);
      notification.notify('Product Deleted successfully', 'success');
    }
    catch (e) {
      notification.notify('Something went wrong', 'error');
    }
  }
  const toggleStatus = async (product) => {
    const status = product.status === 'active' ? 'inactive' : 'active'
    await api.patch(`/admin/products/${product.id}/status`, { status })
    product.status = status
  }
  return {
    products,
    loading,
    loadProducts,
    saveProduct,
    deleteProduct,
    toggleStatus,
  }
}
