<template>
  <div>
    <Head title="Workflows" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Título y botones opcionales -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/workflows/create">
          <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Workflows</h1>
      </div>

      <!-- Centro: Filtros (search, trashed, etc.) -->
      <div class="flex-1 flex justify-center">
        <SearchFilter v-model="form.search" class="w-full max-w-md text-sm" @reset="reset">
          <label class="block text-sm text-gray-700">Buscar:</label>
          <input v-model="form.search" type="text" placeholder="Nombre o descripción..."
            class="input input-sm mt-1 w-full" />
        </SearchFilter>
      </div>

      <!-- Derecha: Paginación -->
      <div>
        <pagination :links="workflows.links">
          {{ workflows.from }}-{{ workflows.to }}/{{ workflows.total }}
        </pagination>
      </div>
    </div>

    <div class="rounded-md shadow overflow-x-auto">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead class="sticky top-0 bg-base-200 z-10">
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Nombre</th>
            <th class="pb-4 pt-6 px-6">Descripción</th>
            <th class="pb-4 pt-6 px-6">Pasos</th>
            <th class="pb-4 pt-6 px-6">Trámites</th>
            <th class="pb-4 pt-6 px-6" colspan="2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="workflow in workflows.data" :key="workflow.id" class="hover">
            <td class="px-6 py-3 border-t">
              <Link :href="`/workflows/${workflow.id}`" tabindex="-1">
                {{ workflow.nombre }}
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/workflows/${workflow.id}`" tabindex="-1">
                {{ workflow.descripcion ? (workflow.descripcion.length > 50 ? workflow.descripcion.substring(0, 50) + '...' : workflow.descripcion) : 'Sin descripción' }}
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/workflows/${workflow.id}`" tabindex="-1">
                <span class="badge badge-primary">{{ workflow.pasos_count || 0 }}</span>
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/workflows/${workflow.id}`" tabindex="-1">
                <span class="badge badge-secondary">{{ workflow.tramites_count || 0 }}</span>
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/workflows/${workflow.id}/edit`" tabindex="-1" class="btn btn-xs btn-outline btn-primary">
                Editar
              </Link>
            </td>
            <td class="w-px border-t">
              <Link :href="`/workflows/${workflow.id}`" tabindex="-1">
                <icon name="cheveron-right" class="w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="workflows.data.length === 0">
            <td class="px-6 py-3 border-t" colspan="6">No hay workflows.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Shared/Pagination.vue'
import Layout from '@/Shared/Layout.vue'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import Icon from '@/Shared/Icon.vue'
import SearchFilter from '@/Shared/SearchFilter.vue'

export default {
  components: {
    Link,
    Pagination,
    Icon,
    Head,
    SearchFilter
  },
  props: {
    filters: Object,
    workflows: Object,
  },
  layout: Layout,
  data() {
    return {
      form: {
        search: this.filters.search ?? '',
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/workflows', pickBy(this.form), { preserveState: true })
      }, 150)
    }
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    }
  }
}
</script> 