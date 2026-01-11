import { ref } from 'vue';
import api from '@/api/axios';
import { useNotificationStore } from '@/stores/notification'
export default function useCommonWork() {
    const customers = ref([])
    const loading = ref(false)
    const loadCustomers = async () => {
        loading.value = true
        try {
            const { data } = await api.get('/admin/customers')
            customers.value = data.data // paginated response
        } catch (error) {
            console.error('Failed to load customers', error)
        } finally {
            loading.value = false
        }
    }
    const deleteCustomer = async (id) => {
        const notification = useNotificationStore();
        if (!confirm('Are you sure you want to delete this customer?')) return
        try {
            await api.delete(`/admin/customers/${id}`)
            notification.notify('Customer deleted successfully', 'success');
            customers.value = customers.value.filter(
                customer => customer.id !== id
            )
        } catch (error) {
            console.error('Failed to delete customer', error)
            notification.notify('Failed to delete customer', 'error');
        }
    }
    const updateCustomer = async (id, payload) => {
        const notification = useNotificationStore()

        try {
            const { data } = await api.put(`admin/customers/${id}`, payload)

            notification.notify('Customer updated successfully', 'success')

            await loadCustomers()

            return data
        } catch (error) {
            // 🔐 Unauthorized / Forbidden
            if (error.response?.status === 403) {
                notification.notify('You are not authorized to update this customer', 'error')
            }

            // ❌ Validation error (Laravel 422)
            if (error.response?.status === 422) {
                return Promise.reject(error.response.data.errors)
            }

            // 💥 Server / Unknown error
            notification.notify('Something went wrong. Please try again.', 'error')

            throw error
        }


    }
   const createCustomer = async (payload) => {
            const notification = useNotificationStore()

            try {
                const { data } = await api.post('admin/customers', payload)

                notification.notify('Customer added successfully', 'success')

                await loadCustomers()

                return data
            } catch (error) {
                if (error.response?.status === 422) {
                    return Promise.reject(error.response.data.errors)
                }

                notification.notify('Failed to add customer', 'error')
                throw error
            }
        }
    return {
        customers,
        loading,
        loadCustomers,
        deleteCustomer,
        updateCustomer,
        createCustomer
    }
}