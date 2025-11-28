<template>
  <div>

    <Head title="Users" />
    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Botón "Nuevo" y título -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/users/create">
        <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Usuarios</h1>
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
          <label class="block text-gray-700">Role:</label>
          <select v-model="form.role" class="select select-sm mt-1 w-full">
            <option :value="null" />
            <option value="user">User</option>
            <option value="owner">Owner</option>
          </select>
          <label class="block text-sm text-gray-700">Trashed:</label>
          <select v-model="form.trashed" class="select select-sm mt-1 w-full">
            <option :value="null" />
            <option value="with">With Trashed</option>
            <option value="only">Only Trashed</option>
          </select>
        </search-filter>
      </div>
    </div>


    <div class="rounded-md shadow overflow-x-auto">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead class="sticky top-0 bg-base-200 z-10">
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Name</th>
            <th class="pb-4 pt-6 px-6">Email</th>
            <th class="pb-4 pt-6 px-6">Tipo</th>
            <th class="pb-4 pt-6 px-6" colspan="2">Acciones</th>
          </tr>
        </thead>
        <tbody>

          <tr v-for="user in users.data" :key="user.id" class="hover">
            <td class="border-t  px-6 py-3">
              <Link class="flex items-center focus:text-indigo-500" :href="`/users/${user.id}/edit`">
              <img v-if="user.photo" class="block -my-2 mr-2 w-5 h-5 rounded-full" :src="user.photo" />
              {{ user.name }}
              <icon v-if="user.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t  px-6 py-3">
              <Link class="flex items-center" :href="`/users/${user.id}/edit`" tabindex="-1">
              {{ user.email }}
              </Link>
            </td>
            <td class="border-t  px-6 py-3">
              <Link class="flex items-center" :href="`/users/${user.id}/edit`" tabindex="-1">
              <span v-if="user.is_admin" class="badge badge-primary">Admin</span>
              <span v-else class="badge badge-secondary">User</span>
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/users/${user.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="users.data.length === 0">
            <td class="px-6 py-3 border-t" colspan="4">No users found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4 flex justify-center">
      <pagination :links="users.links" />
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
import SearchFilter from '@/Shared/SearchFilter.vue'
import Pagination from '@/Shared/Pagination.vue'

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
    users: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/users', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
  created() {
    console.log(this.users)
  }
}
</script>
