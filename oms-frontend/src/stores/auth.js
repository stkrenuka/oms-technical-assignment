// src/stores/auth.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const authenticated = ref(false)

  const setUser = (userData) => {
    user.value = userData
    authenticated.value = true
  }

  const getUser = async () => {
    try {
      const { data } = await api.get('/user')
      user.value = data
      authenticated.value = true
    } catch {
      logout()
    }
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch {}

    localStorage.removeItem('token')
    user.value = null
    authenticated.value = false
  }

  return { user, authenticated, setUser, getUser, logout }
})
