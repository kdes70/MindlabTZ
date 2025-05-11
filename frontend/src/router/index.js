// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import ProductsPage from '@/pages/ProductsPage.vue';
import AuthPage from '@/pages/AuthPage.vue';
import ForgotPasswordPage from '@/pages/ForgotPasswordPage.vue';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/products'
    },
    {
      path: '/login',
      name: 'login',
      component: AuthPage,
      meta: { requiresGuest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: AuthPage,
      meta: { requiresGuest: true }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPasswordPage,
      meta: { requiresGuest: true }
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsPage,
      meta: { requiresAuth: true }
    }
  ]
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'products' });
  } else {
    next();
  }
});

export default router;
