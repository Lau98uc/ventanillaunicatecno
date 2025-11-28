<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links" :key="key">
        <div v-if="link.url === null"
          class="mb-1 mr-1 px-4 py-3 text-gray-400 text-sm leading-5 border border-gray-300 rounded cursor-not-allowed select-none"
          v-html="link.label" />
        <Link v-else :href="link.url"
          class="mb-1 mr-1 px-4 py-3 text-sm leading-5 rounded border border-gray-300 hover:bg-base-100 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 transition-colors"
          :class="{
            'bg-primary text-primary-content border-primary': link.active,
            'text-base-content': !link.active
          }">
        <span v-html="link.label" />
        </Link>

      </template>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link,
  },
  props: {
    links: Array,
  },
}
</script>

