<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-overlay" @click.self="close">
      <div class="modal-container">
        <div class="modal-header">
          <h2>Ajouter une dépense</h2>
          <button @click="close" class="close-button">×</button>
        </div>

        <div v-if="error" class="error-message">
          {{ error }}
        </div>

        <form @submit.prevent="submit" class="modal-body">
          <div class="form-group">
            <label>Catégorie *</label>
            <select v-model="formData.expense_category_id" required class="form-control">
              <option value="">Sélectionnez une catégorie</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.icon }} {{ cat.name }}
              </option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Date *</label>
              <input
                v-model="formData.date"
                type="date"
                required
                :max="today"
                class="form-control"
              />
            </div>
            <div class="form-group">
              <label>Montant TTC *</label>
              <input
                v-model.number="formData.amount"
                type="number"
                step="0.001"
                min="0"
                required
                class="form-control"
                placeholder="0.000"
              />
            </div>
          </div>

          <div class="form-group">
            <label>Marchand *</label>
            <input
              v-model="formData.merchant_name"
              type="text"
              required
              class="form-control"
              placeholder="Nom du marchand"
            />
          </div>

          <div class="form-group">
            <label>Matricule fiscal (facultatif)</label>
            <input
              v-model="formData.merchant_vat_number"
              type="text"
              class="form-control"
              placeholder="1234567/A/M/000"
            />
          </div>

          <div class="form-group">
            <label>Taux TVA</label>
            <select v-model.number="formData.tva_rate" class="form-control">
              <option :value="19">19% (Standard)</option>
              <option :value="13">13% (Réduit)</option>
              <option :value="7">7% (Super-réduit)</option>
              <option :value="0">0% (Exonéré)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea
              v-model="formData.description"
              class="form-control"
              rows="3"
              placeholder="Détails de la dépense..."
            ></textarea>
          </div>

          <div v-if="showGuestFields" class="form-group">
            <label>Nombre de convives</label>
            <input
              v-model.number="formData.guest_count"
              type="number"
              min="0"
              class="form-control"
            />
          </div>

          <div class="modal-footer">
            <button type="button" @click="close" class="btn btn-secondary">
              Annuler
            </button>
            <button type="submit" :disabled="saving" class="btn btn-primary">
              {{ saving ? 'Enregistrement...' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import api from '../services/api'
import type { ExpenseCategory } from '../types'

interface Props {
  isOpen: boolean
  reportId: number
}

interface Emits {
  (e: 'close'): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const saving = ref(false)
const error = ref('')
const categories = ref<ExpenseCategory[]>([])

const formData = ref({
  expense_category_id: '',
  date: new Date().toISOString().split('T')[0],
  merchant_name: '',
  merchant_vat_number: '',
  description: '',
  amount: 0,
  tva_rate: 19,
  guest_count: 0
})

const today = computed(() => new Date().toISOString().split('T')[0])

const showGuestFields = computed(() => {
  const category = categories.value.find(c => c.id === Number(formData.value.expense_category_id))
  return category?.code === 'RESTAURANT'
})

const loadCategories = async () => {
  try {
    const response: any = await api.categories.list({ active: true })
    categories.value = response.data || []
  } catch (err) {
    console.error('Error loading categories:', err)
  }
}

const submit = async () => {
  saving.value = true
  error.value = ''
  try {
    await api.expenseItems.create(props.reportId, formData.value)
    emit('success')
    close()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'ajout'
  } finally {
    saving.value = false
  }
}

const close = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  formData.value = {
    expense_category_id: '',
    date: new Date().toISOString().split('T')[0],
    merchant_name: '',
    merchant_vat_number: '',
    description: '',
    amount: 0,
    tva_rate: 19,
    guest_count: 0
  }
  error.value = ''
}

watch(() => props.isOpen, (newVal) => {
  if (newVal && categories.value.length === 0) {
    loadCategories()
  }
})

onMounted(() => {
  if (props.isOpen) {
    loadCategories()
  }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.modal-container {
  background: white;
  border-radius: 0.75rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.close-button {
  font-size: 2rem;
  color: #6b7280;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-button:hover {
  color: #1f2937;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  margin-top: 1rem;
}

.error-message {
  background-color: #fee2e2;
  color: #991b1b;
  padding: 0.75rem 1.5rem;
  border-left: 4px solid #dc2626;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
