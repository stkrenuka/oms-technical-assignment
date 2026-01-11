import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'
export const useCustomerStore = defineStore('customer', () => {
  const loading = ref(false)
  const searchForOrder = async (query) => {
    if (!query || query.length < 2) return []
    loading.value = true
    try {
      const { data } = await api.get('admin/customers/search', {
        params: {
          search: query,
          per_page: 10,
        },
      })
      return data.data ?? []
    } catch (error) {
      console.error('Customer search failed:', error)
      // useNotificationStore().notify('Failed to load customers', 'error')
      return []
    } finally {
      loading.value = false
    }
  }
  // ⬅⬅⬅ THIS MUST BE RETURNED
  return {
    loading,
    searchForOrder,
  }
})
