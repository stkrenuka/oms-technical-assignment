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
  const loginForm = reactive({
    email: '',
    password: '',
    remember: false
  })
  const registerForm = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })
  const submitSignup = async () => {
    if (!registerForm.name || !registerForm.email || !registerForm.password || !registerForm.password_confirmation) {
      errors.value = {
        general: ['All the fields are required']
      }
      return
    }
    const notification = useNotificationStore();
    if (processing.value) return
    processing.value = true
    errors.value = {}
    try {
      const { data } = await api.post('/register', registerForm)
      localStorage.setItem('token', data.token)
      authStore.setUser(data.user)
      router.push({ name: 'customer.dashboard' })
      notification.notify(
        'Registration successful',
        'success'
      )
    } catch (error) {
      const status = error.response?.status
      if (status === 429) {
        errors.value = {
          general: ['Too many signup attempts. Please try again later.']
        }
        return
      }
      if (status === 422) {
        errors.value = error.response.data.errors
        return
      }
    } finally {
      processing.value = false
    }
  }
  const submitLogin = async () => {
    if (!loginForm.email || !loginForm.password) {
      errors.value = {
        general: ['All the fields are required']
      }
      return
    }
    const notification = useNotificationStore();
    processing.value = true
    errors.value = {}
    try {
      const { data } = await api.post('/login', loginForm)
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
      const status = error.response?.status
      if (status === 429) {
        errors.value = {
          general: ['Too many login attempts. Please try again later.']
        }
        return
      }
      if (status === 401) {
        errors.value = {
          general: ['Invalid email or password']
        }
        return
      }
      errors.value = {
        general: ['Something went wrong. Please try again.']
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
