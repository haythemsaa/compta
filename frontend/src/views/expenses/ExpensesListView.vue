<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <router-link to="/dashboard" class="text-2xl font-bold text-primary-600">
            Expensya TN
          </router-link>
          <router-link to="/expenses/new" class="btn-primary">
            + Nouvelle note de frais
          </router-link>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="card">
        <h2 class="text-2xl font-bold mb-6">Mes notes de frais</h2>

        <div class="text-center py-12 text-gray-500">
          <div class="text-6xl mb-4">📄</div>
          <p class="text-lg mb-2">Aucune note de frais</p>
          <p class="text-sm mb-4">Commencez par créer votre première note de frais</p>
          <router-link to="/expenses/new" class="btn-primary">
            Créer une note de frais
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
// Expenses list view
</script>
