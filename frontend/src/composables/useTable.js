// composables/useTable.js
import { ref, computed, watch } from 'vue';

export function useTable(props, emit) {
  const localData = ref(cloneData(props.data));
  const editMode = ref(Array(props.data.length).fill(false));
  const originalData = ref(cloneData(props.data));

  watch(() => props.data, (newData) => {
    localData.value = cloneData(newData);
    editMode.value = Array(newData.length).fill(false);
    originalData.value = cloneData(newData);
  }, { deep: true });

  const hasChanges = computed(() =>
    JSON.stringify(localData.value) !== JSON.stringify(originalData.value)
  );

  // Методы управления данными
  const startEdit = (rowIndex) => editMode.value[rowIndex] = true;
  const saveRow = (rowIndex) => {
    editMode.value[rowIndex] = false;
    emit('save-row', localData.value[rowIndex], rowIndex);
  };
  const cancelEdit = (rowIndex) => {
    localData.value[rowIndex] = cloneData(originalData.value[rowIndex]);
    editMode.value[rowIndex] = false;
  };
  const removeRow = (rowIndex) => {
    localData.value.splice(rowIndex, 1);
    editMode.value.splice(rowIndex, 1);
    emit('delete-row', rowIndex);
  };
  const addNewRow = () => {
    const newRow = props.headers.reduce((acc, header) => {
      acc[header.key] = '';
      return acc;
    }, { _tempId: Date.now() });
    localData.value.push(newRow);
    editMode.value.push(true);
  };
  const saveChanges = () => {
    originalData.value = cloneData(localData.value);
    emit('save-changes', localData.value);
  };

  // Пагинация
  const paginationData = computed(() => props.pagination || {/* default values */});
  const pageNumbers = computed(() => {/* pagination logic */});

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
    paginationData,
    pageNumbers,
    changePage: (page) => emit('page-change', page)
  };
}

function cloneData(data) {
  return JSON.parse(JSON.stringify(data));
}
