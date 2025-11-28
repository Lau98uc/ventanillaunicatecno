<template>
  <div>

    <Head title="unidades" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Título y botones opcionales -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/unidades/create">
        <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Unidades</h1>
      </div>

      <!-- Centro: Filtros (search, trashed, etc.) -->
      <div class="flex-1 flex justify-center">
        <!-- <SearchFilter v-model="form.search" class="w-full max-w-md text-sm" @reset="reset">
          <label class="block text-sm text-gray-700">Buscar:</label>
          <input v-model="form.search" type="text" placeholder="Código o solicitante..."
            class="input input-sm mt-1 w-full" />
        </SearchFilter> -->
      </div>

      <!-- Derecha: Paginación -->
      <div>
        <pagination :links="unidades.links">
          {{ unidades.from }}-{{ unidades.to }}/{{ unidades.total }}
        </pagination>
      </div>
    </div>



    <div class="rounded-md shadow overflow-x-auto">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead class="sticky top-0 bg-base-200 z-10">
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Name</th>
            <th class="pb-4 pt-6 px-6">City</th>
            <th class="pb-4 pt-6 px-6" colspan="2">Phone</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="unidad in unidades.data" :key="unidad.id" class="hover">
            <td class="px-6 py-3 border-t">
              <Link :href="`/unidades/${unidad.id}/edit`">
              {{ unidad.name }}
              <icon v-if="unidad.deleted_at" name="trash" class="w-4 h-4 fill-gray-400" />
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/unidades/${unidad.id}/edit`" tabindex="-1">
              {{ unidad.city || '-' }}
              </Link>
            </td>
            <td class="px-6 py-3 border-t">
              <Link :href="`/unidades/${unidad.id}/edit`" tabindex="-1">
              {{ unidad.phone || '-' }}
              </Link>
            </td>
            <td class="w-px border-t">
              <Link  :href="`/unidades/${unidad.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="unidades.data.length === 0">
            <td class="px-6 py-3 text-center" colspan="4">No unidades found.</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout.vue'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination.vue'
import SearchFilter from '@/Shared/SearchFilter.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    unidades: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/unidades', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
