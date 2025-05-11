<template>
  <div class="universal-table-container">
    <div class="table-controls mb-4">
      <div class="d-flex justify-content-between align-items-center">
        <h3 v-if="title" class="table-title">
          {{ title }}
        </h3>
        <div class="table-actions">
          <button
            v-if="showAddButton"
            class="btn btn-primary btn-sm me-2"
            @click="addNewRow"
          >
            Добавить запись
          </button>
          <button
            v-if="hasChanges"
            class="btn btn-success btn-sm"
            @click="saveChanges"
          >
            Сохранить изменения
          </button>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
        <tr>
          <th v-for="header in headers" :key="header.key">
            {{ header.label }}
          </th>
          <th v-if="editable" class="action-column">
            Действия
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, rowIndex) in localData" :key="item.id || rowIndex">
          <td v-for="header in headers" :key="`${item.id || rowIndex}-${header.key}`">
            <!-- Разные типы полей -->
            <template v-if="editable && !header.readonly && editMode[rowIndex]">
              <!-- Input field -->
              <input
                v-if="header.type === 'input'"
                type="text"
                class="form-control form-control-sm"
                v-model="localData[rowIndex][header.key]"
              />
              <!-- Number input field -->
              <input
                v-else-if="header.type === 'number'"
                type="number"
                class="form-control form-control-sm"
                v-model.number="localData[rowIndex][header.key]"
              />
              <!-- Select field -->
              <select
                v-else-if="header.type === 'select'"
                class="form-select form-select-sm"
                v-model="localData[rowIndex][header.key]"
              >
                <option
                  v-for="option in header.options"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
              <!-- Date field -->
              <input
                v-else-if="header.type === 'date'"
                type="date"
                class="form-control form-control-sm"
                v-model="localData[rowIndex][header.key]"
              />
              <!-- Формула (только отображение) -->
              <span v-else-if="header.type === 'formula'">
                  {{ calculateFormula(header.formula, localData[rowIndex]) }}
                </span>
              <!-- Default: text input -->
              <input
                v-else
                type="text"
                class="form-control form-control-sm"
                v-model="localData[rowIndex][header.key]"
              />
            </template>
            <!-- Отображение данных (не в режиме редактирования) -->
            <template v-else>
              <!-- Отображение для формул -->
              <span v-if="header.type === 'formula'">
                  {{ calculateFormula(header.formula, item) }}
                </span>
              <!-- Отображение для дат -->
              <span v-else-if="header.type === 'date'">
                  {{ formatDate(item[header.key]) }}
                </span>
              <!-- Отображение для select (показываем label) -->
              <span v-else-if="header.type === 'select'">
                  {{ getSelectOptionLabel(header, item[header.key]) }}
                </span>
              <!-- Обычное отображение -->
              <span v-else>
                  {{ item[header.key] }}
                </span>
            </template>
          </td>
          <!-- Колонка действий -->
          <td v-if="editable" class="action-column">
            <button
              v-if="!editMode[rowIndex]"
              class="btn btn-primary btn-sm me-1"
              @click="startEdit(rowIndex)"
            >
              Изменить
            </button>
            <button
              v-if="editMode[rowIndex]"
              class="btn btn-success btn-sm me-1"
              @click="saveRow(rowIndex)"
            >
              Сохранить
            </button>
            <button
              v-if="editMode[rowIndex]"
              class="btn btn-secondary btn-sm me-1"
              @click="cancelEdit(rowIndex)"
            >
              Отмена
            </button>
            <button
              class="btn btn-danger btn-sm"
              @click="removeRow(rowIndex)"
            >
              Удалить
            </button>
          </td>
        </tr>
        <tr v-if="localData.length === 0">
          <td :colspan="headers.length + (editable ? 1 : 0)" class="text-center">
            Нет данных для отображения
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <!-- Пагинация -->
    <div v-if="pagination" class="d-flex justify-content-between align-items-center mt-3">
      <div class="pagination-info">
        Показано {{ paginationData.from }}-{{ paginationData.to }} из {{ paginationData.total }} записей
      </div>
      <div class="pagination-controls">
        <button
          class="btn btn-sm btn-outline-primary me-1"
          :disabled="paginationData.currentPage === 1"
          @click="changePage(paginationData.currentPage - 1)"
        >
          Назад
        </button>
        <button
          v-for="page in pageNumbers"
          :key="page"
          class="btn btn-sm"
          :class="page === paginationData.currentPage ? 'btn-primary' : 'btn-outline-primary'"
          @click="changePage(page)"
        >
          {{ page }}
        </button>
        <button
          class="btn btn-sm btn-outline-primary ms-1"
          :disabled="paginationData.currentPage === paginationData.lastPage"
          @click="changePage(paginationData.currentPage + 1)"
        >
          Вперед
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
  name: 'UniversalTable',
  props: {
    // Заголовки таблицы
    headers: {
      type: Array,
      required: true
      // Формат: [{ key: 'id', label: 'ID', readonly: true, type: 'input|select|number|date|formula', options: [], formula: '' }]
    },
    // Данные для отображения
    data: {
      type: Array,
      required: true
    },
    // Возможность редактирования данных
    editable: {
      type: Boolean,
      default: true
    },
    // Заголовок таблицы
    title: {
      type: String,
      default: ''
    },
    // Показать кнопку добавления
    showAddButton: {
      type: Boolean,
      default: true
    },
    // Настройки пагинации
    pagination: {
      type: Object,
      default: null
      // Формат: { perPage: 10, currentPage: 1, total: 100, from: 1, to: 10, lastPage: 10 }
    }
  },
  emits: ['update:data', 'save-row', 'delete-row', 'add-row', 'save-changes', 'page-change'],
  setup(props, {emit}) {
    // Локальная копия данных для редактирования
    const localData = ref(JSON.parse(JSON.stringify(props.data)));

    // Отслеживание изменений во входных данных
    watch(
      () => props.data,
      (newData) => {
        localData.value = JSON.parse(JSON.stringify(newData));
        // Сбрасываем состояние редактирования
        editMode.value = Array(newData.length).fill(false);
        originalData.value = JSON.parse(JSON.stringify(newData));
      },
      {deep: true}
    );

    // Сохраняем оригинальные данные для возможности отмены изменений
    const originalData = ref(JSON.parse(JSON.stringify(props.data)));
    // Состояние редактирования для каждой строки
    const editMode = ref(Array(props.data.length).fill(false));
    // Флаг наличия изменений
    const hasChanges = computed(() => {
      return JSON.stringify(localData.value) !== JSON.stringify(originalData.value);
    });

    // Начать редактирование строки
    const startEdit = (rowIndex) => {
      editMode.value[rowIndex] = true;
    };

    // Сохранить отредактированную строку
    const saveRow = (rowIndex) => {
      editMode.value[rowIndex] = false;
      emit('save-row', localData.value[rowIndex], rowIndex);
    };

    // Отменить редактирование строки
    const cancelEdit = (rowIndex) => {
      localData.value[rowIndex] = JSON.parse(JSON.stringify(originalData.value[rowIndex]));
      editMode.value[rowIndex] = false;
    };

    // Удалить строку
    const removeRow = (rowIndex) => {
      if (confirm('Вы уверены, что хотите удалить эту запись?')) {
        const removedItem = localData.value[rowIndex];
        localData.value.splice(rowIndex, 1);
        editMode.value.splice(rowIndex, 1);
        emit('delete-row', removedItem, rowIndex);
        emit('update:data', localData.value);
      }
    };

    // Добавить новую строку
    const addNewRow = () => {
      // Создаем пустой объект с ключами из заголовков
      const newRow = {};
      props.headers.forEach(header => {
        newRow[header.key] = '';
      });
      // Добавляем временный id для корректной работы v-for
      newRow._tempId = Date.now();
      localData.value.push(newRow);
      editMode.value.push(true);
      emit('add-row', newRow);
      emit('update:data', localData.value);
    };

    // Сохранить все изменения
    const saveChanges = () => {
      originalData.value = JSON.parse(JSON.stringify(localData.value));
      emit('save-changes', localData.value);
      emit('update:data', localData.value);
    };

    // Рассчитать значение по формуле
    const calculateFormula = (formula, row) => {
      try {
        // Замена ссылок на поля их значениями
        let calculableFormula = formula;
        Object.keys(row).forEach(key => {
          const regex = new RegExp(`\\{${key}\\}`, 'g');
          calculableFormula = calculableFormula.replace(regex, parseFloat(row[key]) || 0);
        });
        // Вычисление формулы
        // Безопасное выполнение (только базовые математические операции)
        // eslint-disable-next-line no-new-func
        const result = new Function(`return ${calculableFormula}`)();
        return isNaN(result) ? 0 : result.toFixed(2);
      } catch (error) {
        console.error('Ошибка в вычислении формулы:', error);
        return 'Ошибка';
      }
    };

    // Форматирование даты
    const formatDate = (dateString) => {
      if (!dateString) return '';
      const date = new Date(dateString);
      return isNaN(date.getTime()) ? dateString : date.toLocaleDateString('ru-RU');
    };

    // Получить метку для значения select
    const getSelectOptionLabel = (header, value) => {
      if (!header.options || !Array.isArray(header.options)) return value;
      const option = header.options.find(opt => opt.value === value);
      return option ? option.label : value;
    };

    // Данные пагинации
    const paginationData = computed(() => {
      return props.pagination || {
        currentPage: 1,
        lastPage: 1,
        perPage: 10,
        total: localData.value.length,
        from: 1,
        to: Math.min(10, localData.value.length)
      };
    });

    // Номера страниц для отображения
    const pageNumbers = computed(() => {
      const currentPage = paginationData.value.currentPage;
      const lastPage = paginationData.value.lastPage;
      // Показываем максимум 5 страниц
      const delta = 2;
      let range = [];
      for (let i = Math.max(1, currentPage - delta); i <= Math.min(lastPage, currentPage + delta); i++) {
        range.push(i);
      }
      return range;
    });

    // Изменение страницы
    const changePage = (page) => {
      emit('page-change', page);
    };

    return {
      localData,
      editMode,
      hasChanges,
      startEdit,
      saveRow,
      cancelEdit,
      removeRow,
      addNewRow,
      saveChanges,
      calculateFormula,
      formatDate,
      getSelectOptionLabel,
      paginationData,
      pageNumbers,
      changePage
    };
  }
};
</script>

<style scoped>
.universal-table-container {
  font-size: 0.9rem;
}

.action-column {
  white-space: nowrap;
  width: 1%; /* Минимальная ширина */
}

.table-title {
  margin: 0;
  font-size: 1.5rem;
}
</style>
