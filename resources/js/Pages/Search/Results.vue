<template>
  <Head title="Resultados de Búsqueda" />
  
  <div class="min-h-screen bg-base-200">
    <div class="container mx-auto px-4 py-8">
      <!-- Header de búsqueda -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-3xl font-bold text-base-content">
            Resultados de búsqueda
          </h1>
          <Link 
            href="/" 
            class="btn btn-outline btn-primary"
          >
            <icon name="arrow-left" class="w-4 h-4 mr-2" />
            Volver
          </Link>
        </div>
        
        <!-- Buscador -->
        <div class="relative max-w-2xl">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Buscar en el sistema..." 
            class="w-full px-4 py-3 pl-12 pr-4 text-lg bg-base-100 border border-base-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            @keydown.enter="performSearch"
          />
          <svg class="absolute left-4 top-3.5 h-5 w-5 text-base-content" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button 
            @click="performSearch"
            class="absolute right-2 top-2 btn btn-primary btn-sm"
          >
            Buscar
          </button>
        </div>
        
        <p class="mt-2 text-base-content opacity-70">
          Buscando: <span class="font-semibold">"{{ query }}"</span>
        </p>
      </div>

      <!-- Resultados -->
      <div v-if="Object.keys(results).length > 0" class="space-y-8">
        <div 
          v-for="(section, key) in results" 
          :key="key"
          class="bg-base-100 rounded-lg shadow-lg p-6"
        >
          <div class="flex items-center mb-4">
            <icon :name="section.icon" class="w-6 h-6 mr-3 text-primary" />
            <h2 class="text-xl font-semibold text-base-content">
              {{ section.title }}
            </h2>
            <span class="ml-2 badge badge-primary">
              {{ section.items.length }}
            </span>
          </div>
          
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div 
              v-for="item in section.items" 
              :key="item.id"
              class="bg-base-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
              @click="navigateTo(item.url)"
            >
              <div class="flex items-start justify-between mb-2">
                <h3 class="font-semibold text-base-content line-clamp-2">
                  {{ item.title }}
                </h3>
                <icon name="arrow-right" class="w-4 h-4 text-base-content opacity-50" />
              </div>
              
              <p v-if="item.subtitle" class="text-sm text-base-content opacity-70 mb-2">
                {{ item.subtitle }}
              </p>
              
              <p v-if="item.description" class="text-sm text-base-content opacity-60 line-clamp-2">
                {{ item.description }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Sin resultados -->
      <div v-else class="text-center py-12">
        <div class="mb-4">
          <icon name="search" class="w-16 h-16 mx-auto text-base-content opacity-30" />
        </div>
        <h2 class="text-2xl font-semibold text-base-content mb-2">
          No se encontraron resultados
        </h2>
        <p class="text-base-content opacity-70 mb-6">
          No se encontraron coincidencias para "{{ query }}"
        </p>
        <div class="space-y-2">
          <p class="text-sm text-base-content opacity-60">
            Sugerencias:
          </p>
          <ul class="text-sm text-base-content opacity-60 space-y-1">
            <li>• Verifica que las palabras estén escritas correctamente</li>
            <li>• Intenta con términos más generales</li>
            <li>• Usa menos palabras clave</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'

export default {
  components: { Head, Link, Icon },
  props: {
    query: {
      type: String,
      required: true
    },
    results: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      searchQuery: this.query
    }
  },
  methods: {
    performSearch() {
      if (this.searchQuery.trim()) {
        router.visit(`/search?q=${encodeURIComponent(this.searchQuery.trim())}`)
      }
    },
    navigateTo(url) {
      router.visit(url)
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 