<!-- components/UniversalTable/fields/FormulaField.vue -->
<template>
  <span>{{ calculatedValue }}</span>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { useTableFormula } from '@/composables/useTableFormula';

export default defineComponent({
  name: 'FormulaField',
  props: {
    value: [String, Number],
    header: Object,
    rowIndex: Number,
    editMode: Boolean,
  },
  setup(props) {
    const { calculateFormula } = useTableFormula();
    const calculatedValue = computed(() =>
      calculateFormula(props.header.formula, props.value)
    );

    return { calculatedValue };
  }
});
</script>
