<template>
  <div class="p-4">

    <Head title="Reporte de Solicitudes por Trámite" />

    <!-- Filtros con DaisyUI -->
    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

      <div class="form-control">
        <label class="label">
          <span class="label-text">Fecha Desde</span>
        </label>
        <input type="date" v-model="form.fecha_desde" class="input input-bordered w-full" />
      </div>

      <div class="form-control">
        <label class="label">
          <span class="label-text">Fecha Hasta</span>
        </label>
        <input type="date" v-model="form.fecha_hasta" class="input input-bordered w-full" />
      </div>

     <!--  <div class="form-control">
        <label class="label">
          <span class="label-text">Estado</span>
        </label>
        <select v-model="form.estado" class="select select-bordered w-full">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="aprobado">Aprobado</option>
          <option value="rechazado">Rechazado</option>
        </select>
      </div>

      <div class="form-control">
        <label class="label">
          <span class="label-text">Encargado ID</span>
        </label>
        <input type="number" v-model="form.encargado_id" class="input input-bordered w-full" />
      </div>

      <div class="form-control">
        <label class="label">
          <span class="label-text">Trámite ID</span>
        </label>
        <input type="number" v-model="form.tramite_id" class="input input-bordered w-full" />
      </div>
 -->
      <div class="form-control flex flex-row items-end gap-2">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <button type="button" @click="exportPdf" class="btn btn-success">Exportar PDF</button>
      </div>

    </form>





    <!-- Resumen -->
    <div class="mb-6">
      <h2 class="text-xl mb-2">Resumen por Trámite</h2>
      <table class="min-w-full  shadow rounded">
        <thead>
          <tr>
            <th class="px-4 py-2">Trámite</th>
            <th class="px-4 py-2">Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(cantidad, tramite) in resumen" :key="tramite">
            <td class="border px-4 py-2">{{ tramite }}</td>
            <td class="border px-4 py-2">{{ cantidad }}</td>
          </tr>
          <tr>
            <th class="border px-4 py-2">Total</th>
            <th class="border px-4 py-2">{{ total }}</th>
          </tr>
        </tbody>
      </table>
    </div>


    <!-- Gráfico resumen -->
    <div class="mb-6" style="height: 300px;">
      <Bar :data="chartData" :options="chartOptions" />
    </div>

    <!-- Detalle -->
   <!--  <div>
      <h2 class="text-xl mb-2">Detalle de Solicitudes</h2>
      <table class="min-w-full bg-white shadow rounded">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Trámite</th>
            <th class="px-4 py-2">Estado</th>
            <th class="px-4 py-2">Usuario</th>
            <th class="px-4 py-2">Creación</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sol in solicitudes" :key="sol.id">
            <td class="border px-4 py-2">{{ sol.id }}</td>
            <td class="border px-4 py-2">{{ sol.tramite.nombre }}</td>
            <td class="border px-4 py-2">{{ sol.estado_actual }}</td>
            <td class="border px-4 py-2">{{ sol.usuario ? sol.usuario.first_name : '' }}</td>
            <td class="border px-4 py-2">{{ sol.created_at }}</td>
          </tr>
        </tbody>
      </table>
    </div> -->
  </div>
</template>
<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

import { Bar } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  components: { Head, Bar },
  layout: Layout,
  props: {
    solicitudes: Array,
    resumenPorTramite: Object,
    totalSolicitudes: Number,
    filters: Object,
  },
  mounted() {
    console.log(this.resumenPorTramite)
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
    }
  },
  computed: {
    resumen() {
      return this.resumenPorTramite || {}
    },
    total() {
      return this.totalSolicitudes || 0
    },
    solicitudes() {
      return this.$props.solicitudes || []
    },
    chartData() {
      return {
        labels: Object.keys(this.resumen),
        datasets: [
          {
            label: 'Cantidad de solicitudes',
            backgroundColor: '#3b82f6',
            data: Object.values(this.resumen),
          },
        ],
      }
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true,
            ticks: { precision: 0 },
          },
        },
        plugins: {
          legend: { display: false },
          title: {
            display: true,
            text: 'Solicitudes por tipo de trámite',
          },
        },
      }
    },
  },
  methods: {
    submit() {
      this.$inertia.get('/reportes/por_tramites', this.form, {
        preserveState: true,
        preserveScroll: true,
      })
    },
    exportPdf() {
      const params = new URLSearchParams({
        ...this.form,
        export: 'pdf',
      }).toString()
      window.open(`/ventanilla_unica/public/reportes/por_tramites?${params}`, '_blank')
    },
  },
}
</script>
