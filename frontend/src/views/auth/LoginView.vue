<template>
  <div class="login-container">
    <div class="container">
      <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5">
          <!-- Login Card with Animation -->
          <div class="card login-card shadow-lg animate__animated animate__fadeIn">
            <!-- Logo/Header Section -->
            <div class="card-header text-center py-4 border-0 bg-transparent">
              <div class="logo-wrapper mb-3">
                <div class="logo-icon bg-gradient-primary text-white rounded-circle mx-auto animate__animated animate__bounceIn">
                  <i class="bi bi-receipt-cutoff display-4"></i>
                </div>
              </div>
              <h2 class="fw-bold text-gradient mb-2">Compteo TN</h2>
              <p class="text-muted mb-0">Gestion des notes de frais</p>
            </div>

            <div class="card-body px-4 px-md-5 pb-5">
              <!-- Error Alert -->
              <div v-if="error" class="alert alert-danger animate__animated animate__shake" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ error }}
              </div>

              <!-- Login Form -->
              <form @submit.prevent="handleLogin" class="login-form">
                <!-- Email Input -->
                <div class="mb-4">
                  <label for="email" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-2"></i>
                    Adresse email
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                      <i class="bi bi-person text-primary"></i>
                    </span>
                    <input
                      id="email"
                      v-model="email"
                      type="email"
                      class="form-control border-start-0 ps-0"
                      placeholder="exemple@compteo.tn"
                      required
                      autocomplete="email"
                    />
                  </div>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                  <label for="password" class="form-label fw-semibold">
                    <i class="bi bi-lock me-2"></i>
                    Mot de passe
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                      <i class="bi bi-key text-primary"></i>
                    </span>
                    <input
                      id="password"
                      v-model="password"
                      :type="showPassword ? 'text' : 'password'"
                      class="form-control border-start-0 border-end-0 ps-0"
                      placeholder="••••••••"
                      required
                      autocomplete="current-password"
                    />
                    <button
                      type="button"
                      class="btn btn-outline-secondary border-start-0"
                      @click="showPassword = !showPassword"
                    >
                      <i :class="['bi', showPassword ? 'bi-eye-slash' : 'bi-eye']"></i>
                    </button>
                  </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe" />
                    <label class="form-check-label small" for="rememberMe">
                      Se souvenir de moi
                    </label>
                  </div>
                  <a href="#" class="small text-decoration-none">
                    Mot de passe oublié?
                  </a>
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  class="btn btn-primary w-100 py-3 fw-semibold"
                  :disabled="loading"
                >
                  <span v-if="loading">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Connexion en cours...
                  </span>
                  <span v-else>
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Se connecter
                  </span>
                </button>
              </form>

              <!-- Demo Accounts Info -->
              <div class="mt-4 p-3 bg-light rounded-3">
                <p class="small fw-semibold mb-2 text-muted">
                  <i class="bi bi-info-circle me-1"></i>
                  Comptes de démonstration :
                </p>
                <div class="demo-accounts small">
                  <div class="mb-1">
                    <span class="badge bg-primary me-2">Employé</span>
                    <code>employee@compteo.tn</code> / <code>password</code>
                  </div>
                  <div class="mb-1">
                    <span class="badge bg-success me-2">Manager</span>
                    <code>manager@compteo.tn</code> / <code>password</code>
                  </div>
                  <div>
                    <span class="badge bg-danger me-2">Admin</span>
                    <code>admin@compteo.tn</code> / <code>password</code>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="card-footer text-center bg-light border-0 py-3">
              <p class="small text-muted mb-0">
                © 2025 Compteo TN. Tous droits réservés.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const response: any = await api.auth.login({
      email: email.value,
      password: password.value
    })

    // Store token
    localStorage.setItem('auth_token', response.token)

    // Redirect to dashboard
    router.push('/')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Email ou mot de passe incorrect'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

.login-container::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  animation: pulse 15s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
}

.login-card {
  border: none;
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.logo-wrapper {
  position: relative;
}

.logo-icon {
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
}

.login-form .input-group {
  transition: all 0.3s ease;
}

.login-form .input-group:focus-within {
  transform: translateY(-2px);
}

.login-form .form-control:focus,
.login-form .input-group-text {
  border-color: var(--primary-color);
}

.input-group-text {
  transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
  background-color: rgba(79, 70, 229, 0.1);
  border-color: var(--primary-color);
}

.demo-accounts code {
  background: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 576px) {
  .logo-icon {
    width: 80px;
    height: 80px;
  }

  .logo-icon i {
    font-size: 2.5rem !important;
  }

  .card-body {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
  }
}
</style>
