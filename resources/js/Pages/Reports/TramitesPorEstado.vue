<template>
  <div class="p-4">
    <h1 class="text-2xl mb-4 font-bold">Reporte de Solicitudes por Trámite y Estado</h1>

    <!-- Filtros -->
    <form @submit.prevent="submit" class="grid grid-cols-2 gap-4 mb-6">
      <div>
        <label>Fecha Desde</label>
        <input type="date" v-model="form.fecha_desde" class="border rounded p-2 w-full" />
      </div>
      <div>
        <label>Fecha Hasta</label>
        <input type="date" v-model="form.fecha_hasta" class="border rounded p-2 w-full" />
      </div>
      <!-- <div>
        <label>Estado</label>
        <select v-model="form.estado" class="border rounded p-2 w-full">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="completado">Completado</option>
          <option value="rechazado">Rechazado</option>
        </select>
      </div>
      <div>
        <label>Encargado ID</label>
        <input type="number" v-model="form.encargado_id" class="border rounded p-2 w-full" />
      </div>
      <div>
        <label>Trámite ID</label>
        <input type="number" v-model="form.tramite_id" class="border rounded p-2 w-full" />
      </div> -->
      <div class="flex items-end">
        <button type="submit" class="btn btn-primary mr-4">Filtrar</button>
        <button type="button" @click="exportarPdf" class="btn btn-success">Exportar PDF</button>
      </div>
    </form>
    <!-- Tabla -->
    <table class="table-auto w-full mb-8 border-collapse border border-gray-300">
      <thead>
        <tr>
          <th class="border border-gray-300 px-4 py-2">Trámite</th>
          <th v-for="estado in estadosUnicos" :key="estado" class="border border-gray-300 px-4 py-2">
            {{ estado }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(estados, tramite) in reporte" :key="tramite">
          <td class="border border-gray-300 px-4 py-2 font-semibold">{{ tramite }}</td>
          <td v-for="estado in estadosUnicos" :key="estado" class="border border-gray-300 px-4 py-2 text-center">
            {{ estados[estado] || 0 }}
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Gráfico -->
    <div style="height: 400px;">
      <!-- Usa directamente Bar y pásale props -->
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'
import Layout from '@/Shared/Layout.vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  props: {
    reporte: Object,
    filters: Object,
  },
  layout: Layout,
  components: {
    Bar,
  },
  data() {
    return {
      form: {
        fecha_desde: this.filters.fecha_desde || '',
        fecha_hasta: this.filters.fecha_hasta || '',
        estado: this.filters.estado || '',
        encargado_id: this.filters.encargado_id || '',
        tramite_id: this.filters.tramite_id || '',
      },
      estadosUnicos: [],
      chartData: {
        labels: [],
        datasets: [],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' },
          title: {
            display: true,
            text: 'Solicitudes por estado y trámite',
          },
        },
        scales: {
          y: { beginAtZero: true },
        },
      },
    }
  },
  watch: {
    reporte: {
      immediate: true,
      handler(newReporte) {
        this.prepareChartData(newReporte)
      },
    },
  },
  methods: {
    submit() {
      this.$inertia.get('/reportes/por_estados', this.form, {
        preserveState: true,
        preserveScroll: true,
      })
    },
    colorForEstado(estado) {
      const colors = {
        pendiente: '#fbbf24',
        aprobado: '#22c55e',
        rechazado: '#ef4444',
      }
      return colors[estado] || '#3b82f6'
    },
    prepareChartData(reporte) {
      if (!reporte) return

      const estadosSet = new Set()
      Object.values(reporte).forEach(estados => {
        Object.keys(estados).forEach(e => estadosSet.add(e))
      })
      this.estadosUnicos = Array.from(estadosSet)

      const datasets = this.estadosUnicos.map(estado => ({
        label: estado,
        backgroundColor: this.colorForEstado(estado),
        data: Object.values(reporte).map(estados => estados[estado] || 0),
      }))

      this.chartData = {
        labels: Object.keys(reporte),
        datasets,
      }
    },
    exportarPdf() {
      const params = new URLSearchParams({
        ...this.form,
        export: 'pdf',
      }).toString()
      window.open(`/ventanilla_unica/public/reportes/por_estados?${params}`, '_blank')
    }
  },
}
</script>
