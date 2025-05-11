<template>
  <div v-if="pagination" class="d-flex justify-content-between align-items-center mt-3">
    <div class="pagination-info">
      Показано с {{ pagination.from }} по {{ pagination.to }} из {{ pagination.total }} записей
    </div>
    <nav>
      <ul class="pagination mb-0">
        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
          <button class="page-link" @click="changePage(pagination.currentPage - 1)">Назад</button>
        </li>
        <li
          v-for="page in pageNumbers"
          :key="page"
          class="page-item"
          :class="{ active: page === pagination.currentPage }"
        >
          <button class="page-link" @click="changePage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.lastPage }">
          <button class="page-link" @click="changePage(pagination.currentPage + 1)">Вперед</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'TablePagination',
  props: {
    pagination: {
      type: Object,
      default: null
    },
    pageNumbers: {
      type: Array,
      default: () => []
    }
  },
  emits: ['page-change'],
  setup(props, { emit }) {
    const changePage = (page) => {
      if (page >= 1 && page <= props.pagination.lastPage) {
        emit('page-change', page);
      }
    };

    return {
      changePage
    };
  }
});
</script>
