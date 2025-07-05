<script setup lang="ts">
import type { Space } from '@/types/booking'

defineProps<{
  spaces: Space[]
}>()

const modelValue = defineModel<string>({ required: true })
</script>

<template>
  <select v-model="modelValue" class="space-selector">
    <option
        v-for="space in spaces"
        :key="space.id"
        :value="space.id"
    >
      {{ space.name }}
    </option>
  </select>
</template>

<style scoped>
.space-selector {
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid #ccc;
  min-width: 200px;
}
</style>