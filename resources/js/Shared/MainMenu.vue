<template>
  <div>
    <!-- Buscador Global -->
    <div class="mb-6">
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar en el sistema..."
          class="w-full px-4 py-2 pl-10 pr-4 text-sm bg-base-100 border border-base-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          @keydown.enter="handleEnterKey"
          @keydown.escape="searchQuery = ''"
        />
        <svg class="absolute left-3 top-2.5 h-4 w-4 text-base-content" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- Resultados de búsqueda -->
      <div v-if="showResults" class="mt-2 bg-base-100 border border-base-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">
        <div v-if="isLoading" class="px-4 py-3 text-sm text-base-content opacity-70">
          <div class="flex items-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary mr-2"></div>
            Buscando...
          </div>
        </div>
        <div v-else-if="searchResults.length > 0">
          <div
            v-for="item in searchResults"
            :key="`${item.type}-${item.id}`"
            @click="navigateTo(item.url)"
            class="px-4 py-3 hover:bg-base-200 cursor-pointer border-b border-base-200 last:border-b-0"
          >
            <div class="flex items-start">
              <icon :name="item.icon" class="mr-3 w-4 h-4 fill-base-content mt-0.5" />
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-base-content truncate">{{ item.title }}</div>
                <div class="text-xs text-base-content opacity-70 truncate">{{ item.subtitle }}</div>
                <div v-if="item.description" class="text-xs text-base-content opacity-60 mt-1 line-clamp-1">
                  {{ item.description }}
                </div>
                <div class="flex items-center justify-between mt-1">
                  <span class="text-xs px-2 py-1 bg-primary text-primary-content rounded-full">{{ item.origin }}</span>
                  <span class="text-xs text-base-content opacity-50">{{ item.type }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="px-2 py-1 text-xs text-base-content opacity-60 text-center">
            Mostrando resultados rápidos. Presiona <kbd class="kbd kbd-xs mx-1">Enter</kbd> para ver todos.
          </div>
        </div>
        <div v-else-if="searchQuery.length >= 2" class="px-4 py-3 text-sm text-base-content opacity-70">
          <div class="flex items-center">
            <icon name="search" class="w-4 h-4 mr-2" />
            Presiona Enter para ver todos los resultados de "{{ searchQuery }}"
          </div>
        </div>
      </div>
    </div>

    <!-- Menú Principal -->
    <div v-for="item in filteredMenuItems" :key="item.href" class="mb-3">
      <Link
        :href="item.href"
        class="group flex items-center px-3 py-2 rounded-lg transition duration-150"
        :class="isActive(item.href) ? 'bg-primary text-primary-content shadow-lg border-l-4 border-primary-content font-semibold' : 'hover:bg-base-200 text-base-content'"
      >
        <icon
          :name="item.icon"
          class="mr-2 w-5 h-5"
          :class="isActive(item.href) ? 'fill-primary-content' : 'fill-base-content group-hover:fill-primary'"
        />
        <span class="font-medium">{{ item.label }}</span>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link, router, usePage } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'

export default {
  components: { Icon, Link },
  props: {
    menuItems: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      searchQuery: '',
      searchResults: [],
      isLoading: false,
      showResults: false,
    }
  },
  mounted() {
    // Cerrar buscador cuando se hace clic fuera
    document.addEventListener('click', this.handleClickOutside)

    // Agregar listener para el input de búsqueda
    this.$nextTick(() => {
      const searchInput = this.$el.querySelector('input[type="text"]')
      if (searchInput) {
        searchInput.addEventListener('input', this.handleSearchInput)
      }
    })
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)

    // Remover listener del input
    const searchInput = this.$el.querySelector('input[type="text"]')
    if (searchInput) {
      searchInput.removeEventListener('input', this.handleSearchInput)
    }
  },
  computed: {
    // Filtrar el dashboard del menú
    filteredMenuItems() {
      return this.menuItems.filter(item =>
        item.href !== '/' &&
        item.href !== '/dashboard' &&
        item.label.toLowerCase() !== 'dashboard'
      )
    }
  },
  methods: {
    isActive(href) {
      const page = usePage()
      const current = page.url.replace(/^\/|\/$/g, '') // quita / al inicio y final
      const target = href.replace(/^\/|\/$/g, '')

      if (target === '') {
        return current === ''
      }

      // Para rutas exactas como /users, /roles, etc.
      if (target === current) {
        return true
      }

      // Para subrutas como /users/1/edit debe coincidir con /users
      if (current.startsWith(target + '/')) {
        return true
      }

      return false
    },
    navigateTo(href) {
      this.searchQuery = ''
      router.visit(href)
    },
    handleClickOutside(event) {
      // Si el clic no es dentro del buscador, cerrar los resultados
      if (!this.$el.contains(event.target)) {
        this.showResults = false
        this.searchQuery = ''
        this.searchResults = []
      }
    },
    async handleSearchInput(event) {
      const query = event.target.value // Permitir espacios en blanco en el input
      this.searchQuery = query

      if (query.trim().length >= 2) {
        this.showResults = true
        this.isLoading = true

        try {
          const trimmedQuery = query.trim()

          // 🚀 MODIFICACIÓN CLAVE (router.get):
          // 1. Usamos route('search.api') para la URL base.
          // 2. Pasamos { q: trimmedQuery } como el objeto de datos. Inertia/Ziggy construye el ?q=...
          await router.get(route('search.api'), { q: trimmedQuery }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
              this.searchResults = page.props.searchResults || []
            },
            onError: (errors) => {
              console.error('Error en la búsqueda:', errors)
              this.searchResults = []
            },
            onFinish: () => {
              this.isLoading = false
            }
          })
        } catch (error) {
          console.error('Error en la búsqueda:', error)
          this.searchResults = []
          this.isLoading = false
        }
      } else {
        this.showResults = false
        this.searchResults = []
      }
    },
    handleEnterKey() {
      if (this.searchResults.length > 0) {
        this.navigateTo(this.searchResults[0].url)
      } else if (this.searchQuery.trimEnd()) {
        this.showAllResults()
      }
    },
    showAllResults() {
      if (this.searchQuery.trimEnd()) {
        const trimmedQuery = this.searchQuery.trimEnd()

        // 🚀 MODIFICACIÓN CLAVE (router.visit):
        // Asumiendo que la ruta nombrada para la página completa es 'search.show' (si no la tienes, usa el comentario de abajo)
        // router.visit(route('search.show', { q: trimmedQuery }))

        // Si prefieres mantener el hardcode de la URL base (/search), solo usamos el parámetro
        router.visit(route('search.show', { q: trimmedQuery }))
        // Nota: Para este ejemplo he asumido que tienes una ruta nombrada 'search.show' para /search.
        // Si no la tienes, usa la siguiente línea comentada:
        // router.visit(`/search?q=${encodeURIComponent(trimmedQuery)}`)
      }
    }
  },
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
