<template>
  <div class="p-4">
    <Head title="Crear Workflow" />

    <!-- Header con navegación -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <Link href="/workflows" class="btn btn-outline btn-primary btn-sm">
          <icon name="arrow-left" class="w-4 h-4 mr-2" />
          Volver a Workflows
        </Link>
        <h1 class="text-2xl font-bold text-base-content">Crear Nuevo Workflow</h1>
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
                  {{ form.processing ? 'Creando...' : 'Crear Workflow' }}
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
  layout: Layout,
  setup() {
    const form = useForm({
      nombre: '',
      descripcion: ''
    })

    const submit = () => {
      form.post('/workflows', {
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