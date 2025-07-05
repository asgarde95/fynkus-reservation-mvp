<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useBookingStore } from '@/stores/booking'
import BookingCalendar from '@/components/BookingCalendar.vue'
import SpaceSelector from '@/components/ui/SpaceSelector.vue'
import DatePicker from '@/components/ui/DatePicker.vue'

const bookingStore = useBookingStore()
const selectedDate = ref<string>(new Date().toISOString().split('T')[0])
const selectedSpace = ref<string>('padel')

onMounted(async () => {
  await bookingStore.fetchSpaces()
})
</script>

<template>
  <div class="booking-view">
    <h1>Reserva de Espacios Comunes</h1>

    <div class="controls">
      <SpaceSelector
          v-model="selectedSpace"
          :spaces="bookingStore.spaces"
      />
      <DatePicker v-model="selectedDate" />
    </div>

    <BookingCalendar
        :date="selectedDate"
        :space="selectedSpace"
    />
  </div>
</template>

<style scoped>
.booking-view {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}
.controls {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}
</style>