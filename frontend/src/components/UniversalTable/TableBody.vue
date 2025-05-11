<template>
  <tbody>
  <tr v-for="(item, rowIndex) in localData" :key="item.id || rowIndex">
    <td v-for="header in headers" :key="`${item.id || rowIndex}-${header.key}`">
      <FieldFactory
        :header="header"
        :item="item"
        :row-index="rowIndex"
        :editable="editable"
        :edit-mode="editMode[rowIndex]"
        @update:value="handleFieldUpdate(rowIndex, header.key, $event)"
      />
    </td>
    <td v-if="editable" class="action-column">
      <ActionButtons
        :row-index="rowIndex"
        :edit-mode="editMode[rowIndex]"
        @start-edit="startEdit(rowIndex)"
        @save-row="saveRow(rowIndex)"
        @cancel-edit="cancelEdit(rowIndex)"
        @remove-row="removeRow(rowIndex)"
      />
    </td>
  </tr>
  <tr v-if="localData.length === 0">
    <td :colspan="headers.length + (editable ? 1 : 0)" class="text-center">
      Нет данных для отображения
    </td>
  </tr>
  </tbody>
</template>

<script>
import { defineComponent } from 'vue';
import FieldFactory from './fields/FieldFactory.vue';
import ActionButtons from './ActionButtons.vue';

export default defineComponent({
  name: 'TableBody',
  components: { FieldFactory, ActionButtons },
  props: {
    headers: Array,
    localData: Array,
    editable: Boolean,
    editMode: Array,
  },
  emits: ['update:value', 'start-edit', 'save-row', 'cancel-edit', 'remove-row'],
  setup(props, { emit }) {
    const handleFieldUpdate = (rowIndex, key, value) => {
      props.localData[rowIndex][key] = value;
    };

    return {
      handleFieldUpdate,
      startEdit: (rowIndex) => emit('start-edit', rowIndex),
      saveRow: (rowIndex) => emit('save-row', rowIndex),
      cancelEdit: (rowIndex) => emit('cancel-edit', rowIndex),
      removeRow: (rowIndex) => emit('remove-row', rowIndex),
    };
  }
});
</script>
