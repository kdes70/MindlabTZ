<template>
  <div class="table-controls mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <h3 v-if="title" class="table-title">{{ title }}</h3>
      <div class="table-actions">
        <button
          v-if="showAddButton"
          class="btn btn-primary btn-sm me-2"
          @click="$emit('add-new-row')"
        >
          Добавить запись
        </button>
        <button
          v-if="hasChanges"
          class="btn btn-success btn-sm"
          @click="$emit('save-changes')"
        >
          Сохранить изменения
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'TableControls',
  props: {
    title: {
      type: String,
      default: ''
    },
    showAddButton: {
      type: Boolean,
      default: true
    },
    hasChanges: {
      type: Boolean,
      default: false
    }
  },
  emits: ['add-new-row', 'save-changes']
});
</script>

<style scoped>
.table-title {
  margin: 0;
  font-size: 1.5rem;
}
</style>
