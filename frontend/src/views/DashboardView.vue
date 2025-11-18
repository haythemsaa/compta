<template>
  <AppLayout>
    <!-- Page Header with Gradient -->
    <div class="row mb-4 animate__animated animate__fadeIn">
      <div class="col-12">
        <div class="bg-gradient-primary text-white rounded-4 p-4 shadow-lg">
          <h1 class="display-6 fw-bold mb-2">
            <i class="bi bi-speedometer2 me-3"></i>
            Tableau de bord
          </h1>
          <p class="mb-0 opacity-90">
            Bienvenue sur votre espace de gestion des frais professionnels
          </p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
      <p class="mt-3 text-muted">Chargement des données...</p>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Stats Cards Row -->
      <div class="row g-4 mb-5">
        <!-- Draft Card -->
        <div class="col-12 col-sm-6 col-lg-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
          <div class="stats-card">
            <div class="icon bg-secondary bg-opacity-10 text-secondary">
              <i class="bi bi-file-earmark"></i>
            </div>
            <div class="value">{{ stats.draft }}</div>
            <div class="label">Brouillons</div>
          </div>
        </div>

        <!-- Submitted Card -->
        <div class="col-12 col-sm-6 col-lg-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
          <div class="stats-card">
            <div class="icon bg-primary bg-opacity-10 text-primary">
              <i class="bi bi-send"></i>
            </div>
            <div class="value">{{ stats.submitted }}</div>
            <div class="label">Soumis</div>
          </div>
        </div>

        <!-- Approved Card -->
        <div class="col-12 col-sm-6 col-lg-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
          <div class="stats-card">
            <div class="icon bg-success bg-opacity-10 text-success">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="value">{{ stats.approved }}</div>
            <div class="label">Approuvés</div>
          </div>
        </div>

        <!-- Total Month Card -->
        <div class="col-12 col-sm-6 col-lg-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
          <div class="stats-card">
            <div class="icon bg-warning bg-opacity-10 text-warning">
              <i class="bi bi-cash-stack"></i>
            </div>
            <div class="value">{{ formatAmount(stats.total_month) }}</div>
            <div class="label">Total mois (TND)</div>
          </div>
        </div>
      </div>

      <!-- Recent Reports Section -->
      <div class="row g-4 mb-5">
        <div class="col-12 col-lg-8 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Derniers rapports
              </h5>
              <router-link to="/expense-reports" class="btn btn-sm btn-outline-primary">
                Voir tout
              </router-link>
            </div>
            <div class="card-body">
              <!-- Empty State -->
              <div v-if="recentReports.length === 0" class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <p class="text-muted">Aucun rapport de frais pour le moment</p>
                <router-link to="/expense-reports/new" class="btn btn-primary mt-2">
                  <i class="bi bi-plus-circle me-2"></i>
                  Créer votre premier rapport
                </router-link>
              </div>

              <!-- Reports List -->
              <div v-else class="list-group list-group-flush">
                <router-link
                  v-for="(report, index) in recentReports.slice(0, 5)"
                  :key="report.id"
                  :to="\`/expense-reports/\${report.id}\`"
                  class="list-group-item list-group-item-action border-0 px-0 py-3 animate__animated animate__fadeIn"
                  :style="\`animation-delay: \${0.1 * index}s\`"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <h6 class="mb-1 fw-semibold">{{ report.title }}</h6>
                      <p class="mb-0 small text-muted">
                        <i class="bi bi-hash me-1"></i>
                        {{ report.reference }}
                        <span class="mx-2">•</span>
                        <i class="bi bi-calendar me-1"></i>
                        {{ formatDate(report.created_at) }}
                      </p>
                    </div>
                    <div class="text-end ms-3">
                      <div class="fw-bold mb-1">{{ formatAmount(report.total_amount) }} TND</div>
                      <span :class="['badge', getStatusClass(report.status)]">
                        {{ getStatusLabel(report.status) }}
                      </span>
                    </div>
                  </div>
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Category Breakdown Section -->
        <div class="col-12 col-lg-4 animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
          <div class="card h-100">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="bi bi-pie-chart me-2"></i>
                Répartition par catégorie
              </h5>
            </div>
            <div class="card-body">
              <div v-if="breakdown.length === 0" class="text-center py-5">
                <i class="bi bi-pie-chart display-4 text-muted mb-3"></i>
                <p class="text-muted small">Aucune dépense enregistrée</p>
              </div>

              <div v-else class="category-breakdown">
                <div
                  v-for="(cat, index) in breakdown.slice(0, 6)"
                  :key="cat.name"
                  class="category-item animate__animated animate__fadeInRight"
                  :style="\`animation-delay: \${0.1 * index}s\`"
                >
                  <div class="d-flex align-items-center mb-2">
                    <span class="category-icon me-2">{{ cat.icon }}</span>
                    <span class="category-name">{{ cat.name }}</span>
                    <span class="ms-auto fw-bold">{{ formatAmount(cat.total) }} TND</span>
                  </div>
                  <div class="progress" style="height: 8px">
                    <div
                      class="progress-bar bg-gradient-primary"
                      role="progressbar"
                      :style="\`width: \${getPercentage(cat.total)}%\`"
                      :aria-valuenow="getPercentage(cat.total)"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                  <div class="small text-muted mt-1">
                    {{ cat.count }} dépense{{ cat.count > 1 ? 's' : '' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row g-4 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.7s">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-4">
                <i class="bi bi-lightning-charge me-2"></i>
                Actions rapides
              </h5>
              <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                  <router-link to="/expense-reports/new" class="quick-action-card">
                    <i class="bi bi-file-earmark-plus fs-1 mb-3 text-primary"></i>
                    <h6 class="mb-2">Nouveau rapport</h6>
                    <p class="small text-muted mb-0">Créer un rapport de frais</p>
                  </router-link>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <router-link to="/expense-reports" class="quick-action-card">
                    <i class="bi bi-list-ul fs-1 mb-3 text-success"></i>
                    <h6 class="mb-2">Mes rapports</h6>
                    <p class="small text-muted mb-0">Consulter tous les rapports</p>
                  </router-link>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <router-link to="/vehicles" class="quick-action-card">
                    <i class="bi bi-car-front fs-1 mb-3 text-warning"></i>
                    <h6 class="mb-2">Mes véhicules</h6>
                    <p class="small text-muted mb-0">Gérer vos véhicules</p>
                  </router-link>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <a href="#" class="quick-action-card">
                    <i class="bi bi-gear fs-1 mb-3 text-info"></i>
                    <h6 class="mb-2">Paramètres</h6>
                    <p class="small text-muted mb-0">Configurer votre compte</p>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '../services/api'
import type { DashboardStats, ExpenseReport, CategoryBreakdown } from '../types'
import AppLayout from '../components/AppLayout.vue'

const loading = ref(true)

const stats = ref<DashboardStats>({
  draft: 0,
  submitted: 0,
  approved: 0,
  rejected: 0,
  paid: 0,
  total_month: 0
})

const recentReports = ref<ExpenseReport[]>([])
const breakdown = ref<CategoryBreakdown[]>([])

const maxTotal = computed(() => {
  if (breakdown.value.length === 0) return 1
  return Math.max(...breakdown.value.map((cat) => cat.total))
})

const getPercentage = (total: number): number => {
  return (total / maxTotal.value) * 100
}

const loadDashboard = async () => {
  loading.value = true
  try {
    const dashboardData: any = await api.dashboard.stats()
    stats.value = dashboardData.stats
    recentReports.value = dashboardData.recent_reports.data || dashboardData.recent_reports || []

    // Load category breakdown
    const breakdownData: any = await api.dashboard.categoryBreakdown()
    breakdown.value = breakdownData.breakdown || []
  } catch (error) {
    console.error('Error loading dashboard:', error)
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
    month: 'long',
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
  loadDashboard()
})
</script>

<style scoped>
/* Category Breakdown */
.category-breakdown {
  max-height: 500px;
  overflow-y: auto;
}

.category-item {
  padding: 1rem 0;
  border-bottom: 1px solid var(--border-color);
}

.category-item:last-child {
  border-bottom: none;
}

.category-icon {
  font-size: 1.5rem;
}

.category-name {
  font-weight: 500;
  font-size: 0.95rem;
}

/* Quick Actions */
.quick-action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 2rem 1rem;
  background: white;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-lg);
  text-decoration: none;
  transition: all 0.3s ease;
  min-height: 180px;
}

.quick-action-card:hover {
  border-color: var(--primary-color);
  box-shadow: var(--shadow-lg);
  transform: translateY(-5px);
}

.quick-action-card i {
  transition: all 0.3s ease;
}

.quick-action-card:hover i {
  transform: scale(1.1);
}

.quick-action-card h6 {
  color: var(--dark);
  font-weight: 600;
}

/* Progress Bar Animation */
.progress-bar {
  transition: width 1s ease-in-out;
}

/* Responsive */
@media (max-width: 768px) {
  .stats-card .value {
    font-size: 1.75rem;
  }

  .quick-action-card {
    min-height: 150px;
    padding: 1.5rem 1rem;
  }
}
</style>
