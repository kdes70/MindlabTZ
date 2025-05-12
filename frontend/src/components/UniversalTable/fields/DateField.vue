<template>
  <input
    v-if="editMode"
    type="date"
    class="form-control form-control-sm"
    :value="value"
    @input="$emit('update:value', $event.target.value)"
  />
  <span v-else>{{ formattedDate }}</span>
</template>

<script>
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'DateField',
  props: {
    value: [String, Number],
    header: Object,
    rowIndex: Number,
    editMode: Boolean,
  },
  emits: ['update:value'],
  setup(props) {
    const formattedDate = computed(() => {
      if (!props.value) return '';
      const date = new Date(props.value);
      return isNaN(date.getTime()) ? props.value : date.toLocaleDateString('ru-RU');
    });

    return { formattedDate };
  }
});
</script>
