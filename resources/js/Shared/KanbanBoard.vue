<template>
  <!-- Fondo general claro igual que las tarjetas -->
  <div class="flex space-x-6 overflow-x-auto p-4 bg-base-200">
    <!-- Columna por estado -->
    <div v-for="estado in estados" :key="estado"
      class="flex-shrink-0 w-80 rounded-lg p-4 flex flex-col bg-base-100 shadow">
      <!-- Encabezado -->
      <h2 class="text-md font-semibold mb-4 capitalize text-base-content">{{ estado }}</h2>

      <!-- Tarjetas -->
      <div class="flex-grow space-y-3 min-h-[200px]">
        <div v-for="item in itemsPorEstado(estado)" :key="item.id">
          <slot name="card" :item="item">
            <div
              class="bg-base-100 rounded-lg shadow p-3 cursor-pointer hover:bg-primary hover:text-primary-content transition"
              @click="$emit('select', item)">
              <h3 class="font-semibold truncate text-base-content">
                {{ item.titulo || item.name }}
              </h3>
              <p class="text-xs text-base-content/60 truncate">
                {{ item.codigo || '' }}
              </p>
            </div>
          </slot>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  estados: {
    type: Array,
    required: true,
  },
  estadoKey: {
    type: String,
    default: 'estado',
  },
})

function itemsPorEstado(estado) {
  return props.items.filter(item => item[props.estadoKey] === estado)
}
</script>
