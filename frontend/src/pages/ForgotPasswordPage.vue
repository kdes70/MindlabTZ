<!-- src/views/ForgotPasswordPage.vue -->
<template>
  <div class="forgot-password-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="card mt-5">
            <div class="card-body">
              <h2 class="text-center mb-4">Восстановление пароля</h2>

              <form @submit.prevent="handleSubmit">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    v-model="email"
                    required
                  >
                </div>

                <div class="d-grid gap-2 mb-3">
                  <button type="submit" class="btn btn-primary">
                    Отправить ссылку для сброса
                  </button>
                </div>

                <div class="text-center">
                  <router-link :to="{ name: 'login' }">Назад к входу</router-link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';
import AuthService from '@/services/AuthService';

export default {
  setup() {
    const { showToast } = useToast();
    const email = ref('');

    const handleSubmit = async () => {
      try {
        await AuthService.forgotPassword(email.value);
        showToast('Ссылка для сброса пароля отправлена на ваш email', 'success');
      } catch (error) {
        showToast(error.response?.data?.message || 'Ошибка отправки', 'error');
      }
    };

    return {
      email,
      handleSubmit
    };
  }
};
</script>
