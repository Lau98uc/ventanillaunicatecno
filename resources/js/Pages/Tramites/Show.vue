<template>
  <div class="p-4">
    <Head title="Detalle del Trámite" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/tramites" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Trámites
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Detalle del Trámite</h1>
      </div>
      <Link :href="`/tramites/${tramite.id}/edit`" class="btn btn-primary btn-sm">
        <icon name="edit" class="w-4 h-4 mr-2" />
        Editar
      </Link>
    </div>

    <!-- Información principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Información básica -->
      <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
          <h2 class="card-title text-primary mb-4">
            <icon name="document" class="w-6 h-6" />
            Información General
          </h2>
          
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Código:</span>
              <span class="badge badge-primary">{{ tramite.codigo }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Título:</span>
              <span class="text-base-content">{{ tramite.titulo }}</span>
            </div>
            
            <div class="flex justify-between items-start">
              <span class="font-semibold text-base-content">Descripción:</span>
              <span class="text-base-content text-right max-w-xs">{{ tramite.descripcion || 'Sin descripción' }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Workflow:</span>
              <Link 
                v-if="tramite.workflow" 
                :href="`/workflows/${tramite.workflow.id}`"
                class="link link-primary hover:link-secondary"
              >
                {{ tramite.workflow.nombre }}
              </Link>
              <span v-else class="text-base-content opacity-60">Sin workflow asignado</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Creado:</span>
              <span class="text-base-content">{{ formatDate(tramite.created_at) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Última actualización:</span>
              <span class="text-base-content">{{ formatDate(tramite.updated_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Información del workflow -->
      <div class="card bg-base-100 shadow-lg" v-if="flujo">
        <div class="card-body">
          <h2 class="card-title text-primary mb-4">
            <icon name="flow" class="w-6 h-6" />
            Flujo del Workflow
          </h2>
          
          <div v-if="flujo.pasos && flujo.pasos.length > 0" class="space-y-4">
            <h3 class="font-semibold text-base-content">Pasos del flujo:</h3>
            <div class="space-y-2">
              <div 
                v-for="(paso, index) in flujo.pasos" 
                :key="paso.id"
                class="flex items-center gap-3 p-3 bg-base-200 rounded-lg"
              >
                <div class="flex-shrink-0 w-8 h-8 bg-primary text-primary-content rounded-full flex items-center justify-center text-sm font-bold">
                  {{ index + 1 }}
                </div>
                <span class="text-base-content">{{ paso.nombre }}</span>
              </div>
            </div>
            
            <div v-if="flujo.transiciones && flujo.transiciones.length > 0" class="mt-6">
              <h3 class="font-semibold text-base-content mb-3">Transiciones:</h3>
              <div class="space-y-2">
                <div 
                  v-for="transicion in flujo.transiciones" 
                  :key="`${transicion.origen}-${transicion.destino}`"
                  class="flex items-center gap-2 p-2 bg-base-200 rounded text-sm"
                >
                  <span class="text-base-content">Paso {{ transicion.origen }}</span>
                  <icon name="arrow-right" class="w-4 h-4 text-primary" />
                  <span class="text-base-content">Paso {{ transicion.destino }}</span>
                  <span class="badge badge-outline badge-primary ml-auto">{{ transicion.accion }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8">
            <icon name="flow" class="w-12 h-12 mx-auto text-base-content opacity-30 mb-4" />
            <p class="text-base-content opacity-60">No hay pasos configurados en este workflow</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Estadísticas o información adicional -->
    <div class="mt-6 card bg-base-100 shadow-lg">
      <div class="card-body">
        <h2 class="card-title text-primary mb-4">
          <icon name="chart" class="w-6 h-6" />
          Información Adicional
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="stat bg-base-200 rounded-lg">
            <div class="stat-title text-base-content">Solicitudes</div>
            <div class="stat-value text-primary">{{ tramite.solicitudes_count || 0 }}</div>
            <div class="stat-desc text-base-content opacity-60">Total de solicitudes</div>
          </div>
          
          <div class="stat bg-base-200 rounded-lg">
            <div class="stat-title text-base-content">Estado</div>
            <div class="stat-value text-success">{{ tramite.activo ? 'Activo' : 'Inactivo' }}</div>
            <div class="stat-desc text-base-content opacity-60">Estado del trámite</div>
          </div>
          
          <div class="stat bg-base-200 rounded-lg">
            <div class="stat-title text-base-content">Creado por</div>
            <div class="stat-value text-primary">{{ tramite.usuario?.name || 'Sistema' }}</div>
            <div class="stat-desc text-base-content opacity-60">Usuario creador</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Icon from '@/Shared/Icon.vue'

export default {
  components: {
    Head,
    Link,
    Icon
  },
  props: {
    tramite: Object,
    flujo: Object
  },
  layout: Layout,
  methods: {
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>
