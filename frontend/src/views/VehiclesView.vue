<template>
  <AppLayout>
    <div class="vehicles-view">
      <div class="header">
        <h1>Mes véhicules</h1>
        <button @click="addVehicle" class="btn btn-primary">
          + Ajouter un véhicule
        </button>
      </div>

    <div v-if="loading" class="loading">Chargement...</div>

    <div v-else-if="error" class="error">{{ error }}</div>

    <div v-else class="vehicles-grid">
      <div
        v-for="vehicle in vehicles"
        :key="vehicle.id"
        class="vehicle-card"
      >
        <div class="vehicle-header">
          <h3>{{ vehicle.name }}</h3>
          <span :class="['badge', vehicle.is_active ? 'badge-active' : 'badge-inactive']">
            {{ vehicle.is_active ? 'Actif' : 'Inactif' }}
          </span>
        </div>
        <div class="vehicle-details">
          <p><strong>{{ vehicle.brand }} {{ vehicle.model }}</strong></p>
          <p class="registration">{{ vehicle.registration_number }}</p>
          <p>Puissance fiscale: {{ vehicle.fiscal_power }} CV</p>
          <p>Carburant: {{ getFuelTypeLabel(vehicle.fuel_type) }}</p>
          <p>Type: {{ vehicle.type === 'personal' ? 'Personnel' : 'Société' }}</p>
        </div>
        <div class="vehicle-actions">
          <button @click="viewMileageRate(vehicle.id)" class="btn btn-sm btn-secondary">
            Voir barème
          </button>
          <button @click="editVehicle(vehicle.id)" class="btn btn-sm btn-primary">
            Modifier
          </button>
        </div>
      </div>

      <div v-if="vehicles.length === 0" class="empty-state">
        <p>Aucun véhicule enregistré</p>
        <button @click="addVehicle" class="btn btn-primary">
          Ajouter votre premier véhicule
        </button>
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
  alert(`Édition du véhicule ${id} à implémenter`)
}

const viewMileageRate = async (id: number) => {
  try {
    const rate: any = await api.vehicles.getMileageRate(id, 5000)
    alert(`Barème kilométrique: ${rate.rate} TND/km pour 5000 km/an\nPuissance fiscale: ${rate.fiscal_power} CV`)
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
.vehicles-view {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #1f2937;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-secondary {
  background-color: #e5e7eb;
  color: #1f2937;
}

.btn-secondary:hover {
  background-color: #d1d5db;
}

.loading,
.error {
  text-align: center;
  padding: 3rem;
}

.error {
  color: #ef4444;
}

.vehicles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.vehicle-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
}

.vehicle-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.vehicle-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1rem;
}

.vehicle-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
}

.badge-active {
  background-color: #d1fae5;
  color: #065f46;
}

.badge-inactive {
  background-color: #fee2e2;
  color: #991b1b;
}

.vehicle-details p {
  margin: 0.5rem 0;
  font-size: 0.875rem;
  color: #374151;
}

.registration {
  color: #6b7280;
  font-family: monospace;
}

.vehicle-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state p {
  color: #6b7280;
  font-size: 1.125rem;
  margin-bottom: 1.5rem;
}
</style>
