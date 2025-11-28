<template>
  <div>

    <Head title="Notificaciones" />

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
      <!-- Izquierda: título -->
      <div class="flex items-center p-1 gap-2 flex-wrap">
        <h1 class="text-sm font-bold">Notificaciones</h1>
      </div>

      <!-- Centro: filtro búsqueda -->
      <search-filter v-model="form.search" class="w-full max-w-md">
        <label class="block text-sm font-medium text-base-content mb-1">Buscar:</label>
        <input type="search" v-model="form.search" placeholder="Buscar notificaciones..."
          class="input input-bordered w-full bg-base-100 text-base-content" />
      </search-filter>

      <!-- Derecha: paginación -->
      <div>
        <pagination class="mt-6" :links="notifications.links" />
      </div>
    </div>

    <div class="overflow-x-auto rounded-md shadow">
      <table class="table table-xs table-zebra w-full whitespace-nowrap">
        <thead>
          <tr>
            <th>Título</th>
            <th>Mensaje</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Leído</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="notification in notifications.data" :key="notification.id" class="hover">
            <td>
              <a class="flex items-center gap-2 px-6 py-3 focus:text-primary" :href="notification.link || '#'">
              {{ notification.title }}
              <icon v-if="!notification.is_read" name="badge-check" class="w-4 h-4 fill-blue-500" />
              </a>
            </td>
            <td>
              <div class="px-6 py-3 text-ellipsis max-w-xs truncate" :title="notification.message">
                {{ notification.message }}
              </div>
            </td>
            <td>
              <span class="px-2 py-1 rounded text-xs font-semibold text-white" :class="typeColor(notification.type)">
                {{ notification.type || '-' }}
              </span>
            </td>
            <td>
              {{ formatDate(notification.created_at) }}
            </td>
            <td>
              <span v-if="notification.is_read" class="text-green-600 font-semibold">Sí</span>
              <span v-else class="text-red-600 font-semibold">No</span>
            </td>
            <td class="w-px">
              <Link v-if="!notification.is_read" class="btn btn-xs btn-primary"
                :href="`/notifications/${notification.id}/read`" method="post" as="button" type="button">
              Marcar leído
              </Link>
            </td>
          </tr>
          <tr v-if="notifications.data.length === 0">
            <td class="px-6 py-3 text-center" colspan="6">No hay notificaciones.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Pagination from '@/Shared/Pagination.vue'
import SearchFilter from '@/Shared/SearchFilter.vue'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import Layout from '@/Shared/Layout.vue';

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
    notifications: Object,
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
        this.$inertia.get('/notifications', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleString()
    },
    typeColor(type) {
      switch (type) {
        case 'tramite.asignado':
          return 'bg-blue-600'
        case 'tramite.seguimiento':
          return 'bg-yellow-500'
        case 'usuario.bloqueado':
          return 'bg-red-600'
        case 'test':
          return 'bg-gray-600'
        default:
          return 'bg-gray-400'
      }
    },
  },
}
</script>
