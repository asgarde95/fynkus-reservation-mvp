<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useBookingStore } from '@/stores/booking'

// Inicializar el store al cargar la aplicación
const bookingStore = useBookingStore()
bookingStore.fetchSpaces()
</script>

<template>
  <div class="app-container">
    <header class="app-header">
      <h1>Sistema de Reservas Comunitarias</h1>
    </header>

    <main class="app-main">
      <RouterView v-slot="{ Component }">
        <Suspense>
          <!-- Contenido principal -->
          <component :is="Component" />

          <!-- Loading state -->
          <template #fallback>
            <div class="loading-container">
              <p>Cargando aplicación...</p>
            </div>
          </template>
        </Suspense>
      </RouterView>
    </main>

    <footer class="app-footer">
      <p>© 2025 Assier</p>
    </footer>
  </div>
</template>

<style scoped>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.app-header {
  background-color: #2c3e50;
  color: white;
  padding: 1rem;
  text-align: center;
}

.app-main {
  flex: 1;
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.app-footer {
  background-color: #2c3e50;
  color: white;
  text-align: center;
  padding: 1rem;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
}
</style>

<style>
/* Estilos globales */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  line-height: 1.6;
}

#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
</style>