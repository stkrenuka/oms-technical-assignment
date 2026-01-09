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

    return {
        customers,
        loading,
        loadCustomers,
        deleteCustomer,
    }
}