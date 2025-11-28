<template>
  <div class="p-4">
    <Head title="Editar Documento" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/documentos" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Documentos
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Editar Documento</h1>
      </div>
      <Link :href="`/documentos/${documento.id}`" class="btn btn-outline btn-primary btn-sm">
        <icon name="eye" class="w-4 h-4 mr-2" />
        Ver Detalle
      </Link>
    </div>

    <!-- Formulario -->
    <div class="max-w-2xl">
      <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="space-y-6">
              <!-- Nombre -->
              <div>
                <label for="nombre" class="block text-sm font-medium text-base-content mb-2">
                  Nombre del Documento *
                </label>
                <input
                  id="nombre"
                  v-model="form.nombre"
                  type="text"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': form.errors.nombre }"
                  placeholder="Ej: Documento de Identidad"
                  required
                />
                <div v-if="form.errors.nombre" class="text-error text-sm mt-1">
                  {{ form.errors.nombre }}
                </div>
              </div>

              <!-- Archivo -->
              <div>
                <label for="path" class="block text-sm font-medium text-base-content mb-2">
                  Ruta del Archivo *
                </label>
                <input
                  id="path"
                  v-model="form.path"
                  type="text"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': form.errors.path }"
                  placeholder="Ej: /storage/documentos/archivo.pdf"
                  required
                />
                <div v-if="form.errors.path" class="text-error text-sm mt-1">
                  {{ form.errors.path }}
                </div>
                <p class="text-xs text-base-content opacity-60 mt-1">
                  Ingresa la ruta completa del archivo en el servidor
                </p>
              </div>

              <!-- Solicitud asociada -->
              <div>
                <label for="solicitud_id" class="block text-sm font-medium text-base-content mb-2">
                  Solicitud Asociada
                </label>
                <select
                  id="solicitud_id"
                  v-model="form.solicitud_id"
                  class="select select-bordered w-full"
                  :class="{ 'select-error': form.errors.solicitud_id }"
                >
                  <option value="">Seleccionar solicitud (opcional)</option>
                  <option v-for="solicitud in solicitudes" :key="solicitud.id" :value="solicitud.id">
                    Solicitud #{{ solicitud.id }} - {{ solicitud.nombre || 'Sin nombre' }}
                  </option>
                </select>
                <div v-if="form.errors.solicitud_id" class="text-error text-sm mt-1">
                  {{ form.errors.solicitud_id }}
                </div>
              </div>

              <!-- Información actual -->
              <div class="bg-base-200 rounded-lg p-4">
                <h3 class="font-semibold text-base-content mb-3">Información Actual</h3>
                <div class="space-y-2 text-sm">
                  <div>
                    <span class="text-base-content opacity-70">Archivo actual:</span>
                    <span class="ml-2 font-medium">{{ getFileName(documento.path) }}</span>
                  </div>
                  <div>
                    <span class="text-base-content opacity-70">Tamaño:</span>
                    <span class="badge badge-outline ml-2">{{ formatFileSize(documento.size) }}</span>
                  </div>
                  <div v-if="documento.solicitud">
                    <span class="text-base-content opacity-70">Solicitud asociada:</span>
                    <Link 
                      :href="`/solicitudes/${documento.solicitud.id}`"
                      class="link link-primary ml-2"
                    >
                      Solicitud #{{ documento.solicitud.id }}
                    </Link>
                  </div>
                </div>
              </div>

              <!-- Botones -->
              <div class="flex justify-end gap-3">
                <Link href="/documentos" class="btn btn-outline">
                  Cancelar
                </Link>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="form.processing"
                >
                  <icon v-if="form.processing" name="loading" class="w-4 h-4 mr-2 animate-spin" />
                  {{ form.processing ? 'Actualizando...' : 'Actualizar Documento' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Icon from '@/Shared/Icon.vue'

export default {
  components: {
    Head,
    Link,
    Icon
  },
  props: {
    documento: Object,
    solicitudes: {
      type: Array,
      default: () => []
    }
  },
  layout: Layout,
  setup(props) {
    const form = useForm({
      nombre: props.documento.nombre,
      path: props.documento.path,
      solicitud_id: props.documento.solicitud_id || ''
    })

    const submit = () => {
      form.put(`/documentos/${props.documento.id}`)
    }

    const getFileName = (path) => {
      if (!path) return 'N/A'
      return path.split('/').pop() || 'Archivo'
    }

    const formatFileSize = (bytes) => {
      if (!bytes) return 'N/A'
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      if (bytes === 0) return '0 Bytes'
      const i = Math.floor(Math.log(bytes) / Math.log(1024))
      return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
    }

    return {
      form,
      submit,
      getFileName,
      formatFileSize
    }
  }
}
</script> 