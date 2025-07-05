import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Space, TimeSlot, BookingRequest } from '@/types/booking'
import BookingService from '@/services/booking'

export const useBookingStore = defineStore('booking', () => {
    const spaces = ref<Space[]>([])
    const availability = ref<TimeSlot[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)

    const fetchSpaces = async () => {
        try {
            loading.value = true
            spaces.value = await BookingService.getSpaces()
        } catch (err) {
            error.value = 'Error al cargar espacios'
            console.error(err)
        } finally {
            loading.value = false
        }
    }

    const fetchAvailability = async (spaceId: string, date: string) => {
        try {
            loading.value = true
            availability.value = await BookingService.getAvailability(spaceId, date)
        } catch (err) {
            error.value = 'Error al cargar disponibilidad'
            console.error(err)
        } finally {
            loading.value = false
        }
    }

    const createBooking = async (booking: BookingRequest) => {
        try {
            loading.value = true
            await BookingService.createBooking(booking)
            // Refrescar disponibilidad después de reservar
            await fetchAvailability(booking.space, booking.date)
        } catch (err) {
            error.value = 'Error al crear reserva'
            console.error(err)
            throw err
        } finally {
            loading.value = false
        }
    }

    return {
        spaces,
        availability,
        loading,
        error,
        fetchSpaces,
        fetchAvailability,
        createBooking
    }
})