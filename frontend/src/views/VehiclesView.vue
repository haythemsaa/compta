<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="row mb-4 animate__animated animate__fadeIn">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="display-6 fw-bold mb-2">
              <i class="bi bi-car-front me-3"></i>
              Mes véhicules
            </h1>
            <p class="text-muted mb-0">Gérez vos véhicules pour les frais kilométriques</p>
          </div>
          <button @click="addVehicle" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Ajouter un véhicule
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
      <p class="mt-3 text-muted">Chargement des véhicules...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger animate__animated animate__shakeX" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ error }}
    </div>

    <!-- Vehicles Grid -->
    <div v-else>
      <!-- Empty State -->
      <div v-if="vehicles.length === 0" class="card animate__animated animate__fadeIn">
        <div class="card-body text-center py-5">
          <i class="bi bi-car-front display-1 text-muted mb-4"></i>
          <h4 class="mb-3">Aucun véhicule enregistré</h4>
          <p class="text-muted mb-4">
            Ajoutez vos véhicules pour pouvoir créer des frais kilométriques
          </p>
          <button @click="addVehicle" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Ajouter votre premier véhicule
          </button>
        </div>
      </div>

      <!-- Vehicles Cards -->
      <div v-else class="row g-4">
        <div
          v-for="(vehicle, index) in vehicles"
          :key="vehicle.id"
          class="col-12 col-md-6 col-lg-4 animate__animated animate__fadeInUp"
          :style="\`animation-delay: \${0.1 * index}s\`"
        >
          <div class="card vehicle-card h-100">
            <!-- Card Header with Status -->
            <div class="card-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="vehicle-icon me-3">
                  <i class="bi bi-car-front-fill fs-3 text-white"></i>
                </div>
                <div>
                  <h5 class="mb-0 fw-bold">{{ vehicle.name }}</h5>
                  <small class="text-muted">{{ vehicle.brand }} {{ vehicle.model }}</small>
                </div>
              </div>
              <span
                :class="[
                  'badge',
                  'rounded-pill',
                  vehicle.is_active ? 'bg-success' : 'bg-secondary'
                ]"
              >
                {{ vehicle.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </div>

            <!-- Card Body -->
            <div class="card-body">
              <!-- Registration Number -->
              <div class="mb-3 text-center">
                <div class="registration-plate">
                  {{ vehicle.registration_number }}
                </div>
              </div>

              <!-- Vehicle Details -->
              <div class="vehicle-details">
                <div class="detail-item">
                  <i class="bi bi-speedometer2 text-primary me-2"></i>
                  <span class="detail-label">Puissance:</span>
                  <span class="detail-value">{{ vehicle.fiscal_power }} CV</span>
                </div>
                <div class="detail-item">
                  <i class="bi bi-fuel-pump text-primary me-2"></i>
                  <span class="detail-label">Carburant:</span>
                  <span class="detail-value">{{ getFuelTypeLabel(vehicle.fuel_type) }}</span>
                </div>
                <div class="detail-item">
                  <i class="bi bi-building text-primary me-2"></i>
                  <span class="detail-label">Type:</span>
                  <span class="detail-value">
                    {{ vehicle.type === 'personal' ? 'Personnel' : 'Société' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Card Footer with Actions -->
            <div class="card-footer bg-light border-0 d-flex gap-2">
              <button
                @click="viewMileageRate(vehicle.id)"
                class="btn btn-sm btn-outline-primary flex-grow-1"
              >
                <i class="bi bi-calculator me-1"></i>
                Barème
              </button>
              <button
                @click="editVehicle(vehicle.id)"
                class="btn btn-sm btn-primary flex-grow-1"
              >
                <i class="bi bi-pencil me-1"></i>
                Modifier
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '../services/api'
import type { Vehicle } from '../types'
import AppLayout from '../components/AppLayout.vue'

const loading = ref(false)
const error = ref('')
const vehicles = ref<Vehicle[]>([])

const loadVehicles = async () => {
  loading.value = true
  error.value = ''
  try {
    const response: any = await api.vehicles.list()
    vehicles.value = response.data || []
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du chargement des véhicules'
  } finally {
    loading.value = false
  }
}

const addVehicle = () => {
  alert('Modal d\'ajout de véhicule à implémenter')
}

const editVehicle = (id: number) => {
  alert(\`Édition du véhicule \${id} à implémenter\`)
}

const viewMileageRate = async (id: number) => {
  try {
    const rate: any = await api.vehicles.getMileageRate(id, 5000)
    alert(
      \`Barème kilométrique:\\n\\n\` +
        \`Véhicule: \${rate.fiscal_power} CV\\n\` +
        \`Tarif pour 5000 km/an: \${rate.rate} TND/km\\n\\n\` +
        \`Note: Le tarif varie selon la distance annuelle parcourue\`
    )
  } catch (err: any) {
    alert('Erreur lors de la récupération du barème')
  }
}

const getFuelTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    essence: 'Essence',
    diesel: 'Diesel',
    gpl: 'GPL',
    electrique: 'Électrique',
    hybride: 'Hybride'
  }
  return labels[type] || type
}

onMounted(() => {
  loadVehicles()
})
</script>

<style scoped>
.vehicle-card {
  border: none;
  transition: all 0.3s ease;
  overflow: hidden;
}

.vehicle-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl) !important;
}

.vehicle-card .card-header {
  background: var(--gradient-primary);
  color: white;
  border: none;
  padding: 1.25rem;
}

.vehicle-icon {
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
}

.registration-plate {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border: 3px solid #2c3e50;
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-family: 'Courier New', monospace;
  font-weight: bold;
  font-size: 1.25rem;
  letter-spacing: 2px;
  color: #2c3e50;
  text-transform: uppercase;
  display: inline-block;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.vehicle-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.detail-item {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  background: var(--light-gray);
  border-radius: 8px;
  transition: all 0.3s ease;
}

.detail-item:hover {
  background: rgba(79, 70, 229, 0.1);
  transform: translateX(5px);
}

.detail-label {
  color: #6b7280;
  font-size: 0.875rem;
  margin-right: 0.5rem;
}

.detail-value {
  font-weight: 600;
  color: var(--dark);
  margin-left: auto;
}

/* Responsive */
@media (max-width: 768px) {
  .vehicle-card {
    margin-bottom: 1rem;
  }
}
</style>
