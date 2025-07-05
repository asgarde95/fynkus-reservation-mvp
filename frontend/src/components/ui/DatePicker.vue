<script setup lang="ts">
const modelValue = defineModel<string>({ required: true })
</script>

<template>
  <input
      v-model="modelValue"
      type="date"
      class="date-picker"
      :min="new Date().toISOString().split('T')[0]"
  />
</template>

<style scoped>
.date-picker {
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
</style>