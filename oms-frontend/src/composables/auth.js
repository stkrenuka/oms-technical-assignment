// src/composables/auth.js
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth.js'
import api from '@/api/axios'
import { useNotificationStore } from '@/stores/notification'

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
    const notification = useNotificationStore();

  if (processing.value) return

  processing.value = true
  errors.value = {}

  try {
    const { data } = await api.post('/register', registerForm)

    // 🔐 Save token
    localStorage.setItem('token', data.token)

    // Set auth state
    authStore.setUser(data.user)

    // Redirect after register
    router.push({ name: 'customer.dashboard' })
      notification.notify(
      'Registration successful',
      'success'
    )

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    }
  } finally {
    processing.value = false
  }
}


  // ✅ LOGIN FUNCTION (kept simple)
const submitLogin = async () => {
  const notification = useNotificationStore();
  processing.value = true
  errors.value = {}

  try {
    const { data } = await api.post('/login', loginForm)

    // 🔐 Save token
    localStorage.setItem('token', data.token)

    authStore.setUser(data.user)

    router.push(
      data.user.role === 'admin'
        ? { name: 'admin.dashboard' }
        : { name: 'customer.dashboard' }
    )
      notification.notify(
      'Login successful',
      'success'
    )
  } catch (error) {
    errors.value = {
      general: ['Invalid credentials']
    }
  } finally {
    processing.value = false
  }
}



  const logout = async () => {
    await api.post('/logout')
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
