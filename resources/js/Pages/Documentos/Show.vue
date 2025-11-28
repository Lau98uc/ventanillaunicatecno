<template>
  <div class="p-4">
    <Head title="Detalle del Documento" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/documentos" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Documentos
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Detalle del Documento</h1>
      </div>
      <Link :href="`/documentos/${documento.id}/edit`" class="btn btn-primary btn-sm">
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
              <span class="font-semibold text-base-content">Nombre:</span>
              <span class="text-base-content font-medium">{{ documento.nombre }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Archivo:</span>
              <span class="text-base-content">{{ getFileName(documento.path) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Ruta:</span>
              <span class="text-base-content text-sm opacity-70 max-w-xs truncate">{{ documento.path }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Tamaño:</span>
              <span class="badge badge-outline">{{ formatFileSize(documento.size) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Tipo de archivo:</span>
              <span class="badge badge-primary">{{ getFileExtension(documento.path) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Creado:</span>
              <span class="text-base-content">{{ formatDate(documento.created_at) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Última actualización:</span>
              <span class="text-base-content">{{ formatDate(documento.updated_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Información de la solicitud -->
      <div class="card bg-base-100 shadow-lg" v-if="documento.solicitud">
        <div class="card-body">
          <h2 class="card-title text-primary mb-4">
            <icon name="clipboard" class="w-6 h-6" />
            Solicitud Asociada
          </h2>
          
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">ID de Solicitud:</span>
              <Link 
                :href="`/solicitudes/${documento.solicitud.id}`"
                class="link link-primary font-medium"
              >
                #{{ documento.solicitud.id }}
              </Link>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Nombre:</span>
              <span class="text-base-content">{{ documento.solicitud.nombre || 'Sin nombre' }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Estado:</span>
              <span class="badge" :class="getEstadoBadgeClass(documento.solicitud.estado_actual)">
                {{ documento.solicitud.estado_actual || 'Sin estado' }}
              </span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Solicitante:</span>
              <span class="text-base-content">{{ documento.solicitud.usuario?.name || 'N/A' }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-base-content">Trámite:</span>
              <Link 
                v-if="documento.solicitud.tramite"
                :href="`/tramites/${documento.solicitud.tramite.id}`"
                class="link link-primary"
              >
                {{ documento.solicitud.tramite.nombre }}
              </Link>
              <span v-else class="text-base-content opacity-60">Sin trámite</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sin solicitud asociada -->
      <div class="card bg-base-100 shadow-lg" v-else>
        <div class="card-body">
          <h2 class="card-title text-primary mb-4">
            <icon name="clipboard" class="w-6 h-6" />
            Solicitud Asociada
          </h2>
          
          <div class="text-center py-8">
            <icon name="clipboard" class="w-12 h-12 mx-auto text-base-content opacity-30 mb-4" />
            <p class="text-base-content opacity-60">Este documento no está asociado a ninguna solicitud</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Vista previa del archivo -->
    <div class="mt-6 card bg-base-100 shadow-lg">
      <div class="card-body">
        <h2 class="card-title text-primary mb-4">
          <icon name="eye" class="w-6 h-6" />
          Vista Previa
        </h2>
        
        <div class="text-center py-8">
          <div v-if="isImageFile(documento.path)" class="space-y-4">
            <img 
              :src="documento.path" 
              :alt="documento.nombre"
              class="max-w-full h-auto max-h-96 mx-auto rounded-lg shadow-lg"
              @error="handleImageError"
            />
            <p class="text-sm text-base-content opacity-70">{{ documento.nombre }}</p>
          </div>
          
          <div v-else-if="isPdfFile(documento.path)" class="space-y-4">
            <div class="bg-base-200 rounded-lg p-8">
              <icon name="document" class="w-16 h-16 mx-auto text-primary mb-4" />
              <p class="text-base-content font-medium">{{ documento.nombre }}</p>
              <p class="text-sm text-base-content opacity-70">Documento PDF</p>
            </div>
            <a 
              :href="documento.path" 
              target="_blank" 
              class="btn btn-primary"
            >
              <icon name="download" class="w-4 h-4 mr-2" />
              Ver PDF
            </a>
          </div>
          
          <div v-else class="space-y-4">
            <div class="bg-base-200 rounded-lg p-8">
              <icon name="document" class="w-16 h-16 mx-auto text-base-content opacity-50 mb-4" />
              <p class="text-base-content font-medium">{{ documento.nombre }}</p>
              <p class="text-sm text-base-content opacity-70">{{ getFileExtension(documento.path) }} - {{ formatFileSize(documento.size) }}</p>
            </div>
            <a 
              :href="documento.path" 
              target="_blank" 
              class="btn btn-outline btn-primary"
            >
              <icon name="download" class="w-4 h-4 mr-2" />
              Descargar
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Estadísticas -->
    <div class="mt-6 card bg-base-100 shadow-lg">
      <div class="card-body">
        <h2 class="card-title text-primary mb-4">
          <icon name="chart" class="w-6 h-6" />
          Información Adicional
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="stat bg-base-200 rounded-lg">
            <div class="stat-title text-base-content">Tamaño del archivo</div>
            <div class="stat-value text-primary text-lg">{{ formatFileSize(documento.size) }}</div>
            <div class="stat-desc text-base-content opacity-60">Tamaño en disco</div>
          </div>
          
          <div class="stat bg-base-200 rounded-lg">
            <div class="stat-title text-base-content">Tipo de archivo</div>
            <div class="stat-value text-secondary text-lg">{{ getFileExtension(documento.path) }}</div>
            <div class="stat-desc text-base-content opacity-60">Extensión del archivo</div>
          </div>
          
          <div class="stat bg-base-200 rounded-lg">
            <div class="stat-title text-base-content">Fecha de creación</div>
            <div class="stat-value text-accent text-lg">{{ formatShortDate(documento.created_at) }}</div>
            <div class="stat-desc text-base-content opacity-60">Cuando se subió</div>
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
    documento: Object
  },
  layout: Layout,
  methods: {
    getFileName(path) {
      if (!path) return 'N/A'
      return path.split('/').pop() || 'Archivo'
    },
    getFileExtension(path) {
      if (!path) return 'N/A'
      const fileName = this.getFileName(path)
      const extension = fileName.split('.').pop()
      return extension ? extension.toUpperCase() : 'SIN EXTENSIÓN'
    },
    formatFileSize(bytes) {
      if (!bytes) return 'N/A'
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      if (bytes === 0) return '0 Bytes'
      const i = Math.floor(Math.log(bytes) / Math.log(1024))
      return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    formatShortDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },
    isImageFile(path) {
      if (!path) return false
      const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']
      const extension = this.getFileExtension(path).toLowerCase()
      return imageExtensions.includes(extension)
    },
    isPdfFile(path) {
      if (!path) return false
      const extension = this.getFileExtension(path).toLowerCase()
      return extension === 'pdf'
    },
    getEstadoBadgeClass(estado) {
      const classes = {
        'pendiente': 'badge-warning',
        'en_proceso': 'badge-info',
        'completado': 'badge-success',
        'rechazado': 'badge-error',
        'cancelado': 'badge-neutral'
      }
      return classes[estado] || 'badge-outline'
    },
    handleImageError(event) {
      event.target.style.display = 'none'
      event.target.nextElementSibling.textContent = 'Error al cargar la imagen'
    }
  }
}
</script> 