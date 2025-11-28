<template>
  <div>

    <Head title="Trámites (Kanban)" />

    <!-- Encabezado y botones -->
    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <div class="flex items-center gap-2">
        <Link class="btn btn-sm btn-primary" href="/tramites/create">
        <span>Nuevo</span>
        </Link>
        <Link class="text-indigo-400 hover:text-indigo-600" href="/tramites">Trámites</Link>


      </div>
    </div>

    <!-- Contenido Kanban -->
    <KanbanBoard :items="tramites" :estados="estados" @select="verTramite">
      <template #card="{ item }">
        <div
          class="bg-base-300 rounded-lg shadow p-3 cursor-pointer transition hover:bg-primary hover:text-primary-content"
          @click="verTramite(item)">
          <h3 class="font-semibold truncate text-base-content">
            {{ item.titulo }}
          </h3>
          <p class="text-xs text-base-content/60 truncate">
            {{ item.codigo }}
          </p>
        </div>
      </template>
    </KanbanBoard>

  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import KanbanBoard from '@/Shared/KanbanBoard.vue'

defineProps({
  tramites: Array,
  estados: Array,
})

function verTramite(tramite) {
  router.get(`/tramites/${tramite.id}`)
}
</script>

<script>
import Layout from '@/Shared/Layout.vue'
export default {
  layout: Layout,
}
</script>
