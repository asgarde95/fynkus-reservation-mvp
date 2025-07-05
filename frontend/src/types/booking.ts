export interface Space {
    id: string
    name: string
    description?: string
}

export interface TimeSlot {
    time: string
    available: boolean
}

export interface BookingRequest {
    space: string
    date: string
    time: string
}