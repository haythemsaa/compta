<template>
  <div :class="['loading-container', `loading-${size}`, centered ? 'text-center' : '']">
    <div :class="['spinner-border', `text-${variant}`, sizeClass]" role="status">
      <span class="visually-hidden">{{ message }}</span>
    </div>
    <p v-if="showMessage && message" :class="['loading-message', `text-${variant}`, 'mt-3']">
      {{ message }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  size?: 'sm' | 'md' | 'lg'
  variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info'
  message?: string
  showMessage?: boolean
  centered?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  variant: 'primary',
  message: 'Chargement...',
  showMessage: true,
  centered: true
})

const sizeClass = computed(() => {
  const sizes = {
    sm: '',
    md: 'spinner-lg',
    lg: 'spinner-xl'
  }
  return sizes[props.size]
})
</script>

<style scoped>
.loading-container {
  padding: 2rem;
}

.loading-container.loading-sm {
  padding: 1rem;
}

.loading-container.loading-lg {
  padding: 4rem;
}

.spinner-lg {
  width: 3rem;
  height: 3rem;
  border-width: 0.3rem;
}

.spinner-xl {
  width: 4rem;
  height: 4rem;
  border-width: 0.4rem;
}

.loading-message {
  font-weight: 500;
  margin-bottom: 0;
}
</style>
