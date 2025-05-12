<template>
  <div id="app">
    <Toast />

    <nav v-if="isAuthenticated" class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Микроэлектроника</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <router-link class="nav-link" to="/products">Товары</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/sales">Продажи</router-link>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <button @click="logout" class="btn btn-outline-light btn-sm">Выход</button>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <router-view />
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '@/composables/useToast';
import AuthService from '@/services/AuthService';
import Toast from '@/components/Toast.vue';

export default {
  name: 'App',
  components: {
    Toast
  },
  setup() {
    const { showToast } = useToast();
    const router = useRouter();
    const isAuthenticated = ref(false);
    const user = ref(null);

    // Проверка аутентификации при загрузке приложения
    const checkAuth = async () => {
      try {
        // Проверяем наличие токена в localStorage
        const token = localStorage.getItem('authToken');

        if (!token) {
          isAuthenticated.value = false;
          user.value = null;
          return;
        }

        // Получаем информацию о пользователе
        const userData = await AuthService.getCurrentUser();
        isAuthenticated.value = true;
        user.value = userData;

        // Если пользователь не на странице входа, оставляем его на текущей странице
        if (router.currentRoute.value.path === '/login') {
          router.push({ name: 'Products' });
        }
      } catch (error) {
        console.error('Authentication check failed:', error);
        isAuthenticated.value = false;
        user.value = null;
        localStorage.removeItem('authToken');

        // Перенаправляем на страницу входа, если не на ней
        if (router.currentRoute.value.path !== '/login') {
          router.push({ name: 'Login' });
        }
      }
    };

    // Выход из системы
    const logout = async () => {
      try {
        await AuthService.logout();
        isAuthenticated.value = false;
        user.value = null;
        localStorage.removeItem('authToken');
        router.push({ name: 'Login' });
        showToast('Вы успешно вышли из системы', 'success');
      } catch (error) {
        console.error('Logout error:', error);
        showToast('Ошибка при выходе из системы', 'error');
      }
    };

    // Инициализация проверки аутентификации при монтировании компонента
    onMounted(() => {
      checkAuth();
    });

    return {
      isAuthenticated,
      user,
      logout
    };
  }
};
</script>

<style>
/* Дополнительные стили для приложения */
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.container-fluid {
  flex: 1;
  padding: 20px;
}

/* Стили для навигационной панели */
.navbar {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
  font-weight: bold;
}

/* Стили для мобильного меню */
@media (max-width: 991.98px) {
  .navbar-nav {
    margin-top: 10px;
  }
}

/* Стили для контейнера */
.container-fluid {
  max-width: 100%;
  padding: 20px;
}

/* Общие стили для карточек */
.card {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  border: none;
  border-radius: 0.5rem;
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}
</style>
