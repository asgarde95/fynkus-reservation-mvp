import { createRouter, createWebHistory } from 'vue-router'
import BookingView from '@/views/BookingView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'booking',
            component: BookingView
        }
    ]
})

export default router