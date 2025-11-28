<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Detalle del Trámite: {{ tramite.codigo }}</h1>

    <!-- Información general -->
    <section class="mb-6">
      <h2 class="text-xl font-semibold">Información general</h2>
      <p><strong>Título:</strong> {{ tramite.titulo }}</p>
      <p><strong>Estado actual:</strong> {{ tramite.estado_actual ?? 'Sin estado' }}</p>
      <p><strong>Descripción:</strong> {{ tramite.descripcion }}</p>
      <p><strong>Solicitante:</strong> {{ tramite.usuario?.name ?? 'No disponible' }}</p>
      <p><strong>Creado el:</strong> {{ tramite.created_at }}</p>
    </section>

    <!-- Acciones disponibles -->
    <section class="mb-6" v-if="tramite.acciones && tramite.acciones.length">
      <h2 class="text-xl font-semibold mb-2">Acciones disponibles</h2>
      <div class="flex flex-wrap gap-2">
        <button v-for="accion in tramite.acciones" :key="accion" class="btn btn-primary btn-sm"
          @click="ejecutarAccion(accion)">
          {{ accion }}
        </button>
      </div>
    </section>

    <!-- Historial -->
    <!-- <section>
      <h2 class="text-xl font-semibold mb-2">Historial de Ejecuciones</h2>
      <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
          <tr class="bg-gray-100">
            <th class="border border-gray-300 px-2 py-1">Paso</th>
            <th class="border border-gray-300 px-2 py-1">Usuario</th>
            <th class="border border-gray-300 px-2 py-1">Estado</th>
            <th class="border border-gray-300 px-2 py-1">Fecha inicio</th>
            <th class="border border-gray-300 px-2 py-1">Fecha fin</th>
            <th class="border border-gray-300 px-2 py-1">Observación</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ejecucion in tramite.historial" :key="ejecucion.id" class="hover:bg-gray-50">
            <td class="border border-gray-300 px-2 py-1">
              {{ ejecucion.paso ?? 'N/A' }}
            </td>
            <td class="border border-gray-300 px-2 py-1">
              {{ ejecucion.usuario ?? 'N/A' }}
            </td>
            <td class="border border-gray-300 px-2 py-1">
              {{ ejecucion.estado }}
            </td>
            <td class="border border-gray-300 px-2 py-1">
              {{ ejecucion.fecha_inicio }}
            </td>
            <td class="border border-gray-300 px-2 py-1">
              {{ ejecucion.fecha_fin }}
            </td>
            <td class="border border-gray-300 px-2 py-1">
              {{ ejecucion.observacion ?? '-' }}
            </td>
          </tr>
          <tr v-if="!tramite.historial.length">
            <td class="border border-gray-300 px-2 py-1 text-center" colspan="5">
              No hay historial disponible
            </td>
          </tr>
        </tbody>
      </table>
    </section> -->

    <!-- Historial como línea de tiempo -->
    <!-- Historial como línea de tiempo mejorado -->
    <section>
      <h2 class="text-xl font-semibold mb-4">Historial de Ejecuciones</h2>

      <ul class="timeline timeline-vertical">
        <li v-for="ejecucion in tramite.historial" :key="ejecucion.id">
          <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <div class="w-2 h-2 bg-primary rounded-full"></div>
            <span>{{ ejecucion.fecha_inicio }}</span>
          </div>


          <div class="timeline-end mb-10">
            <div class="card bg-base-100 shadow-md border border-base-200">
              <div class="card-body p-4 space-y-1">

                <!-- Paso + Ícono de estado -->
                <div class="flex items-center gap-2">
                  <h3 class="text-lg font-semibold text-primary mb-0">{{ ejecucion.paso ?? 'Paso desconocido' }}</h3>
                  <div class="w-6 h-6 flex items-center justify-center rounded-full" :class="{
                    'bg-green-500 text-white': ejecucion.estado === 'completado',
                    'bg-red-500 text-white': ejecucion.estado === 'rechazado',
                    'bg-yellow-400 text-white': ejecucion.estado === 'pendiente'
                  }">
                    <svg v-if="ejecucion.estado === 'completado'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                      fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else-if="ejecucion.estado === 'rechazado'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                      fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>

                <!-- Detalles -->
                <p class="text-sm"><strong>Usuario:</strong> {{ ejecucion.usuario ?? 'N/A' }}</p>
                <p class="text-sm"><strong>Estado:</strong>
                  <span
                    :class="ejecucion.estado === 'completado' ? 'text-green-600' : ejecucion.estado === 'rechazado' ? 'text-red-600' : 'text-yellow-600'">
                    {{ ejecucion.estado }}
                  </span>
                </p>
                <p class="text-sm"><strong>Inicio:</strong> {{ ejecucion.fecha_inicio }}</p>
                <p class="text-sm"><strong>Fin:</strong> {{ ejecucion.fecha_fin ?? '-' }}</p>
                <p class="text-sm" v-if="ejecucion.observacion"><strong>Observación:</strong> {{ ejecucion.observacion
                  }}</p>
              </div>
            </div>
          </div>
        </li>

        <li v-if="!tramite.historial.length">
          <div class="timeline-middle">
            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
          </div>
          <div class="timeline-end">
            <div class="card bg-base-100 shadow">
              <div class="card-body p-4">
                <p>No hay historial disponible</p>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </section>



  </div>
</template>

<script>
import Layout from '@/Shared/Layout.vue'
import { router } from '@inertiajs/vue3'

export default {
  props: {
    tramite: Object,
  },
  layout: Layout,
  methods: {
    ejecutarAccion(accion) {

      if (confirm(`¿Deseas ejecutar la acción "${accion}"?`)) {
        router.post(`/solicitudes/${this.tramite.id}/acciones`, {
          accion: accion,
        }, {
          onSuccess: () => {
            router.reload()
          }
        })
      }
    }
  }
}
</script>
