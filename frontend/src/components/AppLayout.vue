<template>
  <div class="app-wrapper">
    <!-- Modern Navbar with Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-modern fixed-top animate__animated animate__fadeInDown">
      <div class="container-fluid px-4">
        <!-- Logo/Brand -->
        <router-link to="/" class="navbar-brand d-flex align-items-center">
          <div class="logo-icon bg-gradient-primary text-white rounded-3 p-2 me-2">
            <i class="bi bi-receipt-cutoff fs-4"></i>
          </div>
          <span class="fw-bold text-gradient">Compteo TN</span>
        </router-link>

        <!-- Mobile Toggle -->
        <button
          class="navbar-toggler border-0"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarContent"
        >
          <i class="bi bi-list fs-3"></i>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarContent">
          <!-- Main Navigation -->
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
            <li class="nav-item">
              <router-link to="/" class="nav-link" active-class="active">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/expense-reports" class="nav-link" active-class="active">
                <i class="bi bi-file-earmark-text me-2"></i>
                Rapports de frais
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/vehicles" class="nav-link" active-class="active">
                <i class="bi bi-car-front me-2"></i>
                Véhicules
              </router-link>
            </li>
          </ul>

          <!-- Right Side Actions -->
          <div class="d-flex align-items-center">
            <!-- New Report Button -->
            <router-link to="/expense-reports/new" class="btn btn-primary me-3 d-none d-md-block">
              <i class="bi bi-plus-circle me-2"></i>
              Nouveau rapport
            </router-link>

            <!-- Notifications (future) -->
            <div class="position-relative me-3">
              <button class="btn btn-link text-dark position-relative">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  3
                </span>
              </button>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown" ref="userDropdownRef">
              <button
                class="btn btn-link text-dark d-flex align-items-center dropdown-toggle"
                @click="toggleUserMenu"
              >
                <!-- User Avatar -->
                <div class="user-avatar bg-gradient-primary text-white rounded-circle me-2">
                  {{ userInitials }}
                </div>
                <div class="d-none d-md-block text-start me-2">
                  <div class="fw-semibold small">{{ user?.name }}</div>
                  <div class="text-muted" style="font-size: 0.75rem">{{ roleLabel }}</div>
                </div>
              </button>

              <!-- Dropdown Menu -->
              <div
                v-show="showUserMenu"
                class="dropdown-menu dropdown-menu-end show animate__animated animate__fadeIn animate__faster"
                style="min-width: 250px"
              >
                <div class="px-4 py-3 border-bottom">
                  <div class="fw-semibold">{{ user?.name }}</div>
                  <div class="text-muted small">{{ user?.email }}</div>
                  <span class="badge bg-gradient-primary mt-2">{{ roleLabel }}</span>
                </div>
                <a href="#" class="dropdown-item py-2">
                  <i class="bi bi-person me-2"></i>
                  Mon profil
                </a>
                <a href="#" class="dropdown-item py-2">
                  <i class="bi bi-gear me-2"></i>
                  Paramètres
                </a>
                <div class="dropdown-divider"></div>
                <button @click="handleLogout" class="dropdown-item py-2 text-danger">
                  <i class="bi bi-box-arrow-right me-2"></i>
                  Déconnexion
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
      <div class="container-fluid px-4">
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer bg-white border-top mt-auto py-4">
      <div class="container-fluid px-4">
        <div class="row align-items-center">
          <div class="col-md-6 text-center text-md-start">
            <span class="text-muted">
              © 2025 <strong>Compteo TN</strong>. Tous droits réservés.
            </span>
          </div>
          <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
            <a href="#" class="text-muted text-decoration-none me-3">
              <i class="bi bi-file-text me-1"></i>
              Documentation
            </a>
            <a href="#" class="text-muted text-decoration-none me-3">
              <i class="bi bi-question-circle me-1"></i>
              Support
            </a>
            <a href="#" class="text-muted text-decoration-none">
              <i class="bi bi-info-circle me-1"></i>
              À propos
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const showUserMenu = ref(false)
const userDropdownRef = ref<HTMLElement | null>(null)

const user = computed(() => authStore.user)

const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  const names = user.value.name.split(' ')
  if (names.length >= 2) {
    return \`\${names[0][0]}\${names[1][0]}\`.toUpperCase()
  }
  return user.value.name.substring(0, 2).toUpperCase()
})

const roleLabel = computed(() => {
  const roleLabels: Record<string, string> = {
    admin: 'Administrateur',
    manager: 'Manager',
    employee: 'Employé',
    accountant: 'Comptable',
    daf: 'DAF'
  }
  return roleLabels[user.value?.role || 'employee'] || 'Utilisateur'
})

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleClickOutside = (event: MouseEvent) => {
  if (
    userDropdownRef.value &&
    !userDropdownRef.value.contains(event.target as Node)
  ) {
    showUserMenu.value = false
  }
}

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  authStore.clearUser()
  router.push('/login')
}

// Navbar scroll effect
let lastScrollTop = 0
const handleScroll = () => {
  const navbar = document.querySelector('.navbar-modern')
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop

  if (navbar) {
    if (scrollTop > lastScrollTop && scrollTop > 100) {
      navbar.classList.add('navbar-hidden')
    } else {
      navbar.classList.remove('navbar-hidden')
    }

    if (scrollTop > 50) {
      navbar.classList.add('scrolled')
    } else {
      navbar.classList.remove('scrolled')
    }
  }

  lastScrollTop = scrollTop
}

onMounted(() => {
  authStore.loadUser()
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.app-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Navbar Styles */
.navbar-modern {
  background: white !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 0.75rem 0;
}

.navbar-modern.scrolled {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 0.5rem 0;
}

.navbar-modern.navbar-hidden {
  transform: translateY(-100%);
}

.logo-icon {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.logo-icon:hover {
  transform: rotate(360deg);
}

.navbar-brand {
  font-size: 1.5rem;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.3s ease;
}

.navbar-brand:hover .logo-icon {
  box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
}

/* User Avatar */
.user-avatar {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.dropdown-toggle::after {
  display: none;
}

.dropdown-menu {
  border: none;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  border-radius: 12px;
  margin-top: 0.5rem;
}

.dropdown-item {
  transition: all 0.3s ease;
  border-radius: 6px;
  margin: 0.25rem 0.5rem;
}

.dropdown-item:hover {
  background: rgba(79, 70, 229, 0.1);
  color: var(--primary-color);
  transform: translateX(5px);
}

/* Main Content */
.main-content {
  flex: 1;
  padding-top: 90px;
  padding-bottom: 2rem;
  min-height: calc(100vh - 180px);
}

/* Footer */
.footer {
  margin-top: auto;
}

.footer a:hover {
  color: var(--primary-color) !important;
}

/* Responsive */
@media (max-width: 768px) {
  .main-content {
    padding-top: 80px;
  }

  .navbar-nav {
    padding: 1rem 0;
  }

  .nav-link {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin: 0.25rem 0;
  }
}

/* Animations */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate__fadeIn {
  animation-duration: 0.3s !important;
}
</style>
