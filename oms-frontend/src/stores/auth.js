// src/stores/auth.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'
import { Routes } from '@/api/api.js'

export const useAuthStore = defineStore('auth', () => {
  const authenticated = ref(false)
  const user = ref({})

  const getUser = async () => {
    try {
      const { data } = await api.get(Routes.getUser)

      if (data.success) {
        user.value = data.data
        authenticated.value = true
        return true
      }

      user.value = {}
      authenticated.value = false
      return false
    } catch (error) {
      user.value = {}
      authenticated.value = false
      return false
    }
  }

  const logout = () => {
    user.value = {}
    authenticated.value = false
  }

  return { authenticated, user, getUser, logout }
}, {
  persist: true
})
