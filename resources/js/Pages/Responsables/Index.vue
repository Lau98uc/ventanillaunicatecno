<template>
  <div>

    <Head title="responsables" />

    <div class="flex items-center p-2 md:p-2 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Botón "Nuevo" y título -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/responsables/create">
        <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">responsables</h1>
      </div>

      <!-- Centro: Filtro -->
      <div class="flex-1 flex justify-center">
        <search-filter v-model="form.search" class="w-full max-w-md text-sm" @reset="reset">

          <label class="block text-sm text-gray-700">Trashed:</label>
          <select v-model="form.trashed" class="select select-sm mt-1 w-full">
            <option :value="null" />
            <option value="with">With Trashed</option>
            <option value="only">Only Trashed</option>
          </select>

        </search-filter>
      </div>

      <!-- Derecha: Botón placeholder -->
      <div>

        <pagination :links="responsables.links" />

      </div>
    </div>


    <div class="rounded-md shadow">
      <!-- Encabezado fijo -->
      <div class="overflow-x-auto">
        <table class="table table-xs table-zebra w-full whitespace-nowrap">
          <thead class="sticky top-0 bg-base-200 z-10">
            <tr class="text-left font-bold">
              <th class="pb-4 pt-6 px-6">Name</th>
              <th class="pb-4 pt-6 px-6">Unidad</th>
              <th class="pb-4 pt-6 px-6">City</th>
              <th class="pb-4 pt-6 px-6" colspan="2">Phone</th>
            </tr>
          </thead>
        </table>
      </div>

      <!-- Cuerpo con scroll vertical -->
      <div>
        <table class="table table-xs table-zebra w-full whitespace-nowrap">
          <tbody>
            <tr v-for="responsable in responsables.data" :key="responsable.id" class="hover">
              <td class="border-t px-6 py-3">
                <Link class="flex items-center focus:text-indigo-500" :href="`/responsables/${responsable.id}/edit`">
                {{ responsable.name }}
                <icon v-if="responsable.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                </Link>
              </td>
              <td class="border-t px-6 py-3">
                <Link class="flex items-center" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
                <div v-if="responsable.unidad">
                  {{ responsable.unidad.name }}
                </div>
                </Link>
              </td>
              <td class="border-t px-6 py-3">
                <Link class="flex items-center" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
                {{ responsable.city }}
                </Link>
              </td>
              <td class="border-t px-6 py-3">
                <Link class="flex items-center" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
                {{ responsable.phone }}
                </Link>
              </td>
              <td class="border-t w-px px-3">
                <Link class="flex items-center" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                </Link>
              </td>
            </tr>
            <tr v-if="responsables.data.length === 0">
              <td class="px-6 py-3 border-t" colspan="5">
                No responsables found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
    responsables: Object,
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
        this.$inertia.get('/responsables', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
  mounted() {
    console.log(this.responsables)
  }

}
</script>
