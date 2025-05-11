<template>
  <component
    :is="fieldComponent"
    :value="item[header.key]"
    :header="header"
    :row-index="rowIndex"
    :edit-mode="editMode"
    @update:value="$emit('update:value', $event)"
  />
</template>

<script>
import {defineComponent, computed} from 'vue';
import InputField from './InputField.vue';
import NumberField from './NumberField.vue';
import SelectField from './SelectField.vue';
import DateField from './DateField.vue';
import FormulaField from './FormulaField.vue';

export default defineComponent({
  name: 'FieldFactory',
  components: {InputField, NumberField, SelectField, DateField, FormulaField},
  props: {
    header: {
      type: Object,
      required: true,
      default: () => ({
        key: '',
        label: '',
        readonly: false,
        type: 'input',
        options: [],
        formula: ''
      }),
    },
    item: Object,
    rowIndex: Number,
    editable: Boolean,
    editMode: Boolean,
  },
  emits: ['update:value'],
  setup(props) {
    const fieldComponent = computed(() => {
      if (!props.editable || props.header.readonly) return 'span';
      if (!props.editMode) return props.header.type === 'formula' ? 'FormulaField' : 'span';

      const componentMap = {
        input: 'InputField',
        number: 'NumberField',
        select: 'SelectField',
        date: 'DateField',
        formula: 'FormulaField',
      };

      return componentMap[props.header.type] || 'InputField';
    });

    return {fieldComponent};
  }
});
</script>
