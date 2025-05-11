<!-- components/UniversalTable/fields/InputField.vue -->
<template>
  <input
    v-if="editMode"
    type="text"
    class="form-control form-control-sm"
    :value="value"
    @input="$emit('update:value', $event.target.value)"
  />
  <span v-else>{{ value }}</span>
</template>

<script>
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'InputField',
  props: {
    value: [String, Number],
    header: Object,
    rowIndex: Number,
    editMode: Boolean,
  },
  emits: ['update:value'],
});
</script>
