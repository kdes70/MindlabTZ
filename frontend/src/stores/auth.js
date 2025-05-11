// src/stores/auth.js
import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const isAuthenticated = ref(false);

  const setUser = (userData) => {
    user.value = userData;
    isAuthenticated.value = true;
  };

  const logout = () => {
    user.value = null;
    isAuthenticated.value = false;
    localStorage.removeItem('authToken');
  };

  return {
    user,
    isAuthenticated,
    setUser,
    logout
  };
});
