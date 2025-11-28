<template>
  <div v-if="hasPagination" class="flex justify-center items-center gap-4">
    <div class="text-sm text-base-content">
      <slot></slot>
    </div>

    <!-- Botón Anterior -->
    <button class="btn btn-sm btn-outline" :disabled="!previousLink.url" @click="visit(previousLink.url)">
      &lt;
    </button>

    <!-- Botón Siguiente -->
    <button class="btn btn-sm btn-outline" :disabled="!nextLink.url" @click="visit(nextLink.url)">
      &gt;
    </button>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3'

export default {
  props: {
    links: {
      type: Array,
      required: true,
    },
  },
  computed: {
    hasPagination() {
      return this.links && this.links.length > 3
    },
    previousLink() {
      return this.links[0] || {}
    },
    nextLink() {
      return this.links[this.links.length - 1] || {}
    },
  },
  methods: {
    visit(url) {
      if (!url) return
      // Convierte URL absoluta a relativa para Inertia
      const relativeUrl = url.replace(window.location.origin, '')
      router.visit(relativeUrl, { preserveState: true, preserveScroll: true })
    },
  },
}
</script>
