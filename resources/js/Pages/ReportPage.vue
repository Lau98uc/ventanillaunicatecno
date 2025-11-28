<template>
  <div>
    <h1>Reporte desde backend</h1>
    <!-- Aquí inyectas el HTML renderizado -->
    <div v-html="reportHtml"></div>
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
  props: {
    reportHtml: String

  },
  layout: Layout,
  components: {

  }
}

</script>
