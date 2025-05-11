<template>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div v-for="toast in toasts" :key="toast.id" class="toast show" :class="`bg-${toast.type}`">
      <div class="toast-header">
        <strong class="me-auto">{{ toast.type.charAt(0).toUpperCase() + toast.type.slice(1) }}</strong>
        <button type="button" class="btn-close" @click="removeToast(toast.id)"></button>
      </div>
      <div class="toast-body text-white">
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from '@/composables/useToast';

export default {
  name: 'Toast',
  setup() {
    const { toasts, showToast } = useToast();

    const removeToast = (id) => {
      toasts.value = toasts.value.filter(toast => toast.id !== id);
    };

    return {
      toasts,
      removeToast
    };
  }
};
</script>

<style scoped>
.toast-container {
  z-index: 9999;
}

.bg-success {
  background-color: #198754 !important;
}

.bg-error {
  background-color: #dc3545 !important;
}

.bg-warning {
  background-color: #ffc107 !important;
}

.text-white {
  color: #ffffff !important;
}
</style>
