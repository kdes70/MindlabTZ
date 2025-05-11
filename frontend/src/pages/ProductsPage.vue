<template>
  <div class="products-page">
    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col">
          <h1>Управление товарами микроэлектроники</h1>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="filter-group d-flex align-items-center">
                  <label for="categoryFilter" class="me-2">Категория:</label>
                  <select
                    id="categoryFilter"
                    class="form-select form-select-sm me-3"
                    v-model="filters.categoryId"
                    @change="loadProducts"
                  >
                    <option value="">Все категории</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                  <label for="search" class="me-2">Поиск:</label>
                  <div class="input-group input-group-sm" style="width: 250px;">
                    <input
                      type="text"
                      class="form-control"
                      id="search"
                      v-model="filters.search"
                      placeholder="Поиск по наименованию или артикулу"
                    >
                    <button class="btn btn-primary" type="button" @click="loadProducts">Найти</button>
                  </div>
                </div>
                <div class="sort-group d-flex align-items-center">
                  <label for="sortField" class="me-2">Сортировка:</label>
                  <select id="sortField" class="form-select form-select-sm me-2" v-model="sort.field">
                    <option value="name">По названию</option>
                    <option value="price">По цене</option>
                    <option value="created_at">По дате создания</option>
                  </select>
                  <select class="form-select form-select-sm" v-model="sort.direction">
                    <option value="asc">По возрастанию</option>
                    <option value="desc">По убыванию</option>
                  </select>
                  <button class="btn btn-sm btn-outline-secondary ms-2" @click="applySorting">
                    Применить
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-outline-success me-2" @click="exportToCSV">
                  Экспорт CSV
                </button>
                <button class="btn btn-sm btn-primary" @click="toggleImportModal">
                  Импорт CSV
                </button>
              </div>
              <UniversalTable
                :headers="tableHeaders"
                :data="products"
                :pagination="pagination"
                @save-row="saveProduct"
                @delete-row="deleteProduct"
                @add-row="addProduct"
                @save-changes="bulkSaveChanges"
                @page-change="changePage"
                title="Товары микроэлектроники"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal для импорта CSV -->
    <div v-if="showImportModal" class="modal fade show" style="display: block;" @click.self="toggleImportModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Импорт товаров из CSV</h5>
            <button type="button" class="btn-close" @click="toggleImportModal"></button>
          </div>
          <div class="modal-body">
            <input type="file" class="form-control" accept=".csv" @change="handleCSVUpload" />
            <div class="mt-3 text-muted">
              <p>Формат CSV должен содержать следующие колонки:</p>
              <ul>
                <li>ID (необязательно для новых записей)</li>
                <li>Наименование</li>
                <li>Артикул</li>
                <li>ID категории</li>
                <li>Цена</li>
                <li>Количество на складе</li>
              </ul>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="toggleImportModal">Отмена</button>
            <button class="btn btn-primary" @click="importCSV">Импортировать</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import UniversalTable from '@/components/UniversalTable/UniversalTable.vue';
import { useToast } from '@/composables/useToast';
import ProductService from '@/services/ProductService';
import CategoryService from '@/services/CategoryService';

export default {
  name: 'ProductsPage',
  components: {
    UniversalTable
  },
  setup() {
    const { showToast } = useToast();

    // Данные для таблицы
    const products = ref([]);
    const categories = ref([]);

    // Заголовки таблицы
    const tableHeaders = ref([
      { key: 'id', label: 'ID', readonly: true, type: 'number' },
      { key: 'name', label: 'Наименование', readonly: false, type: 'input' },
      { key: 'article', label: 'Артикул', readonly: false, type: 'input' },
      { key: 'category_id', label: 'Категория', readonly: false, type: 'select', options: [] },
      { key: 'price', label: 'Цена', readonly: false, type: 'number' },
      { key: 'stock', label: 'Количество на складе', readonly: false, type: 'number' },
      { key: 'created_at', label: 'Дата создания', readonly: true, type: 'date' },
      { key: 'updated_at', label: 'Дата обновления', readonly: true, type: 'date' }
    ]);

    // Фильтры и сортировка
    const filters = reactive({
      search: '',
      categoryId: ''
    });

    const sort = reactive({
      field: 'name',
      direction: 'asc'
    });

    // Пагинация
    const pagination = ref({
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      total: 0,
      from: 0,
      to: 0
    });

    // Состояние импорта CSV
    const showImportModal = ref(false);
    const csvFile = ref(null);
    const importedData = ref([]);

    // Загрузка товаров
    const loadProducts = async () => {
      try {
        const params = {
          page: pagination.value.currentPage,
          perPage: pagination.value.perPage,
          sort: sort.field,
          direction: sort.direction,
          search: filters.search,
          categoryId: filters.categoryId
        };

        const response = await ProductService.getProducts(params);
        products.value = response.data;

        // Обновление пагинации
        pagination.value = {
          currentPage: response.meta.current_page,
          lastPage: response.meta.last_page,
          perPage: response.meta.per_page,
          total: response.meta.total,
          from: response.meta.from,
          to: response.meta.to
        };
      } catch (error) {
        showToast('Ошибка загрузки товаров', 'error');
        console.error('Error loading products:', error);
      }
    };

    // Сохранение товара
    const saveProduct = async (product) => {
      try {
        let response;
        if (product.id) {
          // Обновление существующего товара
          response = await ProductService.updateProduct(product.id, product);
          showToast('Товар успешно обновлен', 'success');
        } else {
          // Создание нового товара
          response = await ProductService.createProduct(product);
          showToast('Товар успешно создан', 'success');
        }

        // Обновляем данные в таблице
        await loadProducts();
        return response.data;
      } catch (error) {
        showToast('Ошибка при сохранении товара', 'error');
        console.error('Error saving product:', error);
        return null;
      }
    };

    // Массовое сохранение изменений
    const bulkSaveChanges = async (changedProducts) => {
      try {
        const response = await ProductService.bulkSaveProducts(changedProducts);
        showToast('Изменения сохранены', 'success');
        await loadProducts();
        return response.data;
      } catch (error) {
        showToast('Ошибка при массовом сохранении', 'error');
        console.error('Error bulk saving products:', error);
        return null;
      }
    };

    // Удаление товара
    const deleteProduct = async (product) => {
      try {
        await ProductService.deleteProduct(product.id);
        showToast('Товар успешно удален', 'success');
        // Обновляем данные в таблице
        await loadProducts();
      } catch (error) {
        showToast('Ошибка при удалении товара', 'error');
        console.error('Error deleting product:', error);
      }
    };

    // Добавление товара
    const addProduct = async (product) => {
      // Просто добавляем пустую запись в таблицу, сохранение будет выполнено при нажатии "Сохранить"
      console.log('Adding new product:', product);
    };

    // Применение сортировки
    const applySorting = () => {
      loadProducts();
    };

    // Смена страницы
    const changePage = (page) => {
      pagination.value.currentPage = page;
      loadProducts();
    };

    // Экспорт товаров в CSV
    const exportToCSV = async () => {
      try {
        const blob = await ProductService.exportProductsToCSV();
        const url = window.URL.createObjectURL(new Blob([blob]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'products.csv');
        document.body.appendChild(link);
        link.click();
        link.remove();
        showToast('Экспорт успешно выполнен', 'success');
      } catch (error) {
        showToast('Ошибка при экспорте в CSV', 'error');
        console.error('Error exporting to CSV:', error);
      }
    };

    // Загрузка категорий
    const loadCategories = async () => {
      try {
        const response = await CategoryService.getCategories();
        categories.value = response.data;

        // Обновляем опции для выпадающего списка категорий
        tableHeaders.value = tableHeaders.value.map(header => {
          if (header.key === 'category_id') {
            return {
              ...header,
              options: categories.value.map(category => ({
                value: category.id,
                label: category.name
              }))
            };
          }
          return header;
        });
      } catch (error) {
        showToast('Ошибка загрузки категорий', 'error');
        console.error('Error loading categories:', error);
      }
    };

    // Обработка загрузки CSV-файла
    const handleCSVUpload = (event) => {
      csvFile.value = event.target.files[0];

      if (csvFile.value) {
        const reader = new FileReader();
        reader.onload = (e) => {
          const text = e.target.result;
          const rows = text.split('\n').slice(1); // Пропускаем заголовок
          importedData.value = rows.map(row => {
            const [id, name, article, categoryId, price, stock] = row.split(',');
            return {
              id: id || undefined,
              name,
              article,
              category_id: parseInt(categoryId),
              price: parseFloat(price),
              stock: parseInt(stock)
            };
          });
        };
        reader.readAsText(csvFile.value);
      }
    };

    // Импорт CSV-данных
    const importCSV = async () => {
      if (importedData.value.length === 0) {
        showToast('Нет данных для импорта', 'warning');
        return;
      }

      try {
        await ProductService.bulkSaveProducts(importedData.value);
        showToast('Данные успешно импортированы', 'success');
        await loadProducts();
        toggleImportModal();
      } catch (error) {
        showToast('Ошибка при импорте данных', 'error');
        console.error('Error importing CSV data:', error);
      }
    };

    // Переключение модального окна импорта
    const toggleImportModal = () => {
      showImportModal.value = !showImportModal.value;
      if (!showImportModal.value) {
        csvFile.value = null;
        importedData.value = [];
      }
    };

    // Инициализация страницы
    onMounted(() => {
      loadCategories();
      loadProducts();
    });

    return {
      products,
      categories,
      tableHeaders,
      filters,
      sort,
      pagination,
      showImportModal,
      csvFile,
      importedData,
      loadProducts,
      saveProduct,
      bulkSaveChanges,
      deleteProduct,
      addProduct,
      applySorting,
      changePage,
      exportToCSV,
      handleCSVUpload,
      importCSV,
      toggleImportModal
    };
  }
};
</script>
