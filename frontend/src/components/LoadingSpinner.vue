<template>
  <div :class="['loading-container', size]">
    <div class="spinner"></div>
    <p v-if="message" class="loading-message">{{ message }}</p>
  </div>
</template>

<script setup lang="ts">
interface Props {
  size?: 'sm' | 'md' | 'lg'
  message?: string
}

withDefaults(defineProps<Props>(), {
  size: 'md',
  message: 'Chargement...'
})
</script>

<style scoped>
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.loading-container.sm {
  padding: 1rem;
}

.loading-container.lg {
  padding: 4rem;
}

.spinner {
  border: 3px solid #e5e7eb;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.loading-container.sm .spinner {
  width: 24px;
  height: 24px;
  border-width: 2px;
}

.loading-container.md .spinner {
  width: 40px;
  height: 40px;
  border-width: 3px;
}

.loading-container.lg .spinner {
  width: 56px;
  height: 56px;
  border-width: 4px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-message {
  margin-top: 1rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.loading-container.sm .loading-message {
  font-size: 0.75rem;
  margin-top: 0.5rem;
}

.loading-container.lg .loading-message {
  font-size: 1rem;
  margin-top: 1.5rem;
}
</style>
