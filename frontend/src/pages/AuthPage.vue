<!-- src/views/AuthPage.vue -->
<template>
  <div class="auth-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="card mt-5">
            <div class="card-body">
              <h2 class="text-center mb-4">{{ isLoginMode ? 'Вход' : 'Регистрация' }}</h2>

              <form @submit.prevent="handleSubmit">
                <div v-if="!isLoginMode" class="mb-3">
                  <label class="form-label">Имя</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    required
                  >
                </div>

                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    v-model="form.email"
                    required
                  >
                </div>

                <div class="mb-3">
                  <label class="form-label">Пароль</label>
                  <input
                    type="password"
                    class="form-control"
                    v-model="form.password"
                    required
                    minlength="8"
                  >
                </div>

                <div v-if="!isLoginMode" class="mb-3">
                  <label class="form-label">Подтверждение пароля</label>
                  <input
                    type="password"
                    class="form-control"
                    v-model="form.password_confirmation"
                    required
                  >
                </div>

                <div class="d-grid gap-2 mb-3">
                  <button type="submit" class="btn btn-primary">
                    {{ isLoginMode ? 'Войти' : 'Зарегистрироваться' }}
                  </button>
                </div>

                <div class="text-center">
                  <a href="#" @click.prevent="toggleMode">
                    {{ isLoginMode ? 'Нет аккаунта? Зарегистрироваться' : 'Уже есть аккаунт? Войти' }}
                  </a>
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
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '@/composables/useToast';
import AuthService from '@/services/AuthService';

export default {
  setup() {
    const router = useRouter();
    const { showToast } = useToast();

    const isLoginMode = ref(true);
    const form = reactive({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    });

    const toggleMode = () => {
      isLoginMode.value = !isLoginMode.value;
    };

    const handleSubmit = async () => {
      try {
        let response;
        if (isLoginMode.value) {
          response = await AuthService.login({
            email: form.email,
            password: form.password
          });
        } else {
          response = await AuthService.register(form);
        }

        localStorage.setItem('authToken', response.token);
        router.push({ name: 'products' });

      } catch (error) {
        showToast(error.response?.data?.message || 'Ошибка аутентификации', 'error');
      }
    };

    return {
      isLoginMode,
      form,
      toggleMode,
      handleSubmit
    };
  }
};
</script>
