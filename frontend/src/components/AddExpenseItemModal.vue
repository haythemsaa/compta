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
            <i class="bi bi-plus-circle me-2"></i>
            Ajouter une dépense
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
            <!-- Category Selection -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-tag me-2"></i>
                Catégorie <span class="text-danger">*</span>
              </label>
              <select
                v-model="formData.expense_category_id"
                class="form-select form-select-lg"
                required
              >
                <option value="">Sélectionnez une catégorie</option>
                <option
                  v-for="cat in categories"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.icon }} {{ cat.name }}
                </option>
              </select>
            </div>

            <!-- Date and Amount Row -->
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
                  <i class="bi bi-cash me-2"></i>
                  Montant TTC (TND) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input
                    v-model.number="formData.amount"
                    type="number"
                    step="0.001"
                    min="0"
                    class="form-control"
                    placeholder="0.000"
                    required
                  />
                  <span class="input-group-text">TND</span>
                </div>
              </div>
            </div>

            <!-- Merchant Name -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-shop me-2"></i>
                Nom du marchand <span class="text-danger">*</span>
              </label>
              <input
                v-model="formData.merchant_name"
                type="text"
                class="form-control"
                placeholder="Ex: Restaurant Le Gourmet"
                required
              />
            </div>

            <!-- Merchant VAT Number -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-building me-2"></i>
                Matricule fiscal (facultatif)
              </label>
              <input
                v-model="formData.merchant_vat_number"
                type="text"
                class="form-control"
                placeholder="1234567/A/M/000"
              />
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Format tunisien : XXXXXXX/A/M/000
              </div>
            </div>

            <!-- TVA Rate -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-percent me-2"></i>
                Taux TVA
              </label>
              <div class="btn-group w-100" role="group">
                <input
                  type="radio"
                  class="btn-check"
                  id="tva19"
                  v-model.number="formData.tva_rate"
                  :value="19"
                />
                <label class="btn btn-outline-primary" for="tva19">
                  19% <small class="d-block text-muted" style="font-size: 0.7rem">Standard</small>
                </label>

                <input
                  type="radio"
                  class="btn-check"
                  id="tva13"
                  v-model.number="formData.tva_rate"
                  :value="13"
                />
                <label class="btn btn-outline-primary" for="tva13">
                  13% <small class="d-block text-muted" style="font-size: 0.7rem">Réduit</small>
                </label>

                <input
                  type="radio"
                  class="btn-check"
                  id="tva7"
                  v-model.number="formData.tva_rate"
                  :value="7"
                />
                <label class="btn btn-outline-primary" for="tva7">
                  7% <small class="d-block text-muted" style="font-size: 0.7rem">S-réduit</small>
                </label>

                <input
                  type="radio"
                  class="btn-check"
                  id="tva0"
                  v-model.number="formData.tva_rate"
                  :value="0"
                />
                <label class="btn btn-outline-primary" for="tva0">
                  0% <small class="d-block text-muted" style="font-size: 0.7rem">Exonéré</small>
                </label>
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
                placeholder="Détails de la dépense..."
              ></textarea>
            </div>

            <!-- Guest Count (for Restaurant category) -->
            <div v-if="showGuestFields" class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-people me-2"></i>
                Nombre de convives
              </label>
              <input
                v-model.number="formData.guest_count"
                type="number"
                min="0"
                class="form-control"
                placeholder="Ex: 4"
              />
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Nombre de personnes présentes au repas
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
              Ajouter la dépense
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
import type { ExpenseCategory } from '../types'

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
const error = ref('')
const categories = ref<ExpenseCategory[]>([])

const today = computed(() => new Date().toISOString().split('T')[0])

const formData = ref({
  expense_category_id: '',
  date: today.value,
  merchant_name: '',
  merchant_vat_number: '',
  amount: 0,
  tva_rate: 19,
  description: '',
  guest_count: 0
})

const showGuestFields = computed(() => {
  const category = categories.value.find(c => c.id === Number(formData.value.expense_category_id))
  return category?.code === 'RESTAURANT'
})

const loadCategories = async () => {
  try {
    const response: any = await api.expenseCategories.list()
    categories.value = response.data || []
  } catch (err) {
    console.error('Error loading categories:', err)
  }
}

const submit = async () => {
  saving.value = true
  error.value = ''
  try {
    await api.expenseItems.create(props.reportId, formData.value)
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
    expense_category_id: '',
    date: today.value,
    merchant_name: '',
    merchant_vat_number: '',
    amount: 0,
    tva_rate: 19,
    description: '',
    guest_count: 0
  }
  error.value = ''
}

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    loadCategories()
  }
})
</script>

<style scoped>
.modal {
  display: block;
}

.btn-check:checked + .btn-outline-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
}

.form-control:focus,
.form-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}

.input-group-text {
  background-color: var(--light-gray);
  font-weight: 600;
}

/* Animation */
.animate__faster {
  animation-duration: 0.4s !important;
}
</style>
