<template>
  <div>
    <Head title="Permisos" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Botón "Nuevo" y título -->
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/permissions/create">
        <span>Nuevo</span>
        </Link>
        <h1 class="text-sm font-bold">Permisos</h1>
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

      <!-- Derecha: placeholder -->
      <div></div>
    </div>

    <div class="rounded-md shadow overflow-x-auto">
      <div class="bg-base-100 p-6">
        <div v-for="(perms, group) in permissions" :key="group" class="mb-8">
          <h3 class="text-lg font-semibold mb-4 text-primary">{{ group }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="perm in perms" :key="perm.id" 
                 class="bg-base-200 p-4 rounded-lg border border-base-300">
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-medium text-base-content">{{ perm.name }}</h4>
                  <p v-if="perm.description" class="text-sm text-base-content/70 mt-1">
                    {{ perm.description }}
                  </p>
                </div>
                <div class="flex gap-2">
                  <Link :href="`/permissions/${perm.id}/edit`" 
                        class="btn btn-xs btn-outline btn-primary">
                    Editar
                  </Link>
                  <button @click="deletePermission(perm.id)" 
                          class="btn btn-xs btn-outline btn-error">
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-if="Object.keys(permissions).length === 0" 
             class="text-center py-8 text-base-content/60">
          No hay permisos registrados
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'

export default {
  components: {
    Head,
    Link,
  },
  layout: Layout,
  props: {
    permissions: Object,
  },
  methods: {
    deletePermission(id) {
      if (confirm('¿Estás seguro de que quieres eliminar este permiso?')) {
        this.$inertia.delete(`/permissions/${id}`)
      }
    },
  },
}
</script> 