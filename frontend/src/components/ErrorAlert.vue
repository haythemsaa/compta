<template>
  <div
    v-if="modelValue"
    :class="['alert', alertClass, 'd-flex', 'align-items-start', dismissible ? 'alert-dismissible' : '', 'animate__animated', 'animate__fadeIn']"
    role="alert"
  >
    <i :class="['bi', iconClass, 'me-2', 'flex-shrink-0']" style="font-size: 1.25rem"></i>
    <div class="flex-grow-1">
      <slot>{{ modelValue }}</slot>
    </div>
    <button
      v-if="dismissible"
      type="button"
      class="btn-close"
      @click="$emit('update:modelValue', '')"
      aria-label="Close"
    ></button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  modelValue: string
  type?: 'error' | 'warning' | 'success' | 'info'
  dismissible?: boolean
}

interface Emits {
  (e: 'update:modelValue', value: string): void
}

const props = withDefaults(defineProps<Props>(), {
  type: 'error',
  dismissible: true
})

defineEmits<Emits>()

const alertClass = computed(() => {
  const classes = {
    error: 'alert-danger',
    warning: 'alert-warning',
    success: 'alert-success',
    info: 'alert-info'
  }
  return classes[props.type]
})

const iconClass = computed(() => {
  const icons = {
    error: 'bi-exclamation-triangle-fill',
    warning: 'bi-exclamation-circle-fill',
    success: 'bi-check-circle-fill',
    info: 'bi-info-circle-fill'
  }
  return icons[props.type]
})
</script>

<style scoped>
.alert {
  border-left-width: 4px;
  border-radius: 0.5rem;
}
</style>
