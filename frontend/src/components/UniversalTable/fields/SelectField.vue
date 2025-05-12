<template>
  <select
    v-if="editMode"
    class="form-select form-select-sm"
    :value="value"
    @change="$emit('update:value', $event.target.value)"
  >
    <option v-for="option in header.options" :key="option.value" :value="option.value">
      {{ option.label }}
    </option>
  </select>
  <span v-else>{{ selectedLabel }}</span>
</template>

<script>
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'SelectField',
  props: {
    value: [String, Number],
    header: Object,
    rowIndex: Number,
    editMode: Boolean,
  },
  emits: ['update:value'],
  setup(props) {
    const selectedLabel = computed(() => {
      const option = props.header.options?.find(opt => opt.value === props.value);
      return option?.label || props.value;
    });

    return { selectedLabel };
  }
});
</script>
