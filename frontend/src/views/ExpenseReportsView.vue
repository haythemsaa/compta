<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="row mb-4 animate__animated animate__fadeIn">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="display-6 fw-bold mb-2">
              <i class="bi bi-file-earmark-text me-3"></i>
              Rapports de frais
            </h1>
            <p class="text-muted mb-0">Gérez et suivez vos notes de frais professionnels</p>
          </div>
          <router-link to="/expense-reports/new" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Nouveau rapport
          </router-link>
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4 animate__animated animate__fadeInUp">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold small">
              <i class="bi bi-funnel me-1"></i>
              Statut
            </label>
            <select v-model="statusFilter" @change="loadReports" class="form-select">
              <option value="">Tous les statuts</option>
              <option value="draft">Brouillon</option>
              <option value="submitted">Soumis</option>
              <option value="approved">Approuvé</option>
              <option value="rejected">Rejeté</option>
              <option value="paid">Payé</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small">
              <i class="bi bi-calendar-range me-1"></i>
              Date de début
            </label>
            <input
              v-model="startDate"
              @change="loadReports"
              type="date"
              class="form-control"
            />
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small">
              <i class="bi bi-calendar-check me-1"></i>
              Date de fin
            </label>
            <input
              v-model="endDate"
              @change="loadReports"
              type="date"
              class="form-control"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
      <p class="mt-3 text-muted">Chargement des rapports...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ error }}
    </div>

    <!-- Reports Table/List -->
    <div v-else>
      <!-- Empty State -->
      <div v-if="reports.length === 0" class="card animate__animated animate__fadeIn">
        <div class="card-body text-center py-5">
          <i class="bi bi-inbox display-1 text-muted mb-4"></i>
          <h4 class="mb-3">Aucun rapport de frais trouvé</h4>
          <p class="text-muted mb-4">Commencez par créer votre premier rapport de frais</p>
          <router-link to="/expense-reports/new" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Créer votre premier rapport
          </router-link>
        </div>
      </div>

      <!-- Reports Table (Desktop) -->
      <div v-else class="card d-none d-lg-block animate__animated animate__fadeInUp">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th scope="col" class="ps-4">
                  <i class="bi bi-hash me-2"></i>Référence
                </th>
                <th scope="col">
                  <i class="bi bi-file-text me-2"></i>Titre
                </th>
                <th scope="col">
                  <i class="bi bi-calendar-event me-2"></i>Période
                </th>
                <th scope="col">
                  <i class="bi bi-cash-stack me-2"></i>Montant
                </th>
                <th scope="col">
                  <i class="bi bi-flag me-2"></i>Statut
                </th>
                <th scope="col" class="text-end pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(report, index) in reports"
                :key="report.id"
                class="animate__animated animate__fadeIn"
                :style="\`animation-delay: \${0.05 * index}s\`"
              >
                <td class="ps-4">
                  <code class="text-primary fw-semibold">{{ report.reference }}</code>
                </td>
                <td>
                  <div class="fw-semibold">{{ report.title }}</div>
                  <small class="text-muted">Créé le {{ formatDate(report.created_at) }}</small>
                </td>
                <td>
                  <small>
                    {{ formatDate(report.period_start) }}
                    <br />
                    <i class="bi bi-arrow-down small"></i>
                    {{ formatDate(report.period_end) }}
                  </small>
                </td>
                <td>
                  <span class="fw-bold fs-5">{{ formatAmount(report.total_amount) }}</span>
                  <small class="text-muted d-block">TND</small>
                </td>
                <td>
                  <span :class="['badge', 'fs-6', getStatusClass(report.status)]">
                    {{ getStatusLabel(report.status) }}
                  </span>
                </td>
                <td class="text-end pe-4">
                  <router-link
                    :to="\`/expense-reports/\${report.id}\`"
                    class="btn btn-sm btn-outline-primary"
                  >
                    <i class="bi bi-eye me-1"></i>
                    Voir
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reports Cards (Mobile) -->
      <div class="d-lg-none">
        <div
          v-for="(report, index) in reports"
          :key="report.id"
          class="card mb-3 animate__animated animate__fadeInUp"
          :style="\`animation-delay: \${0.1 * index}s\`"
        >
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h6 class="mb-1 fw-bold">{{ report.title }}</h6>
                <code class="small text-primary">{{ report.reference }}</code>
              </div>
              <span :class="['badge', getStatusClass(report.status)]">
                {{ getStatusLabel(report.status) }}
              </span>
            </div>
            <div class="mb-3">
              <div class="small text-muted mb-1">
                <i class="bi bi-calendar-range me-1"></i>
                {{ formatDate(report.period_start) }} - {{ formatDate(report.period_end) }}
              </div>
              <div class="small text-muted">
                <i class="bi bi-clock me-1"></i>
                Créé le {{ formatDate(report.created_at) }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="text-muted small">Montant total</div>
                <div class="fw-bold fs-4">{{ formatAmount(report.total_amount) }} TND</div>
              </div>
              <router-link
                :to="\`/expense-reports/\${report.id}\`"
                class="btn btn-primary"
              >
                <i class="bi bi-eye me-1"></i>
                Voir
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import type { ExpenseReport } from '../types'
import AppLayout from '../components/AppLayout.vue'

const router = useRouter()
const loading = ref(false)
const error = ref('')

const reports = ref<ExpenseReport[]>([])
const statusFilter = ref('')
const startDate = ref('')
const endDate = ref('')

const loadReports = async () => {
  loading.value = true
  error.value = ''
  try {
    const params: any = {}
    if (statusFilter.value) params.status = statusFilter.value
    if (startDate.value) params.start_date = startDate.value
    if (endDate.value) params.end_date = endDate.value

    const response: any = await api.expenseReports.list(params)
    reports.value = response.data || []
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du chargement des rapports'
  } finally {
    loading.value = false
  }
}

const formatAmount = (amount: number): string => {
  return amount.toFixed(3)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    draft: 'Brouillon',
    submitted: 'Soumis',
    approved: 'Approuvé',
    rejected: 'Rejeté',
    paid: 'Payé'
  }
  return labels[status] || status
}

const getStatusClass = (status: string): string => {
  const classes: Record<string, string> = {
    draft: 'bg-secondary',
    submitted: 'bg-primary',
    approved: 'bg-success',
    rejected: 'bg-danger',
    paid: 'bg-info'
  }
  return classes[status] || 'bg-secondary'
}

onMounted(() => {
  loadReports()
})
</script>

<style scoped>
.table thead {
  position: sticky;
  top: 0;
  z-index: 10;
}

.table tbody tr {
  cursor: pointer;
  transition: all 0.3s ease;
}

.table tbody tr:hover {
  background-color: rgba(79, 70, 229, 0.05) !important;
}

code {
  font-size: 0.9rem;
}
</style>
