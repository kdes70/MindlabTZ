// src/services/AuthService.js
import api from './api';

class AuthService {

  async login(credentials) {
    try {
      const response = await api.post('/login', credentials);
      return response.data;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  }

  async register(userData) {
    try {
      const response = await api.post('/register', userData);
      return response.data;
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  }

  async forgotPassword(email) {
    try {
      const response = await api.post('/forgot-password', { email });
      return response.data;
    } catch (error) {
      console.error('Forgot password error:', error);
      throw error;
    }
  }

  async resetPassword(data) {
    try {
      const response = await api.post('/reset-password', data);
      return response.data;
    } catch (error) {
      console.error('Reset password error:', error);
      throw error;
    }
  }

  async logout() {
    try {
      await api.post('/logout');
      return true;
    } catch (error) {
      console.error('Logout error:', error);
      throw error;
    }
  }

  async getCurrentUser() {
    try {
      const response = await api.get('/me');
      return response.data;
    } catch (error) {
      console.error('Get current user error:', error);
      return null;
    }
  }
}

export default new AuthService();
