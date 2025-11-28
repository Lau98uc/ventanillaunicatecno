<template>
  <div>
    <h3 class="font-bold mb-4">Historial de Seguimientos</h3>
    <ul class="space-y-4">
      <li v-for="seg in seguimientos" :key="seg.id" class="flex items-start space-x-4">
        <!-- Indicador -->
        <div class="flex flex-col items-center">
          <div class="w-4 h-4 bg-indigo-600 rounded-full mt-1"></div>
          <div class="w-px bg-gray-300 flex-1"></div>
        </div>
        <!-- Contenido -->
        <div>
          <div class="text-xs text-gray-500">
            {{ formatDate(seg.fecha) }} por <strong>{{ seg.usuario.name }}</strong>
          </div>
          <div>
            Cambió de <em>{{ seg.estado_anterior ?? 'N/A' }}</em> a <em>{{ seg.estado_nuevo }}</em>
          </div>
          <div class="text-gray-700 italic">
            {{ seg.observacion || 'Sin observación' }}
          </div>
        </div>
      </li>
      <li v-if="seguimientos.length === 0" class="text-gray-500 italic text-center">
        No hay seguimientos registrados.
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    seguimientos: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleString()
    },
  },
}
</script>
