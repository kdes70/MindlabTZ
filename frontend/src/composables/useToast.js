import {ref} from 'vue';

export function useToast() {
  const toasts = ref([]);

  const showToast = (message, type = 'success', duration = 3000) => {
    const id = Date.now();
    const toast = {id, message, type};

    toasts.value.push(toast);

    setTimeout(() => {
      toasts.value = toasts.value.filter(t => t.id !== id);
    }, duration);
  };

  return {
    toasts,
    showToast
  };
}
