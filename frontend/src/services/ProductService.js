import axios from 'axios';
import {API_URL} from '@/config';

class ProductService {
  /**
   * Получение списка товаров с пагинацией и фильтрацией
   *
   * @param {Object} params - Параметры запроса
   * @param {number} params.page - Номер страницы
   * @param {number} params.perPage - Количество записей на странице
   * @param {string} params.sort - Поле для сортировки
   * @param {string} params.direction - Направление сортировки (asc/desc)
   * @param {string} params.search - Поисковый запрос
   * @param {number|string} params.categoryId - Идентификатор категории для фильтрации
   * @returns {Promise} - Promise с данными
   */
  async getProducts(params = {}) {
    try {
      const response = await axios.get(`${API_URL}/api/products`, {params});
      return response.data;
    } catch (error) {
      console.error('Error fetching products:', error);
      throw error;
    }
  }

  /**
   * Получение товара по идентификатору
   *
   * @param {number} id - Идентификатор товара
   * @returns {Promise} - Promise с данными
   */
  async getProduct(id) {
    try {
      const response = await axios.get(`${API_URL}/api/products/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching product with id ${id}:`, error);
      throw error;
    }
  }

  /**
   * Создание нового товара
   *
   * @param {Object} productData - Данные товара
   * @returns {Promise} - Promise с данными
   */
  async createProduct(productData) {
    try {
      const response = await axios.post(`${API_URL}/api/products`, productData);
      return response.data;
    } catch (error) {
      console.error('Error creating product:', error);
      throw error;
    }
  }

  /**
   * Обновление существующего товара
   *
   * @param {number} id - Идентификатор товара
   * @param {Object} productData - Данные товара
   * @returns {Promise} - Promise с данными
   */
  async updateProduct(id, productData) {
    try {
      const response = await axios.put(`${API_URL}/api/products/${id}`, productData);
      return response.data;
    } catch (error) {
      console.error(`Error updating product with id ${id}:`, error);
      throw error;
    }
  }

  /**
   * Удаление товара
   *
   * @param {number} id - Идентификатор товара
   * @returns {Promise} - Promise с данными
   */
  async deleteProduct(id) {
    try {
      const response = await axios.delete(`${API_URL}/api/products/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error deleting product with id ${id}:`, error);
      throw error;
    }
  }

  /**
   * Получение истории цен товара
   *
   * @param {number} id - Идентификатор товара
   * @param {Object} params - Дополнительные параметры запроса
   * @returns {Promise} - Promise с данными
   */
  async getProductPriceHistory(id, params = {}) {
    try {
      const response = await axios.get(`${API_URL}/api/products/${id}/price-history`, {params});
      return response.data;
    } catch (error) {
      console.error(`Error fetching price history for product with id ${id}:`, error);
      throw error;
    }
  }

  /**
   * Массовое создание или обновление товаров
   *
   * @param {Array} productsData - Массив с данными товаров
   * @returns {Promise} - Promise с данными
   */
  async bulkSaveProducts(productsData) {
    try {
      const response = await axios.post(`${API_URL}/api/products/bulk`, productsData);
      return response.data;
    } catch (error) {
      console.error('Error bulk saving products:', error);
      throw error;
    }
  }

  /**
   * Экспорт товаров в CSV
   *
   * @returns {Promise} - Promise с CSV-данными
   */
  async exportProductsToCSV() {
    try {
      const response = await axios.get(`${API_URL}/api/products/export`, {
        responseType: 'blob'
      });
      return response.data;
    } catch (error) {
      console.error('Error exporting products to CSV:', error);
      throw error;
    }
  }

}

export default new ProductService();
