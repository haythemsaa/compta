<template>
  <AppLayout>
    <div class="expense-reports-view">
      <div class="header">
        <h1>Rapports de frais</h1>
        <button @click="createReport" class="btn btn-primary">
          Nouveau rapport
        </button>
      </div>

    <div class="filters">
      <select v-model="statusFilter" @change="loadReports" class="filter-select">
        <option value="">Tous les statuts</option>
        <option value="draft">Brouillon</option>
        <option value="submitted">Soumis</option>
        <option value="approved">Approuvé</option>
        <option value="rejected">Rejeté</option>
        <option value="paid">Payé</option>
      </select>
    </div>

    <div v-if="loading" class="loading">
      Chargement...
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
    </div>

    <div v-else class="reports-list">
      <div
        v-for="report in reports"
        :key="report.id"
        class="report-card"
        @click="viewReport(report.id)"
      >
        <div class="report-header">
          <h3>{{ report.title }}</h3>
          <span :class="['status-badge', `status-${report.status}`]">
            {{ getStatusLabel(report.status) }}
          </span>
        </div>
        <div class="report-info">
          <p class="reference">{{ report.reference }}</p>
          <p class="date">{{ formatDate(report.created_at) }}</p>
        </div>
        <div class="report-amount">
          <span class="amount">{{ formatAmount(report.total_amount) }} TND</span>
          <span class="vat">TVA: {{ formatAmount(report.total_tva) }} TND</span>
        </div>
        <div v-if="report.description" class="report-description">
          {{ report.description }}
        </div>
      </div>

      <div v-if="reports.length === 0" class="empty-state">
        <p>Aucun rapport de frais trouvé</p>
        <button @click="createReport" class="btn btn-primary">
          Créer votre premier rapport
        </button>
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

const loadReports = async () => {
  loading.value = true
  error.value = ''
  try {
    const params: any = {}
    if (statusFilter.value) {
      params.status = statusFilter.value
    }
    const response: any = await api.expenseReports.list(params)
    reports.value = response.data || []
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors du chargement des rapports'
  } finally {
    loading.value = false
  }
}

const createReport = () => {
  router.push('/expense-reports/new')
}

const viewReport = (id: number) => {
  router.push(`/expense-reports/${id}`)
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

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatAmount = (amount: number): string => {
  return amount.toFixed(3)
}

onMounted(() => {
  loadReports()
})
</script>

<style scoped>
.expense-reports-view {
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

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.filters {
  margin-bottom: 1.5rem;
}

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.loading,
.error {
  text-align: center;
  padding: 3rem;
  font-size: 1.125rem;
}

.error {
  color: #ef4444;
}

.reports-list {
  display: grid;
  gap: 1rem;
}

.report-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.report-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border-color: #3b82f6;
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1rem;
}

.report-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
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

.report-info {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.report-amount {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.75rem;
  border-top: 1px solid #e5e7eb;
}

.amount {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.vat {
  font-size: 0.875rem;
  color: #6b7280;
}

.report-description {
  margin-top: 0.75rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-state p {
  color: #6b7280;
  font-size: 1.125rem;
  margin-bottom: 1.5rem;
}
</style>
