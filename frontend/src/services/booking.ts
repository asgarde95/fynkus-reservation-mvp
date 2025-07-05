import axios from 'axios'
import type { Space, TimeSlot, BookingRequest } from '@/types/booking'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json'
    }
})

export default {
    async getSpaces(): Promise<Space[]> {
        const response = await api.get('/api/spaces')
        return response.data
    },

    async getAvailability(spaceId: string, date: string): Promise<TimeSlot[]> {
        const response = await api.get('/api/availability', {
            params: { space: spaceId, date }
        })
        return response.data
    },

    async createBooking(booking: BookingRequest): Promise<void> {
        await api.post('/api/bookings', booking)
    }
}