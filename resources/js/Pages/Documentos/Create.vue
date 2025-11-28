<template>
  <div class="p-4">
    <Head title="Crear Documento" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/documentos" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Documentos
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Crear Nuevo Documento</h1>
      </div>
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
                  {{ form.processing ? 'Creando...' : 'Crear Documento' }}
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
    solicitudes: {
      type: Array,
      default: () => []
    }
  },
  layout: Layout,
  setup() {
    const form = useForm({
      nombre: '',
      path: '',
      solicitud_id: ''
    })

    const submit = () => {
      form.post('/documentos', {
        onSuccess: () => {
          form.reset()
        }
      })
    }

    return {
      form,
      submit
    }
  }
}
</script> 