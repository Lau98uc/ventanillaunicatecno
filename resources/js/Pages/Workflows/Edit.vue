<template>
  <div class="p-4">
    <Head title="Editar Workflow" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/workflows" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Workflows
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Editar Workflow</h1>
      </div>
      <Link :href="`/workflows/${workflow.id}`" class="btn btn-outline btn-primary btn-sm">
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
                  Nombre del Workflow *
                </label>
                <input
                  id="nombre"
                  v-model="form.nombre"
                  type="text"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': form.errors.nombre }"
                  placeholder="Ej: Aprobación de Documentos"
                  required
                />
                <div v-if="form.errors.nombre" class="text-error text-sm mt-1">
                  {{ form.errors.nombre }}
                </div>
              </div>

              <!-- Descripción -->
              <div>
                <label for="descripcion" class="block text-sm font-medium text-base-content mb-2">
                  Descripción
                </label>
                <textarea
                  id="descripcion"
                  v-model="form.descripcion"
                  rows="4"
                  class="textarea textarea-bordered w-full"
                  :class="{ 'textarea-error': form.errors.descripcion }"
                  placeholder="Describe el propósito y funcionamiento de este workflow..."
                ></textarea>
                <div v-if="form.errors.descripcion" class="text-error text-sm mt-1">
                  {{ form.errors.descripcion }}
                </div>
              </div>

              <!-- Información de pasos y transiciones -->
              <div class="bg-base-200 rounded-lg p-4">
                <h3 class="font-semibold text-base-content mb-3">Configuración del Flujo</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-base-content opacity-70">Pasos configurados:</span>
                    <span class="badge badge-primary ml-2">{{ workflow.pasos?.length || 0 }}</span>
                  </div>
                  <div>
                    <span class="text-base-content opacity-70">Transiciones:</span>
                    <span class="badge badge-secondary ml-2">{{ workflow.transiciones?.length || 0 }}</span>
                  </div>
                </div>
                <p class="text-xs text-base-content opacity-60 mt-2">
                  Los pasos y transiciones se configuran en la vista de detalle del workflow.
                </p>
              </div>

              <!-- Botones -->
              <div class="flex justify-end gap-3">
                <Link href="/workflows" class="btn btn-outline">
                  Cancelar
                </Link>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="form.processing"
                >
                  <icon v-if="form.processing" name="loading" class="w-4 h-4 mr-2 animate-spin" />
                  {{ form.processing ? 'Actualizando...' : 'Actualizar Workflow' }}
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
    workflow: Object
  },
  layout: Layout,
  setup(props) {
    const form = useForm({
      nombre: props.workflow.nombre,
      descripcion: props.workflow.descripcion || ''
    })

    const submit = () => {
      form.put(`/workflows/${props.workflow.id}`)
    }

    return {
      form,
      submit
    }
  }
}
</script> 