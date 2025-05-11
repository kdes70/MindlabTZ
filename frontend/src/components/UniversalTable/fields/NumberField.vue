<template>
  <input
    v-if="editMode"
    type="number"
    class="form-control form-control-sm"
    :value="value"
    @input="$emit('update:value', Number($event.target.value))"
  />
  <span v-else>{{ value }}</span>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'NumberField',
  props: {
    value: [String, Number],
    header: Object,
    rowIndex: Number,
    editMode: Boolean,
  },
  emits: ['update:value'],
});
</script>
