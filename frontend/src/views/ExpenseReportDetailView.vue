<template>
  <AppLayout>
    <!-- Page Header with Actions -->
    <div class="row mb-4 animate__animated animate__fadeIn">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-start">
          <div class="flex-grow-1">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <router-link to="/expense-reports">Rapports de frais</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                  {{ isNew ? 'Nouveau rapport' : report?.reference }}
                </li>
              </ol>
            </nav>
            <h1 class="display-6 fw-bold mb-2">
              {{ isNew ? 'Nouveau rapport de frais' : report?.title }}
            </h1>
            <div v-if="!isNew && report" class="d-flex align-items-center gap-3">
              <code class="text-primary fs-6">{{ report.reference }}</code>
              <span :class="['badge', 'fs-6', getStatusClass(report.status)]">
                {{ getStatusLabel(report.status) }}
              </span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="btn-group" role="group">
            <button @click="goBack" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>
              Retour
            </button>

            <!-- Draft Actions -->
            <template v-if="report?.status === 'draft'">
              <button
                @click="saveReport"
                class="btn btn-primary"
                :disabled="saving"
              >
                <span v-if="saving">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Enregistrement...
                </span>
                <span v-else>
                  <i class="bi bi-save me-2"></i>
                  Enregistrer
                </span>
              </button>
              <button
                v-if="!isNew"
                @click="submitReport"
                class="btn btn-success"
                :disabled="submitting"
              >
                <span v-if="submitting">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Soumission...
                </span>
                <span v-else>
                  <i class="bi bi-send me-2"></i>
                  Soumettre
                </span>
              </button>
            </template>

            <!-- Manager Actions -->
            <template v-if="report?.status === 'submitted' && canApprove">
              <button
                @click="approveReport"
                class="btn btn-success"
                :disabled="approving"
              >
                <span v-if="approving">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Approbation...
                </span>
                <span v-else>
                  <i class="bi bi-check-circle me-2"></i>
                  Approuver
                </span>
              </button>
              <button
                @click="showRejectModal = true"
                class="btn btn-danger"
              >
                <i class="bi bi-x-circle me-2"></i>
                Rejeter
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
      <p class="mt-3 text-muted">Chargement du rapport...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger animate__animated animate__shakeX" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ error }}
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Rejection Notice -->
      <div
        v-if="report?.status === 'rejected' && report.rejection_reason"
        class="alert alert-danger animate__animated animate__fadeIn mb-4"
      >
        <h5 class="alert-heading">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          Rapport rejeté
        </h5>
        <hr />
        <p class="mb-0">
          <strong>Raison:</strong> {{ report.rejection_reason }}
        </p>
      </div>

      <div class="row g-4">
        <!-- Left Column: Report Info -->
        <div class="col-12 col-lg-4">
          <!-- General Information Card -->
          <div class="card mb-4 animate__animated animate__fadeInLeft">
            <div class="card-header bg-gradient-primary text-white">
              <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Informations générales
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">
                  <i class="bi bi-pencil me-2"></i>
                  Titre <span class="text-danger">*</span>
                </label>
                <input
                  v-model="formData.title"
                  type="text"
                  class="form-control"
                  :disabled="report?.status !== 'draft'"
                  placeholder="Ex: Déplacement professionnel"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">
                  <i class="bi bi-calendar-range me-2"></i>
                  Période
                </label>
                <div class="row g-2">
                  <div class="col-6">
                    <input
                      v-model="formData.period_start"
                      type="date"
                      class="form-control"
                      :disabled="report?.status !== 'draft'"
                    />
                    <div class="form-text small">Début</div>
                  </div>
                  <div class="col-6">
                    <input
                      v-model="formData.period_end"
                      type="date"
                      class="form-control"
                      :disabled="report?.status !== 'draft'"
                    />
                    <div class="form-text small">Fin</div>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">
                  <i class="bi bi-text-paragraph me-2"></i>
                  Description
                </label>
                <textarea
                  v-model="formData.description"
                  class="form-control"
                  rows="4"
                  :disabled="report?.status !== 'draft'"
                  placeholder="Mission, contexte, remarques..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Summary Card -->
          <div v-if="!isNew" class="card animate__animated animate__fadeInLeft" style="animation-delay: 0.1s">
            <div class="card-header bg-gradient-success text-white">
              <h5 class="mb-0">
                <i class="bi bi-calculator me-2"></i>
                Récapitulatif
              </h5>
            </div>
            <div class="card-body">
              <div class="summary-item">
                <span class="label">Dépenses:</span>
                <span class="value">{{ items.length }}</span>
              </div>
              <div class="summary-item">
                <span class="label">Frais kilométriques:</span>
                <span class="value">{{ mileageExpenses.length }}</span>
              </div>
              <hr />
              <div class="summary-item total">
                <span class="label">Total TTC:</span>
                <span class="value text-primary fs-4 fw-bold">
                  {{ formatAmount(report?.total_amount || 0) }} TND
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Expenses & Mileage -->
        <div class="col-12 col-lg-8">
          <!-- Expense Items Section -->
          <div v-if="!isNew" class="card mb-4 animate__animated animate__fadeInRight">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="bi bi-receipt me-2"></i>
                Dépenses ({{ items.length }})
              </h5>
              <button
                v-if="report?.status === 'draft'"
                @click="addExpenseItem"
                class="btn btn-sm btn-primary"
              >
                <i class="bi bi-plus-circle me-2"></i>
                Ajouter
              </button>
            </div>
            <div class="card-body">
              <!-- Empty State -->
              <div v-if="items.length === 0" class="text-center py-4">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <p class="text-muted">Aucune dépense ajoutée</p>
                <button
                  v-if="report?.status === 'draft'"
                  @click="addExpenseItem"
                  class="btn btn-primary"
                >
                  <i class="bi bi-plus-circle me-2"></i>
                  Ajouter votre première dépense
                </button>
              </div>

              <!-- Expenses List -->
              <div v-else class="list-group list-group-flush">
                <div
                  v-for="(item, index) in items"
                  :key="item.id"
                  class="list-group-item px-0 animate__animated animate__fadeIn"
                  :style="`animation-delay: ${0.05 * index}s`"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <div class="d-flex align-items-center mb-2">
                        <span class="category-icon me-2">{{ item.category?.icon }}</span>
                        <h6 class="mb-0">{{ item.category?.name }}</h6>
                      </div>
                      <p class="mb-1 small text-muted">
                        <i class="bi bi-shop me-1"></i>
                        {{ item.merchant_name }}
                        <span class="mx-2">•</span>
                        <i class="bi bi-calendar me-1"></i>
                        {{ formatDate(item.date) }}
                      </p>
                      <p v-if="item.description" class="mb-0 small">{{ item.description }}</p>
                    </div>
                    <div class="text-end">
                      <div class="fw-bold fs-5">{{ formatAmount(item.amount) }} TND</div>
                      <small class="text-muted">TVA {{ item.tva_rate }}%</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Mileage Expenses Section -->
          <div v-if="!isNew" class="card animate__animated animate__fadeInRight" style="animation-delay: 0.1s">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="bi bi-car-front me-2"></i>
                Frais kilométriques ({{ mileageExpenses.length }})
              </h5>
              <button
                v-if="report?.status === 'draft'"
                @click="addMileageExpense"
                class="btn btn-sm btn-primary"
              >
                <i class="bi bi-plus-circle me-2"></i>
                Ajouter
              </button>
            </div>
            <div class="card-body">
              <!-- Empty State -->
              <div v-if="mileageExpenses.length === 0" class="text-center py-4">
                <i class="bi bi-car-front display-4 text-muted mb-3"></i>
                <p class="text-muted">Aucun frais kilométrique ajouté</p>
                <button
                  v-if="report?.status === 'draft'"
                  @click="addMileageExpense"
                  class="btn btn-primary"
                >
                  <i class="bi bi-plus-circle me-2"></i>
                  Ajouter votre premier frais
                </button>
              </div>

              <!-- Mileage List -->
              <div v-else class="list-group list-group-flush">
                <div
                  v-for="(expense, index) in mileageExpenses"
                  :key="expense.id"
                  class="list-group-item px-0 animate__animated animate__fadeIn"
                  :style="`animation-delay: ${0.05 * index}s`"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <h6 class="mb-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ expense.start_location }}
                        <i class="bi bi-arrow-right mx-2"></i>
                        {{ expense.end_location }}
                      </h6>
                      <p class="mb-1 small text-muted">
                        <i class="bi bi-speedometer me-1"></i>
                        {{ expense.distance_km }} km
                        <span v-if="expense.round_trip" class="badge bg-info ms-2">Aller-retour</span>
                        <span class="mx-2">•</span>
                        <i class="bi bi-calendar me-1"></i>
                        {{ formatDate(expense.date) }}
                      </p>
                      <p v-if="expense.purpose" class="mb-0 small">{{ expense.purpose }}</p>
                    </div>
                    <div class="text-end">
                      <div class="fw-bold fs-5">{{ formatAmount(expense.amount) }} TND</div>
                      <small class="text-muted">{{ expense.rate }} TND/km</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <AddExpenseItemModal
      :is-open="showExpenseModal"
      :report-id="Number(reportId)"
      @close="showExpenseModal = false"
      @success="handleExpenseAdded"
    />

    <AddMileageExpenseModal
      :is-open="showMileageModal"
      :report-id="Number(reportId)"
      @close="showMileageModal = false"
      @success="handleMileageAdded"
    />

    <!-- Reject Modal -->
    <div
      v-if="showRejectModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click.self="showRejectModal = false"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content animate__animated animate__zoomIn animate__faster">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">
              <i class="bi bi-x-circle me-2"></i>
              Rejeter le rapport
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showRejectModal = false"></button>
          </div>
          <div class="modal-body">
            <label class="form-label fw-semibold">
              Raison du rejet <span class="text-danger">*</span>
            </label>
            <textarea
              v-model="rejectReason"
              class="form-control"
              rows="4"
              placeholder="Expliquez pourquoi ce rapport est rejeté..."
              required
            ></textarea>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="showRejectModal = false"
              :disabled="rejecting"
            >
              Annuler
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="rejectReport"
              :disabled="rejecting || !rejectReason.trim()"
            >
              <span v-if="rejecting">
                <span class="spinner-border spinner-border-sm me-2"></span>
                Rejet...
              </span>
              <span v-else>
                <i class="bi bi-x-circle me-2"></i>
                Confirmer le rejet
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../services/api'
import type { ExpenseReport, ExpenseItem, MileageExpense } from '../types'
import AppLayout from '../components/AppLayout.vue'
import AddExpenseItemModal from '../components/AddExpenseItemModal.vue'
import AddMileageExpenseModal from '../components/AddMileageExpenseModal.vue'

const router = useRouter()
const route = useRoute()
const reportId = ref(route.params.id)
const isNew = computed(() => reportId.value === 'new')

const loading = ref(true)
const saving = ref(false)
const submitting = ref(false)
const approving = ref(false)
const rejecting = ref(false)
const error = ref('')

const report = ref<ExpenseReport | null>(null)
const items = ref<ExpenseItem[]>([])
const mileageExpenses = ref<MileageExpense[]>([])

const showExpenseModal = ref(false)
const showMileageModal = ref(false)
const showRejectModal = ref(false)
const rejectReason = ref('')

const formData = ref({
  title: '',
  period_start: new Date().toISOString().split('T')[0],
  period_end: new Date().toISOString().split('T')[0],
  description: ''
})

const canApprove = computed(() => {
  // TODO: Check user permissions
  return true
})

const loadReport = async () => {
  if (isNew.value) {
    loading.value = false
    return
  }

  loading.value = true
  error.value = ''
  try {
    const data: any = await api.expenseReports.get(Number(reportId.value))
    report.value = data
    items.value = data.items || []
    mileageExpenses.value = data.mileage_expenses || []
    formData.value = {
      title: data.title,
      period_start: data.period_start,
      period_end: data.period_end,
      description: data.description || ''
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du chargement du rapport'
  } finally {
    loading.value = false
  }
}

const saveReport = async () => {
  saving.value = true
  error.value = ''
  try {
    if (isNew.value) {
      const response: any = await api.expenseReports.create(formData.value)
      router.push(`/expense-reports/${response.id}`)
    } else {
      await api.expenseReports.update(Number(reportId.value), formData.value)
      await loadReport()
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'enregistrement'
  } finally {
    saving.value = false
  }
}

const submitReport = async () => {
  if (!confirm('Soumettre ce rapport pour approbation ?')) return
  submitting.value = true
  error.value = ''
  try {
    await api.expenseReports.submit(Number(reportId.value))
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de la soumission'
  } finally {
    submitting.value = false
  }
}

const approveReport = async () => {
  if (!confirm('Approuver ce rapport de frais ?')) return
  approving.value = true
  error.value = ''
  try {
    await api.expenseReports.approve(Number(reportId.value))
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'approbation'
  } finally {
    approving.value = false
  }
}

const rejectReport = async () => {
  if (!rejectReason.value.trim()) return
  rejecting.value = true
  error.value = ''
  try {
    await api.expenseReports.reject(Number(reportId.value), rejectReason.value)
    showRejectModal.value = false
    rejectReason.value = ''
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du rejet'
  } finally {
    rejecting.value = false
  }
}

const addExpenseItem = () => {
  showExpenseModal.value = true
}

const addMileageExpense = () => {
  showMileageModal.value = true
}

const handleExpenseAdded = () => {
  loadReport()
}

const handleMileageAdded = () => {
  loadReport()
}

const goBack = () => {
  router.push('/expense-reports')
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

const getStatusLabel = (status?: string): string => {
  const labels: Record<string, string> = {
    draft: 'Brouillon',
    submitted: 'Soumis',
    approved: 'Approuvé',
    rejected: 'Rejeté',
    paid: 'Payé'
  }
  return status ? labels[status] || status : ''
}

const getStatusClass = (status?: string): string => {
  const classes: Record<string, string> = {
    draft: 'bg-secondary',
    submitted: 'bg-primary',
    approved: 'bg-success',
    rejected: 'bg-danger',
    paid: 'bg-info'
  }
  return status ? classes[status] || 'bg-secondary' : 'bg-secondary'
}

onMounted(() => {
  loadReport()
})
</script>

<style scoped>
.category-icon {
  font-size: 1.5rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
}

.summary-item.total {
  border-top: 2px solid var(--border-color);
  padding-top: 1rem;
  margin-top: 0.5rem;
}

.summary-item .label {
  color: #6b7280;
  font-weight: 500;
}

.summary-item .value {
  font-weight: 600;
}

.list-group-item:last-child {
  border-bottom: none;
}

.modal {
  display: block;
}
</style>
