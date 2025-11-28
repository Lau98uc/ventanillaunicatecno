<template>
  <div>
    <Head title="Dashboard" />
    <h1 class="mb-8 text-3xl font-bold">Dashboard</h1>

    <!-- Estadísticas de Visitas -->
    <div class="mb-8">
      <h2 class="text-2xl font-semibold mb-4 text-base-content">Estadísticas de Visitas</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-base-200 p-6 rounded-lg shadow">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-primary text-primary-content">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-base-content">Total Visitas</p>
              <p class="text-2xl font-semibold text-base-content">{{ estadisticasVisitas.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-base-200 p-6 rounded-lg shadow">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-success text-success-content">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-base-content">Hoy</p>
              <p class="text-2xl font-semibold text-base-content">{{ estadisticasVisitas.hoy || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-base-200 p-6 rounded-lg shadow">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-warning text-warning-content">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-base-content">Esta Semana</p>
              <p class="text-2xl font-semibold text-base-content">{{ estadisticasVisitas.esta_semana || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-base-200 p-6 rounded-lg shadow">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-secondary text-secondary-content">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-base-content">Este Mes</p>
              <p class="text-2xl font-semibold text-base-content">{{ estadisticasVisitas.este_mes || 0 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico de barras de visitas por ruta -->
    <div class="mb-8 bg-base-100 p-6 rounded-lg shadow">
      <h2 class="text-2xl font-semibold mb-4 text-base-content">Visitas por Página</h2>
      <Bar :data="barChartData" :options="barChartOptions" />
    </div>

    <!-- Rutas Más Visitadas -->
    <div class="mb-8">
      <h2 class="text-2xl font-semibold mb-4 text-base-content">Páginas Más Visitadas</h2>
      <div class="bg-base-100 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-base-200">
          <h3 class="text-lg font-medium text-base-content">Top 5 Rutas</h3>
        </div>
        <div class="divide-y divide-base-200">
          <div v-for="(ruta, index) in rutasMasVisitadas" :key="index" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-primary-content text-sm font-medium mr-3">
                {{ index + 1 }}
              </span>
              <span class="text-base-content font-medium">{{ ruta.route }}</span>
            </div>
            <span class="text-base-content">{{ ruta.total_visitas }} visitas</span>
          </div>
        </div>
      </div>
    </div>

    <p class="mb-8 leading-normal text-base-content">Bienvenido al sistema de Ventanilla Única. Aquí puedes ver las estadísticas de uso de la aplicación.</p>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  components: {
    Head,
    Bar
  },
  layout: Layout,
  props: {
    estadisticasVisitas: {
      type: Object,
      required: true,
      default: () => ({
        total: 0,
        hoy: 0,
        esta_semana: 0,
        este_mes: 0,
      }),
    },
    rutasMasVisitadas: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  data() {
    return {
      barChartData: {
        labels: this.rutasMasVisitadas.map(r => r.route),
        datasets: [
          {
            label: 'Visitas',
            backgroundColor: [
              '#a78bfa', '#f472b6', '#60a5fa', '#34d399', '#fbbf24'
            ],
            data: this.rutasMasVisitadas.map(r => r.total_visitas)
          }
        ]
      },
      barChartOptions: {
        responsive: true,
        plugins: {
          legend: { display: true },
          title: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    }
  },
  mounted() {
    console.log('Dashboard mounted');
    console.log('estadisticasVisitas:', this.estadisticasVisitas);
    console.log('rutasMasVisitadas:', this.rutasMasVisitadas);
  },
}
</script>
