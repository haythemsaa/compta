<template>
  <!-- Bootstrap Modal -->
  <div
    v-if="isOpen"
    class="modal fade show d-block"
    tabindex="-1"
    style="background-color: rgba(0, 0, 0, 0.5)"
    @click.self="close"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
      <div class="modal-content animate__animated animate__zoomIn animate__faster">
        <!-- Modal Header -->
        <div class="modal-header bg-gradient-primary text-white">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-car-front me-2"></i>
            Ajouter un frais kilométrique
          </h5>
          <button type="button" class="btn-close btn-close-white" @click="close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body p-4">
          <!-- Error Alert -->
          <div v-if="error" class="alert alert-danger animate__animated animate__shakeX" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ error }}
          </div>

          <form @submit.prevent="submit">
            <!-- Vehicle Selection -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-car-front-fill me-2"></i>
                Véhicule <span class="text-danger">*</span>
              </label>
              <select
                v-model="formData.vehicle_id"
                class="form-select form-select-lg"
                required
                @change="updateMileageRate"
              >
                <option value="">Sélectionnez un véhicule</option>
                <option
                  v-for="vehicle in vehicles"
                  :key="vehicle.id"
                  :value="vehicle.id"
                >
                  {{ vehicle.name }} - {{ vehicle.registration_number }} ({{ vehicle.fiscal_power }} CV)
                </option>
              </select>
              <div v-if="selectedVehicle" class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                {{ selectedVehicle.brand }} {{ selectedVehicle.model }} - Puissance fiscale: {{ selectedVehicle.fiscal_power }} CV
              </div>
            </div>

            <!-- Date and Purpose Row -->
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-calendar-event me-2"></i>
                  Date <span class="text-danger">*</span>
                </label>
                <input
                  v-model="formData.date"
                  type="date"
                  class="form-control"
                  required
                  :max="today"
                />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-briefcase me-2"></i>
                  Objet du déplacement <span class="text-danger">*</span>
                </label>
                <input
                  v-model="formData.purpose"
                  type="text"
                  class="form-control"
                  placeholder="Ex: Visite client"
                  required
                />
              </div>
            </div>

            <!-- Start and End Location -->
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-2"></i>
                  Lieu de départ <span class="text-danger">*</span>
                </label>
                <input
                  v-model="formData.start_location"
                  type="text"
                  class="form-control"
                  placeholder="Ex: Tunis"
                  required
                />
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-geo-fill me-2"></i>
                  Lieu d'arrivée <span class="text-danger">*</span>
                </label>
                <input
                  v-model="formData.end_location"
                  type="text"
                  class="form-control"
                  placeholder="Ex: Sfax"
                  required
                />
              </div>
            </div>

            <!-- Distance Calculation -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-semibold mb-0">
                  <i class="bi bi-speedometer me-2"></i>
                  Distance (km) <span class="text-danger">*</span>
                </label>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-primary"
                  @click="calculateDistance"
                  :disabled="!formData.start_location || !formData.end_location || calculating"
                >
                  <span v-if="calculating">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Calcul...
                  </span>
                  <span v-else>
                    <i class="bi bi-calculator me-1"></i>
                    Calculer la distance
                  </span>
                </button>
              </div>
              <input
                v-model.number="formData.distance_km"
                type="number"
                step="0.1"
                min="0"
                class="form-control"
                placeholder="0.0"
                required
              />
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Cliquez sur "Calculer" pour estimer automatiquement la distance
              </div>
            </div>

            <!-- Round Trip -->
            <div class="mb-4">
              <div class="form-check form-switch">
                <input
                  v-model="formData.round_trip"
                  class="form-check-input"
                  type="checkbox"
                  id="roundTrip"
                  style="cursor: pointer; width: 3rem; height: 1.5rem"
                />
                <label class="form-check-label fw-semibold" for="roundTrip" style="cursor: pointer">
                  <i class="bi bi-arrow-left-right me-2"></i>
                  Aller-retour
                </label>
              </div>
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Active cette option pour doubler automatiquement la distance
              </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-pencil me-2"></i>
                Description
              </label>
              <textarea
                v-model="formData.description"
                class="form-control"
                rows="3"
                placeholder="Détails supplémentaires du déplacement..."
              ></textarea>
            </div>

            <!-- Estimated Amount Card -->
            <div v-if="estimatedAmount" class="alert alert-info d-flex align-items-center">
              <i class="bi bi-calculator-fill fs-3 me-3"></i>
              <div class="flex-grow-1">
                <div class="fw-semibold">Montant estimé</div>
                <div class="fs-4 fw-bold text-primary">{{ estimatedAmount }} TND</div>
                <div class="small">
                  <i class="bi bi-info-circle me-1"></i>
                  Barème: {{ estimatedRate?.toFixed(3) }} TND/km
                  <span v-if="formData.round_trip"> • Distance totale: {{ formData.distance_km * 2 }} km</span>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" @click="close" :disabled="saving">
            <i class="bi bi-x-circle me-2"></i>
            Annuler
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="submit"
            :disabled="saving"
          >
            <span v-if="saving">
              <span class="spinner-border spinner-border-sm me-2"></span>
              Enregistrement...
            </span>
            <span v-else>
              <i class="bi bi-check-circle me-2"></i>
              Ajouter le frais
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
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
const estimatedRate = ref<number | null>(null)

const today = computed(() => new Date().toISOString().split('T')[0])

const formData = ref({
  vehicle_id: '',
  date: today.value,
  start_location: '',
  end_location: '',
  distance_km: 0,
  round_trip: false,
  purpose: '',
  description: ''
})

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
    const response: any = await api.vehicles.list()
    vehicles.value = response.data || []
  } catch (err) {
    console.error('Error loading vehicles:', err)
  }
}

const updateMileageRate = async () => {
  if (!formData.value.vehicle_id) {
    estimatedRate.value = null
    return
  }

  try {
    const response: any = await api.vehicles.getMileageRate(Number(formData.value.vehicle_id), 5000)
    estimatedRate.value = response.rate
  } catch (err) {
    console.error('Error getting mileage rate:', err)
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
  if (!saving.value) {
    emit('close')
    resetForm()
  }
}

const resetForm = () => {
  formData.value = {
    vehicle_id: '',
    date: today.value,
    start_location: '',
    end_location: '',
    distance_km: 0,
    round_trip: false,
    purpose: '',
    description: ''
  }
  estimatedRate.value = null
  error.value = ''
}

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    loadVehicles()
  }
})
</script>

<style scoped>
.modal {
  display: block;
}

.form-control:focus,
.form-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}

.form-check-input:checked {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
}

/* Animation */
.animate__faster {
  animation-duration: 0.4s !important;
}
</style>
