<template>
  <div>

    <Head title="Solicitudes" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Título y botones opcionales -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/solicitudes/create">
        <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Solicitudes</h1>
      </div>

      <!-- Centro: Filtros (search, trashed, etc.) -->
      <div class="flex-1 flex justify-center">
        <SearchFilter v-model="form.search" class="w-full max-w-md text-sm" @reset="reset">
          <label class="block text-sm text-gray-700">Buscar:</label>
          <input v-model="form.search" type="text" placeholder="Código o solicitante..."
            class="input input-sm mt-1 w-full" />
        </SearchFilter>
      </div>

      <!-- Derecha: Paginación -->
      <div>
        <pagination :links="solicitudes.links">
          {{ solicitudes.from }}-{{ solicitudes.to }}/{{ solicitudes.total }}
        </pagination>
      </div>
    </div>


    <div class="rounded-md shadow overflow-x-auto">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead class="sticky top-0 bg-base-200 z-10">
          <tr class="text-left font-bold">

            <th class="pb-4 pt-6 px-6">ID</th>
            <th class="pb-4 pt-6 px-6">Trámite</th>
            <th class="pb-4 pt-6 px-6">Estado Actual</th>
            <th class="pb-4 pt-6 px-6">Solicitante</th>
            <th class="pb-4 pt-6 px-6" colspan="2">Fecha</th>

          </tr>
        </thead>
        <tbody>
          <tr v-for="item in solicitudes.data" :key="item.id" class="hover">

            <td class="border-t px-6 py-3">
              <Link :href="`/solicitudes/${item.id}`" tabindex="-1">{{ item.id }}</Link>
            </td>
            <td class="border-t px-6 py-3">
              <Link :href="`/solicitudes/${item.id}`" tabindex="-1">{{ item.tramite }}</Link>
            </td>
            <td class="border-t px-6 py-3">
              <Link class="flex items-center" :href="`/solicitudes/${item.id}`">
              {{ item.estado_actual }}</Link>
            </td>
            <td class="border-t px-6 py-3">
              <Link class="flex items-center" :href="`/solicitudes/${item.id}`">{{ item.usuario.name }}</Link>
            </td>
            <td class="border-t px-6 py-3">
              <Link class="flex items-center" :href="`/solicitudes/${item.id}`">{{ item.created_at }}</Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex px-4" :href="`/solicitudes/${item.id}`" tabindex="-1">
              <icon name="cheveron-right" class="w-6 h-6 fill-gray-400" />
              </Link>
            </td>

          </tr>
          <tr v-if="solicitudes.data.length === 0">
            <td class="px-6 py-3 border-t" colspan="6">No hay solicitudes para tu unidad.</td>
          </tr>
        </tbody>
      </table>
    </div>


  </div>
</template>

<script>
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import Pagination from '@/Shared/Pagination.vue'
import SearchFilter from '@/Shared/SearchFilter.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  props: {
    filters: Object,
    solicitudes: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
      },
    }
  },
  layout: Layout,
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        router.get('/solicitudes', pickBy(this.form), { preserveState: true })
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
