<template>
  <AppLayout>
    <div class="expense-report-detail">
      <div class="header">
        <div>
          <h1>{{ isNew ? 'Nouveau rapport de frais' : report?.title }}</h1>
          <p v-if="!isNew" class="reference">
            {{ report?.reference }}
            <span :class="['status-badge', `status-${report?.status}`]">
              {{ getStatusLabel(report?.status) }}
            </span>
          </p>
        </div>
        <div class="actions">
          <button @click="goBack" class="btn btn-secondary">Retour</button>

          <!-- Draft actions -->
          <button
            v-if="report?.status === 'draft'"
            @click="saveReport"
            class="btn btn-primary"
            :disabled="saving"
          >
            {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
          <button
            v-if="report?.status === 'draft' && !isNew"
            @click="submitReport"
            class="btn btn-success"
            :disabled="submitting"
          >
            {{ submitting ? 'Soumission...' : 'Soumettre' }}
          </button>

          <!-- Manager actions -->
          <button
            v-if="report?.status === 'submitted' && canApprove"
            @click="approveReport"
            class="btn btn-success"
            :disabled="approving"
          >
            {{ approving ? 'Approbation...' : '✓ Approuver' }}
          </button>
          <button
            v-if="report?.status === 'submitted' && canApprove"
            @click="showRejectModal = true"
            class="btn btn-danger"
          >
            ✗ Rejeter
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading">Chargement...</div>

      <div v-else-if="error" class="error">{{ error }}</div>

      <div v-else class="content">
        <!-- Report Info -->
        <div class="card">
          <h2>Informations générales</h2>
          <div class="form-group">
            <label>Titre *</label>
            <input
              v-model="formData.title"
              type="text"
              :disabled="report?.status !== 'draft'"
              class="form-control"
              placeholder="Ex: Déplacement Tunis - Sfax"
            />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea
              v-model="formData.description"
              :disabled="report?.status !== 'draft'"
              class="form-control"
              rows="3"
              placeholder="Mission, contexte..."
            />
          </div>

          <!-- Show rejection reason if rejected -->
          <div v-if="report?.status === 'rejected' && report.rejection_reason" class="rejection-notice">
            <strong>Raison du rejet:</strong> {{ report.rejection_reason }}
          </div>
        </div>

        <!-- Expense Items -->
        <div v-if="!isNew" class="card">
          <div class="section-header">
            <h2>Dépenses ({{ items.length }})</h2>
            <button
              v-if="report?.status === 'draft'"
              @click="addExpenseItem"
              class="btn btn-sm btn-primary"
            >
              + Ajouter une dépense
            </button>
          </div>

          <div v-if="items.length === 0" class="empty-message">
            Aucune dépense ajoutée
          </div>

          <div v-else class="items-list">
            <div
              v-for="item in items"
              :key="item.id"
              class="item-card"
            >
              <div class="item-header">
                <span class="category-badge" :style="{ backgroundColor: item.category?.color }">
                  {{ item.category?.icon }} {{ item.category?.name }}
                </span>
                <span class="item-amount">{{ formatAmount(item.amount) }} TND</span>
              </div>
              <div class="item-details">
                <p><strong>{{ item.merchant_name }}</strong></p>
                <p class="item-date">{{ formatDate(item.date) }}</p>
                <p v-if="item.description" class="item-description">{{ item.description }}</p>
              </div>
              <div class="item-footer">
                <span>HT: {{ formatAmount(item.amount_ht) }} TND</span>
                <span>TVA {{ item.tva_rate }}%: {{ formatAmount(item.tva_amount) }} TND</span>
                <button
                  v-if="report?.status === 'draft'"
                  @click="deleteItem(item.id)"
                  class="btn btn-sm btn-danger"
                >
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Mileage Expenses -->
        <div v-if="!isNew" class="card">
          <div class="section-header">
            <h2>Frais kilométriques ({{ mileageExpenses.length }})</h2>
            <button
              v-if="report?.status === 'draft'"
              @click="addMileageExpense"
              class="btn btn-sm btn-primary"
            >
              + Ajouter un trajet
            </button>
          </div>

          <div v-if="mileageExpenses.length === 0" class="empty-message">
            Aucun frais kilométrique ajouté
          </div>

          <div v-else class="items-list">
            <div
              v-for="mileage in mileageExpenses"
              :key="mileage.id"
              class="item-card"
            >
              <div class="item-header">
                <span>🚗 {{ mileage.vehicle?.name }}</span>
                <span class="item-amount">{{ formatAmount(mileage.total_amount) }} TND</span>
              </div>
              <div class="item-details">
                <p><strong>{{ mileage.start_location }} → {{ mileage.end_location }}</strong></p>
                <p class="item-date">{{ formatDate(mileage.date) }}</p>
                <p>{{ mileage.distance_km }} km {{ mileage.round_trip ? '(Aller-retour)' : '' }}</p>
                <p v-if="mileage.purpose" class="item-description">{{ mileage.purpose }}</p>
              </div>
              <div class="item-footer">
                <span>Tarif: {{ mileage.rate_per_km }} TND/km</span>
                <button
                  v-if="report?.status === 'draft'"
                  @click="deleteMileageExpense(mileage.id)"
                  class="btn btn-sm btn-danger"
                >
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Total -->
        <div v-if="!isNew" class="card totals">
          <h2>Total</h2>
          <div class="total-row">
            <span>Total HT:</span>
            <span class="total-value">{{ formatAmount(report?.total_ht || 0) }} TND</span>
          </div>
          <div class="total-row">
            <span>Total TVA:</span>
            <span class="total-value">{{ formatAmount(report?.total_tva || 0) }} TND</span>
          </div>
          <div class="total-row total-final">
            <span>Total TTC:</span>
            <span class="total-value">{{ formatAmount(report?.total_amount || 0) }} TND</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <AddExpenseItemModal
      v-if="!isNew"
      :is-open="showExpenseItemModal"
      :report-id="reportId!"
      @close="showExpenseItemModal = false"
      @success="handleExpenseItemAdded"
    />

    <AddMileageExpenseModal
      v-if="!isNew"
      :is-open="showMileageModal"
      :report-id="reportId!"
      @close="showMileageModal = false"
      @success="handleMileageAdded"
    />

    <!-- Reject Modal -->
    <Teleport to="body">
      <div v-if="showRejectModal" class="modal-overlay" @click.self="showRejectModal = false">
        <div class="modal-container">
          <div class="modal-header">
            <h2>Rejeter le rapport de frais</h2>
            <button @click="showRejectModal = false" class="close-button">×</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Raison du rejet *</label>
              <textarea
                v-model="rejectReason"
                class="form-control"
                rows="4"
                placeholder="Indiquez la raison du rejet..."
                required
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="showRejectModal = false" class="btn btn-secondary">Annuler</button>
            <button @click="rejectReport" :disabled="rejecting || !rejectReason.trim()" class="btn btn-danger">
              {{ rejecting ? 'Rejet...' : 'Confirmer le rejet' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'
import AddExpenseItemModal from '../components/AddExpenseItemModal.vue'
import AddMileageExpenseModal from '../components/AddMileageExpenseModal.vue'
import type { ExpenseReport, ExpenseItem, MileageExpense } from '../types'

const router = useRouter()
const route = useRoute()

const reportId = computed(() => {
  const id = route.params.id
  return id === 'new' ? null : Number(id)
})
const isNew = computed(() => reportId.value === null)

const loading = ref(false)
const saving = ref(false)
const submitting = ref(false)
const approving = ref(false)
const rejecting = ref(false)
const error = ref('')
const report = ref<ExpenseReport | null>(null)
const items = ref<ExpenseItem[]>([])
const mileageExpenses = ref<MileageExpense[]>([])
const canApprove = ref(false) // TODO: Get from user permissions

const showExpenseItemModal = ref(false)
const showMileageModal = ref(false)
const showRejectModal = ref(false)
const rejectReason = ref('')

const formData = ref({
  title: '',
  description: ''
})

const loadReport = async () => {
  if (isNew.value) return

  loading.value = true
  error.value = ''
  try {
    report.value = await api.expenseReports.get(reportId.value!)
    formData.value.title = report.value.title
    formData.value.description = report.value.description || ''

    // Load items
    const itemsResponse: any = await api.expenseItems.list(reportId.value!)
    items.value = itemsResponse.data || []

    // Load mileage expenses
    const mileageResponse: any = await api.mileageExpenses.list(reportId.value!)
    mileageExpenses.value = mileageResponse.data || []
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du chargement'
  } finally {
    loading.value = false
  }
}

const saveReport = async () => {
  saving.value = true
  error.value = ''
  try {
    if (isNew.value) {
      const created: any = await api.expenseReports.create(formData.value)
      router.push(`/expense-reports/${created.id}`)
    } else {
      await api.expenseReports.update(reportId.value!, formData.value)
      await loadReport()
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'enregistrement'
  } finally {
    saving.value = false
  }
}

const submitReport = async () => {
  if (!confirm('Êtes-vous sûr de vouloir soumettre ce rapport ?')) return

  submitting.value = true
  error.value = ''
  try {
    await api.expenseReports.submit(reportId.value!)
    router.push('/expense-reports')
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
    await api.expenseReports.approve(reportId.value!)
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de l\'approbation'
  } finally {
    approving.value = false
  }
}

const rejectReport = async () => {
  if (!rejectReason.value.trim()) {
    alert('Veuillez indiquer la raison du rejet')
    return
  }

  rejecting.value = true
  error.value = ''
  try {
    await api.expenseReports.reject(reportId.value!, rejectReason.value)
    showRejectModal.value = false
    rejectReason.value = ''
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du rejet'
  } finally {
    rejecting.value = false
  }
}

const deleteItem = async (itemId: number) => {
  if (!confirm('Supprimer cette dépense ?')) return

  try {
    await api.expenseItems.delete(itemId)
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de la suppression'
  }
}

const deleteMileageExpense = async (mileageId: number) => {
  if (!confirm('Supprimer ce frais kilométrique ?')) return

  try {
    await api.mileageExpenses.delete(mileageId)
    await loadReport()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de la suppression'
  }
}

const addExpenseItem = () => {
  showExpenseItemModal.value = true
}

const addMileageExpense = () => {
  showMileageModal.value = true
}

const handleExpenseItemAdded = () => {
  showExpenseItemModal.value = false
  loadReport()
}

const handleMileageAdded = () => {
  showMileageModal.value = false
  loadReport()
}

const goBack = () => {
  router.push('/expense-reports')
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatAmount = (amount: number): string => {
  return amount.toFixed(3)
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

onMounted(() => {
  if (!isNew.value) {
    loadReport()
  }
})
</script>

<style scoped>
.expense-report-detail {
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.reference {
  color: #6b7280;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
}

.status-draft {
  background-color: #f3f4f6;
  color: #6b7280;
}

.status-submitted {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-approved {
  background-color: #d1fae5;
  color: #065f46;
}

.status-rejected {
  background-color: #fee2e2;
  color: #991b1b;
}

.status-paid {
  background-color: #e0e7ff;
  color: #3730a3;
}

.actions {
  display: flex;
  gap: 0.75rem;
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

.btn-success {
  background-color: #10b981;
  color: white;
}

.btn-success:hover:not(:disabled) {
  background-color: #059669;
}

.btn-danger {
  background-color: #ef4444;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background-color: #dc2626;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading,
.error {
  text-align: center;
  padding: 3rem;
}

.error {
  color: #ef4444;
  background-color: #fee2e2;
  border-radius: 0.5rem;
}

.content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
}

.card h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 1rem 0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.section-header h2 {
  margin: 0;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:disabled {
  background-color: #f3f4f6;
  cursor: not-allowed;
}

.rejection-notice {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #fee2e2;
  border-left: 4px solid #ef4444;
  border-radius: 0.5rem;
  color: #991b1b;
}

.empty-message {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.item-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.category-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
  color: white;
}

.item-amount {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.item-details {
  margin-bottom: 0.75rem;
}

.item-details p {
  margin: 0.25rem 0;
  font-size: 0.875rem;
}

.item-date {
  color: #6b7280;
}

.item-description {
  color: #6b7280;
  font-style: italic;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.75rem;
  border-top: 1px solid #e5e7eb;
  font-size: 0.875rem;
  color: #6b7280;
}

.totals {
  background-color: #f9fafb;
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  font-size: 1rem;
}

.total-final {
  border-top: 2px solid #e5e7eb;
  padding-top: 1rem;
  margin-top: 0.5rem;
  font-size: 1.25rem;
  font-weight: 600;
}

.total-value {
  font-weight: 600;
}

/* Modal styles */
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
  max-width: 500px;
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
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}
</style>
