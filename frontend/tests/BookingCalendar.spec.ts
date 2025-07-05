import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import BookingCalendar from '@/components/BookingCalendar.vue'
import { useBookingStore } from '@/stores/booking'
import type { TimeSlot } from '@/types/booking'

vi.mock('@/stores/booking')

describe('BookingCalendar', () => {
    it('renders time slots correctly', () => {
        const mockStore = {
            availability: [
                { time: '09:00', available: true },
                { time: '10:00', available: false }
            ] as TimeSlot[],
            fetchAvailability: vi.fn()
        }

        // @ts-ignore
        useBookingStore.mockReturnValue(mockStore)

        const wrapper = mount(BookingCalendar, {
            props: {
                date: '2023-01-01',
                space: 'padel'
            }
        })

        expect(wrapper.text()).toContain('09:00')
        expect(wrapper.text()).toContain('10:00')
        expect(wrapper.find('button.available').exists()).toBe(true)
        expect(wrapper.find('button.booked').exists()).toBe(true)
    })
})