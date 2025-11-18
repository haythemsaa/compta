<template>
  <div class="empty-state text-center py-5 animate__animated animate__fadeIn">
    <div class="empty-icon mb-4">
      <i :class="['bi', icon, iconColorClass]"></i>
    </div>
    <h4 class="mb-3 fw-bold">{{ title }}</h4>
    <p class="text-muted mb-4">{{ description }}</p>
    <slot name="action">
      <button
        v-if="actionText"
        @click="$emit('action')"
        :class="['btn', actionVariant, 'btn-lg']"
      >
        <i v-if="actionIcon" :class="['bi', actionIcon, 'me-2']"></i>
        {{ actionText }}
      </button>
    </slot>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  icon?: string
  iconColor?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'muted'
  title: string
  description: string
  actionText?: string
  actionIcon?: string
  actionVariant?: string
}

interface Emits {
  (e: 'action'): void
}

const props = withDefaults(defineProps<Props>(), {
  icon: 'bi-inbox',
  iconColor: 'muted',
  actionVariant: 'btn-primary'
})

defineEmits<Emits>()

const iconColorClass = computed(() => {
  return `text-${props.iconColor}`
})
</script>

<style scoped>
.empty-state {
  max-width: 600px;
  margin: 0 auto;
  padding: 3rem 2rem;
}

.empty-icon i {
  font-size: 5rem;
  opacity: 0.6;
}

.empty-state h4 {
  color: var(--dark);
}

.empty-state p {
  font-size: 1.1rem;
}

@media (max-width: 768px) {
  .empty-icon i {
    font-size: 4rem;
  }

  .empty-state h4 {
    font-size: 1.25rem;
  }

  .empty-state p {
    font-size: 1rem;
  }
}
</style>
