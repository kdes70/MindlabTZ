<template>
  <div class="login-container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card p-4" style="width: 20rem;">
      <h3 class="text-center mb-4">Вход</h3>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" v-model="credentials.email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Пароль</label>
          <input type="password" class="form-control" id="password" v-model="credentials.password" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Войти</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '@/composables/useToast';
import AuthService from '@/services/AuthService';

export default {
  name: 'LoginPage',
  setup() {
    const { showToast } = useToast();
    const router = useRouter();

    const credentials = ref({
      email: '',
      password: ''
    });

    const login = async () => {
      try {
        const response = await AuthService.login(credentials.value);
        localStorage.setItem('authToken', response.token);
        showToast('Успешный вход', 'success');
        router.push({ name: 'Products' });
      } catch (error) {
        showToast('Ошибка входа. Проверьте логин и пароль', 'error');
        console.error('Login error:', error);
      }
    };

    return {
      credentials,
      login
    };
  }
};
</script>
