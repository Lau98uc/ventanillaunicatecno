<template>
  <div>

    <Head title="Roles" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Botón "Nuevo" y título -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/roles/create">
        <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Roles</h1>
      </div>

      <!-- Centro: Navegación de seguridad -->
      <div class="flex-1 flex justify-center">
        <div class="flex items-center gap-4">
          <Link href="/users" class="btn btn-sm btn-outline btn-primary">
            Usuarios
          </Link>
          <Link href="/roles" class="btn btn-sm btn-outline btn-secondary">
            Roles
          </Link>
          <Link href="/permissions" class="btn btn-sm btn-outline btn-accent">
            Permisos
          </Link>
        </div>
      </div>

      <!-- Derecha: Filtro -->
      <div class="flex justify-end">
        <search-filter v-model="form.search" class="w-full max-w-md text-sm" @reset="reset">
          <label class="block text-gray-700">Buscar:</label>
          <input v-model="form.search" type="text" class="input input-sm mt-1 w-full"
            placeholder="Buscar rol por nombre" />
        </search-filter>
      </div>
    </div>

    <div class="rounded-md shadow overflow-x-auto">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead class="sticky top-0 bg-base-200 z-10">
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Nombre</th>
            <th class="pb-4 pt-6 px-6">Permisos</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="role in roles.data" :key="role.id" class="hover">
            <td class="border-t px-6 py-3">
              <Link class="flex items-center focus:text-indigo-500" :href="`/roles/${role.id}/edit`">
              {{ role.name }}
              </Link>
            </td>
            <td class="border-t px-6 py-3">
              <ul class="list-disc list-inside text-sm max-w-xs truncate">
                <li v-for="perm in role.permissions" :key="perm.id" :title="perm.name">
                  {{ perm.name }}
                </li>
              </ul>
            </td>
          </tr>
          <tr v-if="roles.data.length === 0">
            <td colspan="2" class="text-center py-4 text-gray-500">
              No hay roles registrados
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4 flex justify-center">
      <pagination :links="roles.links">
        {{ roles.from }}-{{ roles.to }}/{{ roles.total }}
      </pagination>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import SearchFilter from '@/Shared/SearchFilter.vue'
import Pagination from '@/Shared/Pagination.vue'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

export default {
  components: {
    Head,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    roles: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search || '',
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/roles', pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
          replace: true, // <--- importante para que no rompa el historial
        })

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
