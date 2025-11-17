<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto px-4 py-8">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nouveau rapport de frais</h1>
        <p class="text-gray-600 mt-1">Créez un nouveau rapport pour soumettre vos dépenses</p>
      </div>

      <div v-if="error" class="error-message mb-4">
        {{ error }}
      </div>

      <form @submit.prevent="submit" class="card">
        <div class="form-group">
          <label>Titre *</label>
          <input
            v-model="formData.title"
            type="text"
            required
            class="form-control"
            placeholder="Ex: Frais de mission Janvier 2025"
          />
        </div>

        <div class="form-group">
          <label>Période de début *</label>
          <input
            v-model="formData.period_start"
            type="date"
            required
            :max="formData.period_end || today"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Période de fin *</label>
          <input
            v-model="formData.period_end"
            type="date"
            required
            :min="formData.period_start"
            :max="today"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Description (facultatif)</label>
          <textarea
            v-model="formData.description"
            class="form-control"
            rows="3"
            placeholder="Ajoutez des détails sur ce rapport..."
          ></textarea>
        </div>

        <div class="form-footer">
          <button
            type="button"
            @click="$router.push('/expense-reports')"
            class="btn btn-secondary"
          >
            Annuler
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="btn btn-primary"
          >
            {{ saving ? 'Création...' : 'Créer le rapport' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'

const router = useRouter()
const saving = ref(false)
const error = ref('')

const formData = ref({
  title: '',
  period_start: new Date().toISOString().split('T')[0],
  period_end: new Date().toISOString().split('T')[0],
  description: ''
})

const today = computed(() => new Date().toISOString().split('T')[0])

const submit = async () => {
  saving.value = true
  error.value = ''
  try {
    const response: any = await api.expenseReports.create(formData.value)
    // Redirect to the detail page
    router.push(`/expense-reports/${response.id}`)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de la création du rapport'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.card {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.error-message {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  border-left: 4px solid #dc2626;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  margin-top: 1rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #2563eb;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #1f2937;
}

.btn-secondary:hover {
  background-color: #d1d5db;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
