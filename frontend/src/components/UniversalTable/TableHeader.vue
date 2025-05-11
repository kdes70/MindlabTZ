<template>
  <thead>
  <tr>
    <th v-for="header in headers" :key="header.key">
      {{ header.label }}
    </th>
    <th v-if="editable" class="action-column">Действия</th>
  </tr>
  </thead>
</template>

<script>
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'TableHeader',
  props: {
    headers: {
      type: Array,
      required: true
    },
    editable: {
      type: Boolean,
      default: true
    }
  }
});
</script>

<style scoped>
.action-column {
  white-space: nowrap;
  width: 1%; /* Минимальная ширина */
}
</style>
