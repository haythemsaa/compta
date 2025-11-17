<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-overlay" @click.self="close">
      <div class="modal-container">
        <div class="modal-header">
          <h2>Ajouter un frais kilométrique</h2>
          <button @click="close" class="close-button">×</button>
        </div>

        <div v-if="error" class="error-message">
          {{ error }}
        </div>

        <form @submit.prevent="submit" class="modal-body">
          <div class="form-group">
            <label>Véhicule *</label>
            <select v-model="formData.vehicle_id" required class="form-control">
              <option value="">Sélectionnez un véhicule</option>
              <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                {{ vehicle.name }} ({{ vehicle.fiscal_power }} CV)
              </option>
            </select>
            <p v-if="selectedVehicle" class="text-xs text-gray-500 mt-1">
              Tarif estimé: {{ estimatedRate }} TND/km
            </p>
          </div>

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
            <label>Lieu de départ *</label>
            <input
              v-model="formData.start_location"
              type="text"
              required
              class="form-control"
              placeholder="Ex: Tunis"
            />
          </div>

          <div class="form-group">
            <label>Lieu d'arrivée *</label>
            <input
              v-model="formData.end_location"
              type="text"
              required
              class="form-control"
              placeholder="Ex: Sfax"
            />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Distance (km) *</label>
              <div class="input-group">
                <input
                  v-model.number="formData.distance_km"
                  type="number"
                  step="0.1"
                  min="0"
                  required
                  class="form-control"
                  placeholder="272"
                />
                <button
                  type="button"
                  @click="calculateDistance"
                  :disabled="calculating || !formData.start_location || !formData.end_location"
                  class="btn-calculate"
                >
                  {{ calculating ? '...' : '🔍' }}
                </button>
              </div>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input
                  v-model="formData.round_trip"
                  type="checkbox"
                  class="checkbox"
                />
                <span>Aller-retour</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label>Objet du déplacement *</label>
            <input
              v-model="formData.purpose"
              type="text"
              required
              class="form-control"
              placeholder="Ex: Visite client, réunion..."
            />
          </div>

          <div class="form-group">
            <label>Description (facultatif)</label>
            <textarea
              v-model="formData.description"
              class="form-control"
              rows="2"
              placeholder="Détails supplémentaires..."
            ></textarea>
          </div>

          <div v-if="estimatedAmount" class="estimated-total">
            <span>Montant estimé:</span>
            <strong>{{ estimatedAmount }} TND</strong>
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
import type { Vehicle } from '../types'

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
const calculating = ref(false)
const error = ref('')
const vehicles = ref<Vehicle[]>([])
const estimatedRate = ref<number>(0.29)

const formData = ref({
  vehicle_id: '',
  date: new Date().toISOString().split('T')[0],
  start_location: '',
  end_location: '',
  distance_km: 0,
  round_trip: false,
  purpose: '',
  description: ''
})

const today = computed(() => new Date().toISOString().split('T')[0])

const selectedVehicle = computed(() => {
  return vehicles.value.find(v => v.id === Number(formData.value.vehicle_id))
})

const estimatedAmount = computed(() => {
  if (!formData.value.distance_km || !estimatedRate.value) return null
  const distance = formData.value.round_trip ? formData.value.distance_km * 2 : formData.value.distance_km
  return (distance * estimatedRate.value).toFixed(3)
})

const loadVehicles = async () => {
  try {
    const response: any = await api.vehicles.list({ active: true })
    vehicles.value = response.data || []
  } catch (err) {
    console.error('Error loading vehicles:', err)
  }
}

const calculateDistance = async () => {
  if (!formData.value.start_location || !formData.value.end_location) return

  calculating.value = true
  try {
    const result: any = await api.mileageExpenses.calculateDistance(
      formData.value.start_location,
      formData.value.end_location
    )
    formData.value.distance_km = result.distance_km
  } catch (err) {
    console.error('Error calculating distance:', err)
  } finally {
    calculating.value = false
  }
}

const updateMileageRate = async () => {
  if (!formData.value.vehicle_id) return

  try {
    const rate: any = await api.vehicles.getMileageRate(Number(formData.value.vehicle_id), 5000)
    estimatedRate.value = rate.rate
  } catch (err) {
    console.error('Error getting mileage rate:', err)
  }
}

const submit = async () => {
  saving.value = true
  error.value = ''
  try {
    await api.mileageExpenses.create(props.reportId, formData.value)
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
    vehicle_id: '',
    date: new Date().toISOString().split('T')[0],
    start_location: '',
    end_location: '',
    distance_km: 0,
    round_trip: false,
    purpose: '',
    description: ''
  }
  error.value = ''
}

watch(() => formData.value.vehicle_id, () => {
  if (formData.value.vehicle_id) {
    updateMileageRate()
  }
})

watch(() => props.isOpen, (newVal) => {
  if (newVal && vehicles.value.length === 0) {
    loadVehicles()
  }
})

onMounted(() => {
  if (props.isOpen) {
    loadVehicles()
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

.input-group {
  display: flex;
  gap: 0.5rem;
}

.btn-calculate {
  padding: 0.75rem;
  background-color: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-calculate:hover:not(:disabled) {
  background-color: #e5e7eb;
}

.btn-calculate:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  padding-top: 2rem;
}

.checkbox {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.estimated-total {
  background-color: #f0f9ff;
  padding: 1rem;
  border-radius: 0.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
  border: 1px solid #bfdbfe;
}

.estimated-total strong {
  font-size: 1.125rem;
  color: #1e40af;
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
