<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <h1 class="text-2xl font-bold text-primary-600">Compteo TN</h1>
          <div class="flex items-center space-x-4">
            <router-link to="/expenses/new" class="btn-primary">
              + Nouvelle note de frais
            </router-link>
            <button @click="logout" class="text-gray-600 hover:text-gray-900">
              Déconnexion
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <h3 class="text-sm text-gray-500 mb-1">Notes en attente</h3>
          <p class="text-3xl font-bold text-primary-600">{{ stats.pending }}</p>
        </div>
        <div class="card">
          <h3 class="text-sm text-gray-500 mb-1">Validées</h3>
          <p class="text-3xl font-bold text-green-600">{{ stats.approved }}</p>
        </div>
        <div class="card">
          <h3 class="text-sm text-gray-500 mb-1">Rejetées</h3>
          <p class="text-3xl font-bold text-red-600">{{ stats.rejected }}</p>
        </div>
        <div class="card">
          <h3 class="text-sm text-gray-500 mb-1">Total mois</h3>
          <p class="text-3xl font-bold text-gray-900">{{ stats.totalMonth }} TND</p>
        </div>
      </div>

      <div class="card">
        <h2 class="text-xl font-semibold mb-4">Dernières notes de frais</h2>
        <div class="text-center py-12 text-gray-500">
          <p>Aucune note de frais pour le moment</p>
          <router-link to="/expenses/new" class="text-primary-600 hover:underline mt-2 inline-block">
            Créer votre première note
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const stats = ref({
  pending: 0,
  approved: 0,
  rejected: 0,
  totalMonth: 0
})

const logout = () => {
  localStorage.removeItem('auth_token')
  router.push('/login')
}
</script>
