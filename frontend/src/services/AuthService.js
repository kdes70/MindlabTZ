// src/services/AuthService.js
import axios from 'axios';
import {API_URL} from '@/config';

const api = axios.create({
  baseURL: '/api', // Используем прокси Vite
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
});


class AuthService {

  async login(credentials) {
    try {
      const response = await api.post(`${API_URL}/api/login`, credentials);
      return response.data;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  }

  async register(userData) {
    try {
      const response = await api.post(`${API_URL}/api/register`, userData);
      return response.data;
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  }

  async forgotPassword(email) {
    try {
      const response = await api.post(`${API_URL}/api/forgot-password`, {email});
      return response.data;
    } catch (error) {
      console.error('Forgot password error:', error);
      throw error;
    }
  }

  async resetPassword(data) {
    try {
      const response = await api.post(`${API_URL}/api/reset-password`, data);
      return response.data;
    } catch (error) {
      console.error('Reset password error:', error);
      throw error;
    }
  }

  async logout() {
    try {
      await api.post(`${API_URL}/logout`, {}, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      });
      return true;
    } catch (error) {
      console.error('Logout error:', error);
      throw error;
    }
  }

  async getCurrentUser() {
    const token = localStorage.getItem('authToken');
    if (token) {
      const response = await api.get(`${API_URL}/me`, {}, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      });

      return response.data;
    }
    return null;
  }
}

export default new AuthService();
