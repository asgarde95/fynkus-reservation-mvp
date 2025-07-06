<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import { useBookingStore } from '@/stores/booking'
import type { TimeSlot } from '@/types/booking'

const props = defineProps<{
  date: string
  space: string
}>()

const bookingStore = useBookingStore()
const timeSlots = ref<TimeSlot[]>([])
const isLoading = ref(false)
const error = ref<string | null>(null)

watchEffect(async () => {
  try {
    isLoading.value = true
    error.value = null
    await bookingStore.fetchAvailability(props.space, props.date)
    timeSlots.value = bookingStore.availability
  } catch (err) {
    error.value = 'Error al cargar disponibilidad'
    console.error(err)
  } finally {
    isLoading.value = false
  }
})

const bookSlot = async (time: string) => {
  try {
    await bookingStore.createBooking({
      space: props.space,
      date: props.date,
      time
    })

    // Actualizar la disponibilidad después de reservar
    await bookingStore.fetchAvailability(props.space, props.date)
    // Actualizar los timeSlots con la nueva disponibilidad
    timeSlots.value = bookingStore.availability

  } catch (err) {
    console.error('Error al reservar:', err)
  }
}
</script>

<template>
  <div class="calendar">
    <div v-if="isLoading">Cargando disponibilidad...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <div v-if="timeSlots.length === 0" class="no-slots-message">
        Debes de ejecutar la migración de la base de datos.
        Y recargar la página. (Perdón por las molestias)
      </div>
      <div v-else class="slots-grid">
        <button
            v-for="slot in timeSlots"
            :key="slot.time"
            @click="bookSlot(slot.time)"
            :disabled="!slot.available"
            :class="{
            'available': slot.available,
            'booked': !slot.available
          }"
        >
          {{ slot.time }} - {{ slot.available ? 'LIBRE' : 'RESERVADO' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.slots-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

button {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

button.available {
  background-color: #e8f5e9;
}

button.available:hover {
  background-color: #c8e6c9;
}

button.booked {
  background-color: #ffebee;
  cursor: not-allowed;
}

.error {
  color: #d32f2f;
  padding: 10px;
  background-color: #ffebee;
  border-radius: 4px;
}

.no-slots-message {
  color: #757575;
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 4px;
  text-align: center;
}
</style>