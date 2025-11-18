import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'
import type { User } from '../types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const loading = ref(false)

  const loadUser = async () => {
    loading.value = true
    try {
      const userData: any = await api.auth.me()
      user.value = userData
    } catch (error) {
      console.error('Error loading user:', error)
      user.value = null
    } finally {
      loading.value = false
    }
  }

  const setUser = (userData: User) => {
    user.value = userData
  }

  const clearUser = () => {
    user.value = null
  }

  return {
    user,
    loading,
    loadUser,
    setUser,
    clearUser
  }
})
