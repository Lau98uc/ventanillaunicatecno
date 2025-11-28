<template>
  <div>
    <Head title="Documentos" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Título y botones opcionales -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/documentos/create">
          <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Documentos</h1>
      </div>

      <!-- Centro: Filtros (search, trashed, etc.) -->
      <div class="flex-1 flex justify-center">
        <SearchFilter v-model="form.search" class="w-full max-w-md text-sm" @reset="reset">
          <label class="block text-sm text-gray-700">Buscar:</label>
          <input v-model="form.search" type="text" placeholder="Nombre del documento..."
            class="input input-sm mt-1 w-full" />
        </SearchFilter>
      </div>

      <!-- Derecha: Paginación -->
      <div>
        <pagination :links="documentos.links">
          {{ documentos.from }}-{{ documentos.to }}/{{ documentos.total }}
        </pagination>
      </div>
    </div>

    <div class="rounded-md shadow overflow-x-auto">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead class="sticky top-0 bg-base-200 z-10">
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Nombre</th>
            <th class="pb-4 pt-6 px-6">Archivo</th>
            <th class="pb-4 pt-6 px-6">Solicitud</th>
            <th class="pb-4 pt-6 px-6">Tamaño</th>
            <th class="pb-4 pt-6 px-6">Fecha</th>
            <th class="pb-4 pt-6 px-6" colspan="2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="documento in documentos.data" :key="documento.id" class="hover">
            <td class="px-6 py-3 border-t">
              <Link :href="`/documentos/${documento.id}`" tabindex="-1">
                {{ documento.nombre }}
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/documentos/${documento.id}`" tabindex="-1">
                <span class="text-sm text-base-content opacity-70">{{ getFileName(documento.path) }}</span>
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link 
                v-if="documento.solicitud" 
                :href="`/solicitudes/${documento.solicitud.id}`" 
                tabindex="-1"
                class="link link-primary"
              >
                Solicitud #{{ documento.solicitud.id }}
              </Link>
              <span v-else class="text-base-content opacity-60">Sin solicitud</span>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/documentos/${documento.id}`" tabindex="-1">
                <span class="badge badge-outline">{{ formatFileSize(documento.size) }}</span>
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/documentos/${documento.id}`" tabindex="-1">
                {{ formatDate(documento.created_at) }}
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/documentos/${documento.id}/edit`" tabindex="-1" class="btn btn-xs btn-outline btn-primary">
                Editar
              </Link>
            </td>
            <td class="w-px border-t">
              <Link :href="`/documentos/${documento.id}`" tabindex="-1">
                <icon name="cheveron-right" class="w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="documentos.data.length === 0">
            <td class="px-6 py-3 border-t" colspan="7">No hay documentos.</td>
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
    documentos: Object,
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
        this.$inertia.get('/documentos', pickBy(this.form), { preserveState: true })
      }, 150)
    }
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    getFileName(path) {
      if (!path) return 'N/A'
      return path.split('/').pop() || 'Archivo'
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
        month: 'short',
        day: 'numeric'
      })
    }
  }
}
</script> 