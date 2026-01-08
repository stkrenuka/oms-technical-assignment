// src/composables/auth.js
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth.js'
import api from '@/api/axios'

export default function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()

  const processing = ref(false)
  const errors = ref({})

  // 🔐 LOGIN FORM
  const loginForm = reactive({
    email: '',
    password: '',
    remember: false
  })

  // 📝 REGISTER FORM (matches your template)
  const registerForm = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  // ✅ REGISTER FUNCTION
  const submitSignup = async () => {
    if (processing.value) return

    processing.value = true
    errors.value = {}

    try {
      await api.get('/sanctum/csrf-cookie')
      await api.post('/register', registerForm)

      // fetch logged-in user
      await authStore.getUser()

      // redirect after success
      router.push({ name: 'login' }) // or dashboard

    } catch (error) {
      if (error.response?.data?.errors) {
        errors.value = error.response.data.errors
      }
    } finally {
      processing.value = false
    }
  }

  // ✅ LOGIN FUNCTION (kept simple)
  const submitLogin = async () => {
    if (processing.value) return

    processing.value = true
    errors.value = {}

    try {
      await api.get('/sanctum/csrf-cookie')
      await api.post('/login', loginForm)

      await authStore.getUser()
      if (authStore.user.role === 'admin') {
        router.push({ name: 'admin.dashboard' })
      } else {
        router.push({ name: 'customer.orders' })
      }

    } catch (error) {
      if (error.response?.status === 401) {
        errors.value = {
          general: [error.response.data.message]
        }
        console.log('error,error', errors.value)

      }

      if (error.response?.status === 422) {
        errors.value = error.response.data.errors
      }
    } finally {
      processing.value = false
    }
  }


  const logout = async () => {
    await axios.post('/logout')
    authStore.logout()
    router.push({ name: 'login' })
  }

  return {
    loginForm,
    registerForm,
    submitLogin,
    submitSignup,
    logout,
    errors,
    processing
  }
}
