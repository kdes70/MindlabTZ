// src/services/CategoryService.js
import api from './api';

class CategoryService {
  /**
   * Получение списка категорий
   *
   * @param {Object} params - Параметры запроса
   * @returns {Promise} - Promise с данными
   */
  async getCategories(params = {}) {
    try {
      const response = await api.get('/categories', { params });
      return response.data;
    } catch (error) {
      console.error('Error fetching categories:', error);
      throw error;
    }
  }

  /**
   * Создание новой категории
   *
   * @param {Object} categoryData - Данные категории
   * @returns {Promise} - Promise с данными
   */
  async createCategory(categoryData) {
    try {
      const response = await api.post('/categories', categoryData);
      return response.data;
    } catch (error) {
      console.error('Error creating category:', error);
      throw error;
    }
  }

  /**
   * Обновление категории
   *
   * @param {number} id - Идентификатор категории
   * @param {Object} categoryData - Данные категории
   * @returns {Promise} - Promise с данными
   */
  async updateCategory(id, categoryData) {
    try {
      const response = await api.put(`/categories/${id}`, categoryData);
      return response.data;
    } catch (error) {
      console.error(`Error updating category with id ${id}:`, error);
      throw error;
    }
  }

  /**
   * Удаление категории
   *
   * @param {number} id - Идентификатор категории
   * @returns {Promise} - Promise с данными
   */
  async deleteCategory(id) {
    try {
      const response = await api.delete(`/categories/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting category with id ${id}:`, error);
      throw error;
    }
  }
}

export default new CategoryService();
