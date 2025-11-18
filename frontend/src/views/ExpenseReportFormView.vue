<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="row mb-4 animate__animated animate__fadeIn">
      <div class="col-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <router-link to="/expense-reports">Rapports de frais</router-link>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Nouveau rapport</li>
          </ol>
        </nav>
        <h1 class="display-6 fw-bold mb-2">
          <i class="bi bi-file-earmark-plus me-3"></i>
          Créer un rapport de frais
        </h1>
        <p class="text-muted">Remplissez les informations ci-dessous pour créer votre rapport</p>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-lg-8">
        <!-- Error Alert -->
        <div v-if="error" class="alert alert-danger animate__animated animate__shakeX mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          {{ error }}
        </div>

        <!-- Main Form Card -->
        <div class="card shadow-lg animate__animated animate__fadeInUp">
          <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
              <i class="bi bi-pencil-square me-2"></i>
              Informations du rapport
            </h5>
          </div>

          <div class="card-body p-4">
            <form @submit.prevent="submit">
              <!-- Title Field -->
              <div class="mb-4">
                <label for="title" class="form-label fw-semibold">
                  <i class="bi bi-pencil me-2"></i>
                  Titre du rapport <span class="text-danger">*</span>
                </label>
                <input
                  id="title"
                  v-model="formData.title"
                  type="text"
                  class="form-control form-control-lg"
                  placeholder="Ex: Frais de mission Janvier 2025"
                  required
                />
                <div class="form-text">
                  <i class="bi bi-info-circle me-1"></i>
                  Un titre clair et descriptif pour identifier facilement votre rapport
                </div>
              </div>

              <!-- Period Fields -->
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label for="periodStart" class="form-label fw-semibold">
                    <i class="bi bi-calendar-event me-2"></i>
                    Début de période <span class="text-danger">*</span>
                  </label>
                  <input
                    id="periodStart"
                    v-model="formData.period_start"
                    type="date"
                    class="form-control"
                    required
                    :max="formData.period_end || today"
                  />
                </div>
                <div class="col-md-6">
                  <label for="periodEnd" class="form-label fw-semibold">
                    <i class="bi bi-calendar-check me-2"></i>
                    Fin de période <span class="text-danger">*</span>
                  </label>
                  <input
                    id="periodEnd"
                    v-model="formData.period_end"
                    type="date"
                    class="form-control"
                    required
                    :min="formData.period_start"
                    :max="today"
                  />
                </div>
              </div>

              <!-- Description Field -->
              <div class="mb-4">
                <label for="description" class="form-label fw-semibold">
                  <i class="bi bi-text-paragraph me-2"></i>
                  Description (facultatif)
                </label>
                <textarea
                  id="description"
                  v-model="formData.description"
                  class="form-control"
                  rows="4"
                  placeholder="Ajoutez des détails sur ce rapport : mission, contexte, remarques..."
                ></textarea>
                <div class="form-text">
                  <i class="bi bi-lightbulb me-1"></i>
                  Les détails aideront votre manager à mieux comprendre vos dépenses
                </div>
              </div>

              <!-- Info Card -->
              <div class="alert alert-info d-flex align-items-start">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div class="flex-grow-1">
                  <h6 class="alert-heading mb-2">À savoir</h6>
                  <ul class="mb-0 small">
                    <li>Vous pourrez ajouter vos dépenses après la création du rapport</li>
                    <li>Le rapport restera en brouillon jusqu'à ce que vous le soumettiez</li>
                    <li>Vous pouvez modifier les informations tant que le rapport n'est pas soumis</li>
                  </ul>
                </div>
              </div>
            </form>
          </div>

          <!-- Card Footer with Actions -->
          <div class="card-footer bg-light border-0 p-4">
            <div class="d-flex justify-content-between gap-3">
              <button
                type="button"
                @click="$router.push('/expense-reports')"
                class="btn btn-outline-secondary px-4"
                :disabled="saving"
              >
                <i class="bi bi-x-circle me-2"></i>
                Annuler
              </button>
              <button
                type="button"
                @click="submit"
                class="btn btn-primary btn-lg px-5"
                :disabled="saving"
              >
                <span v-if="saving">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Création en cours...
                </span>
                <span v-else>
                  <i class="bi bi-check-circle me-2"></i>
                  Créer le rapport
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Tips Card -->
        <div class="card mt-4 border-0 bg-light animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bi bi-lightbulb text-warning me-2"></i>
              Conseils rapides
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                  <div class="small">
                    <strong>Soyez précis</strong>
                    <p class="text-muted mb-0">Un titre clair facilite le suivi de vos rapports</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                  <div class="small">
                    <strong>Regroupez par période</strong>
                    <p class="text-muted mb-0">Créez un rapport par mois ou par mission</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                  <div class="small">
                    <strong>Gardez vos justificatifs</strong>
                    <p class="text-muted mb-0">Vous pourrez les uploader après la création</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                  <div class="small">
                    <strong>Soumettez rapidement</strong>
                    <p class="text-muted mb-0">Plus vous soumettez tôt, plus vite vous serez remboursé</p>
                  </div>
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
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AppLayout from '../components/AppLayout.vue'

const router = useRouter()
const saving = ref(false)
const error = ref('')

const today = computed(() => new Date().toISOString().split('T')[0])

const formData = ref({
  title: '',
  period_start: new Date().toISOString().split('T')[0],
  period_end: new Date().toISOString().split('T')[0],
  description: ''
})

const submit = async () => {
  saving.value = true
  error.value = ''
  try {
    const response: any = await api.expenseReports.create(formData.value)
    // Redirect to the detail page
    router.push(`/expense-reports/${response.id}`)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erreur lors de la création du rapport'
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.card {
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}
</style>
