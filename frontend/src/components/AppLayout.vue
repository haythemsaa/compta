<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/dashboard" class="flex items-center">
              <h1 class="text-2xl font-bold text-blue-600">Compteo</h1>
              <span class="ml-2 text-sm text-gray-500">Tunisia</span>
            </router-link>
            <div class="ml-10 flex items-center space-x-4">
              <router-link
                to="/dashboard"
                class="nav-link"
                :class="{ 'nav-link-active': isActive('/dashboard') }"
              >
                📊 Tableau de bord
              </router-link>
              <router-link
                to="/expense-reports"
                class="nav-link"
                :class="{ 'nav-link-active': isActive('/expense-reports') }"
              >
                📋 Rapports de frais
              </router-link>
              <router-link
                to="/vehicles"
                class="nav-link"
                :class="{ 'nav-link-active': isActive('/vehicles') }"
              >
                🚗 Véhicules
              </router-link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              to="/expense-reports/new"
              class="btn btn-primary"
            >
              + Nouveau rapport
            </router-link>
            <div class="relative" ref="userMenuRef">
              <button
                @click="toggleUserMenu"
                class="flex items-center space-x-2 text-gray-700 hover:text-gray-900"
              >
                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center">
                  {{ userInitials }}
                </div>
                <span class="text-sm font-medium">{{ user?.name || 'Utilisateur' }}</span>
              </button>
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10"
              >
                <div class="px-4 py-2 text-xs text-gray-500 border-b">
                  {{ user?.email }}
                </div>
                <div class="px-4 py-2 text-xs text-gray-500">
                  {{ getRoleLabel(user?.role) }}
                </div>
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Déconnexion
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../services/api'
import type { User } from '../types'

const router = useRouter()
const route = useRoute()
const user = ref<User | null>(null)
const showUserMenu = ref(false)
const userMenuRef = ref<HTMLElement | null>(null)

const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  const names = user.value.name.split(' ')
  if (names.length >= 2) {
    return `${names[0][0]}${names[1][0]}`.toUpperCase()
  }
  return user.value.name.substring(0, 2).toUpperCase()
})

const isActive = (path: string): boolean => {
  if (path === '/dashboard') {
    return route.path === '/dashboard' || route.path === '/'
  }
  return route.path.startsWith(path)
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleClickOutside = (event: MouseEvent) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
    showUserMenu.value = false
  }
}

const loadUser = async () => {
  try {
    const userData: any = await api.auth.me()
    user.value = userData
  } catch (error) {
    console.error('Error loading user:', error)
  }
}

const logout = () => {
  localStorage.removeItem('auth_token')
  router.push('/login')
}

const getRoleLabel = (role?: string): string => {
  const labels: Record<string, string> = {
    employee: 'Employé',
    manager: 'Manager',
    accountant: 'Comptable',
    daf: 'DAF',
    admin: 'Administrateur'
  }
  return role ? labels[role] || role : ''
}

onMounted(() => {
  loadUser()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.nav-link {
  @apply text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition;
}

.nav-link-active {
  @apply text-blue-600 bg-blue-50;
}

.btn {
  @apply px-4 py-2 rounded-md font-medium transition;
}

.btn-primary {
  @apply bg-blue-600 text-white hover:bg-blue-700;
}
</style>
