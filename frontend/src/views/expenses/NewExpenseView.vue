<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <router-link to="/dashboard" class="text-2xl font-bold text-primary-600">
            Expensya TN
          </router-link>
        </div>
      </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="card">
        <h2 class="text-2xl font-bold mb-6">Nouvelle note de frais</h2>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- OCR Upload Section -->
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary-500 transition-colors cursor-pointer">
            <div class="text-6xl mb-4">📸</div>
            <h3 class="text-lg font-semibold mb-2">Scanner un justificatif</h3>
            <p class="text-sm text-gray-600 mb-4">
              Prenez une photo ou téléchargez votre ticket/facture
            </p>
            <input
              type="file"
              accept="image/*"
              class="hidden"
              id="file-upload"
              @change="handleFileUpload"
            />
            <label for="file-upload" class="btn-primary cursor-pointer">
              Choisir un fichier
            </label>
          </div>

          <!-- Manual Entry -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Catégorie
              </label>
              <select
                v-model="form.category"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Sélectionner une catégorie</option>
                <option value="transport">🚗 Transport</option>
                <option value="restaurant">🍽️ Restaurant</option>
                <option value="hotel">🏨 Hôtel</option>
                <option value="other">📦 Autre</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Montant (TND)
              </label>
              <input
                v-model.number="form.amount"
                type="number"
                step="0.01"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                placeholder="0.00"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Date
              </label>
              <input
                v-model="form.date"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Description
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                placeholder="Décrivez brièvement cette dépense..."
              ></textarea>
            </div>
          </div>

          <div class="flex space-x-4">
            <button type="submit" class="btn-primary flex-1">
              Créer la note de frais
            </button>
            <router-link to="/dashboard" class="btn-secondary flex-1 text-center">
              Annuler
            </router-link>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
  category: '',
  amount: 0,
  date: new Date().toISOString().split('T')[0],
  description: ''
})

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    // TODO: Implement OCR processing
    console.log('File selected:', file.name)
  }
}

const handleSubmit = () => {
  // TODO: Implement API call
  console.log('Creating expense:', form.value)
  router.push('/dashboard')
}
</script>
