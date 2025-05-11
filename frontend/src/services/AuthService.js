// src/services/AuthService.js
import axios from 'axios';
import {API_URL} from '@/config';

class AuthService {

  async login(credentials) {
    try {
      const response = await axios.post(`${API_URL}/api/login`, credentials);
      return response.data;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  }

  async register(userData) {
    try {
      const response = await axios.post(`${API_URL}/api/register`, userData);
      return response.data;
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  }

  async forgotPassword(email) {
    try {
      const response = await axios.post(`${API_URL}/api/forgot-password`, {email});
      return response.data;
    } catch (error) {
      console.error('Forgot password error:', error);
      throw error;
    }
  }

  async resetPassword(data) {
    try {
      const response = await axios.post(`${API_URL}/api/reset-password`, data);
      return response.data;
    } catch (error) {
      console.error('Reset password error:', error);
      throw error;
    }
  }

  /**
   * Выход из системы
   *
   * @returns {Promise} - Promise с данными
   */
  async logout() {
    try {
      await axios.post(`${API_URL}/logout`, {}, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      });

      localStorage.removeItem('authToken');
      return true;
    } catch (error) {
      console.error('Logout error:', error);
      throw error;
    }
  }
}

export default new AuthService();
