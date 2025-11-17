<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
    <div class="text-center">
      <h1 class="text-6xl font-bold text-primary-900 mb-4">
        Expensya Tunisie
      </h1>
      <p class="text-xl text-gray-700 mb-8">
        Solution SaaS de Gestion Automatisée des Notes de Frais
      </p>
      <div class="space-x-4">
        <router-link to="/login" class="btn-primary">
          Se connecter
        </router-link>
        <button class="btn-secondary">
          En savoir plus
        </button>
      </div>
      <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
        <div class="card text-center">
          <div class="text-4xl mb-2">📸</div>
          <h3 class="font-semibold mb-2">OCR Intelligent</h3>
          <p class="text-sm text-gray-600">Photo → Note de frais en 5 secondes</p>
        </div>
        <div class="card text-center">
          <div class="text-4xl mb-2">🇹🇳</div>
          <h3 class="font-semibold mb-2">100% Conformité TN</h3>
          <p class="text-sm text-gray-600">Fiscalité et réglementation tunisienne</p>
        </div>
        <div class="card text-center">
          <div class="text-4xl mb-2">⚡</div>
          <h3 class="font-semibold mb-2">Gain de Temps</h3>
          <p class="text-sm text-gray-600">80% de réduction du temps de traitement</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Home page
</script>
