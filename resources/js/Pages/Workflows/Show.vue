<template>
  <div class="p-4">
    <Head title="Detalle del Workflow" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/workflows" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Workflows
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Detalle del Workflow</h1>
      </div>
      <Link :href="`/workflows/${workflow.id}/edit`" class="btn btn-primary btn-sm">
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
            <icon name="flow" class="w-6 h-6" />
            Información General
          </h2>
          
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Nombre:</span>
              <span class="text-base-content font-medium">{{ workflow.nombre }}</span>
            </div>
            
            <div class="flex justify-between items-start">
              <span class="font-semibold text-base-content">Descripción:</span>
              <span class="text-base-content text-right max-w-xs">{{ workflow.descripcion || 'Sin descripción' }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Total de pasos:</span>
              <span class="badge badge-primary">{{ workflow.pasos?.length || 0 }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Total de transiciones:</span>
              <span class="badge badge-secondary">{{ workflow.transiciones?.length || 0 }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Creado:</span>
              <span class="text-base-content">{{ formatDate(workflow.created_at) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Última actualización:</span>
              <span class="text-base-content">{{ formatDate(workflow.updated_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Estadísticas -->
      <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
          <h2 class="card-title text-primary mb-4">
            <icon name="chart" class="w-6 h-6" />
            Estadísticas
          </h2>
          
          <div class="grid grid-cols-1 gap-4">
            <div class="stat bg-base-200 rounded-lg">
              <div class="stat-title text-base-content">Trámites asociados</div>
              <div class="stat-value text-primary">{{ workflow.tramites_count || 0 }}</div>
              <div class="stat-desc text-base-content opacity-60">Total de trámites que usan este workflow</div>
            </div>
            
            <div class="stat bg-base-200 rounded-lg">
              <div class="stat-title text-base-content">Solicitudes activas</div>
              <div class="stat-value text-success">{{ workflow.solicitudes_count || 0 }}</div>
              <div class="stat-desc text-base-content opacity-60">Solicitudes en proceso</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pasos del workflow -->
    <div class="mt-6 card bg-base-100 shadow-lg" v-if="workflow.pasos && workflow.pasos.length > 0">
      <div class="card-body">
        <h2 class="card-title text-primary mb-4">
          <icon name="list" class="w-6 h-6" />
          Pasos del Workflow
        </h2>
        
        <div class="space-y-4">
          <div 
            v-for="(paso, index) in workflow.pasos" 
            :key="paso.id"
            class="flex items-center gap-4 p-4 bg-base-200 rounded-lg"
          >
            <div class="flex-shrink-0 w-10 h-10 bg-primary text-primary-content rounded-full flex items-center justify-center text-lg font-bold">
              {{ index + 1 }}
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-base-content">{{ paso.nombre }}</h3>
              <p v-if="paso.descripcion" class="text-sm text-base-content opacity-70 mt-1">{{ paso.descripcion }}</p>
            </div>
            <div class="flex-shrink-0">
              <span class="badge badge-outline badge-primary">{{ paso.tipo || 'Paso' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Transiciones del workflow -->
    <div class="mt-6 card bg-base-100 shadow-lg" v-if="workflow.transiciones && workflow.transiciones.length > 0">
      <div class="card-body">
        <h2 class="card-title text-primary mb-4">
          <icon name="arrow-right" class="w-6 h-6" />
          Transiciones del Workflow
        </h2>
        
        <div class="space-y-3">
          <div 
            v-for="transicion in workflow.transiciones" 
            :key="`${transicion.origen}-${transicion.destino}`"
            class="flex items-center gap-4 p-3 bg-base-200 rounded-lg"
          >
            <div class="flex-1 text-center">
              <span class="badge badge-primary">Paso {{ transicion.origen }}</span>
            </div>
            <div class="flex-shrink-0">
              <icon name="arrow-right" class="w-6 h-6 text-primary" />
            </div>
            <div class="flex-1 text-center">
              <span class="badge badge-secondary">Paso {{ transicion.destino }}</span>
            </div>
            <div class="flex-shrink-0">
              <span class="badge badge-outline badge-accent">{{ transicion.accion }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Trámites asociados -->
    <div class="mt-6 card bg-base-100 shadow-lg" v-if="workflow.tramites && workflow.tramites.length > 0">
      <div class="card-body">
        <h2 class="card-title text-primary mb-4">
          <icon name="document" class="w-6 h-6" />
          Trámites Asociados
        </h2>
        
        <div class="overflow-x-auto">
          <table class="table table-zebra w-full">
            <thead>
              <tr>
                <th class="text-left">Código</th>
                <th class="text-left">Título</th>
                <th class="text-left">Estado</th>
                <th class="text-left">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tramite in workflow.tramites" :key="tramite.id">
                <td>
                  <Link :href="`/tramites/${tramite.id}`" class="link link-primary">
                    {{ tramite.codigo }}
                  </Link>
                </td>
                <td>{{ tramite.titulo }}</td>
                <td>
                  <span class="badge" :class="tramite.activo ? 'badge-success' : 'badge-error'">
                    {{ tramite.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td>
                  <Link :href="`/tramites/${tramite.id}`" class="btn btn-xs btn-outline btn-primary">
                    Ver
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Mensaje si no hay pasos -->
    <div class="mt-6 card bg-base-100 shadow-lg" v-if="!workflow.pasos || workflow.pasos.length === 0">
      <div class="card-body text-center py-8">
        <icon name="flow" class="w-16 h-16 mx-auto text-base-content opacity-30 mb-4" />
        <h3 class="text-lg font-semibold text-base-content mb-2">No hay pasos configurados</h3>
        <p class="text-base-content opacity-60 mb-4">Este workflow no tiene pasos definidos aún.</p>
        <Link :href="`/workflows/${workflow.id}/edit`" class="btn btn-primary">
          <icon name="edit" class="w-4 h-4 mr-2" />
          Configurar Pasos
        </Link>
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
    workflow: Object
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