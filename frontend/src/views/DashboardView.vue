<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="text-center py-12">Chargement...</div>

      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="card">
            <h3 class="text-sm text-gray-500 mb-1">Brouillons</h3>
            <p class="text-3xl font-bold text-gray-600">{{ stats.draft }}</p>
          </div>
          <div class="card">
            <h3 class="text-sm text-gray-500 mb-1">Soumis</h3>
            <p class="text-3xl font-bold text-blue-600">{{ stats.submitted }}</p>
          </div>
          <div class="card">
            <h3 class="text-sm text-gray-500 mb-1">Approuvés</h3>
            <p class="text-3xl font-bold text-green-600">{{ stats.approved }}</p>
          </div>
          <div class="card">
            <h3 class="text-sm text-gray-500 mb-1">Total mois</h3>
            <p class="text-3xl font-bold text-gray-900">{{ formatAmount(stats.total_month) }} TND</p>
          </div>
        </div>

        <div class="card mb-8">
          <h2 class="text-xl font-semibold mb-4">Derniers rapports</h2>
          <div v-if="recentReports.length === 0" class="text-center py-12 text-gray-500">
            <p>Aucun rapport de frais pour le moment</p>
            <router-link to="/expense-reports/new" class="text-primary-600 hover:underline mt-2 inline-block">
              Créer votre premier rapport
            </router-link>
          </div>
          <div v-else class="space-y-3">
            <router-link
              v-for="report in recentReports"
              :key="report.id"
              :to="`/expense-reports/${report.id}`"
              class="block p-4 border border-gray-200 rounded-lg hover:border-primary-400 hover:bg-gray-50 transition"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium text-gray-900">{{ report.title }}</h3>
                  <p class="text-sm text-gray-500">{{ report.reference }} • {{ formatDate(report.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-gray-900">{{ formatAmount(report.total_amount) }} TND</p>
                  <span :class="['text-xs px-2 py-1 rounded-full', getStatusClass(report.status)]">
                    {{ getStatusLabel(report.status) }}
                  </span>
                </div>
              </div>
            </router-link>
          </div>
        </div>

        <div v-if="breakdown.length > 0" class="card">
          <h2 class="text-xl font-semibold mb-4">Répartition par catégorie</h2>
          <div class="space-y-2">
            <div
              v-for="cat in breakdown"
              :key="cat.name"
              class="flex justify-between items-center p-3 border border-gray-200 rounded"
            >
              <div class="flex items-center gap-2">
                <span class="text-2xl">{{ cat.icon }}</span>
                <span class="font-medium">{{ cat.name }}</span>
              </div>
              <div class="text-right">
                <p class="font-semibold">{{ formatAmount(cat.total) }} TND</p>
                <p class="text-sm text-gray-500">{{ cat.count }} dépenses</p>
              </div>
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
import type { DashboardStats, ExpenseReport, CategoryBreakdown } from '../types'
import AppLayout from '../components/AppLayout.vue'

const loading = ref(true)

const stats = ref<DashboardStats>({
  draft: 0,
  submitted: 0,
  approved: 0,
  rejected: 0,
  paid: 0,
  total_month: 0,
  pending_approval: 0
})

const recentReports = ref<ExpenseReport[]>([])
const breakdown = ref<CategoryBreakdown[]>([])

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
    year: 'numeric',
    month: 'short',
    day: 'numeric'
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
    draft: 'bg-gray-100 text-gray-700',
    submitted: 'bg-blue-100 text-blue-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
    paid: 'bg-purple-100 text-purple-700'
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

onMounted(() => {
  loadDashboard()
})
</script>
