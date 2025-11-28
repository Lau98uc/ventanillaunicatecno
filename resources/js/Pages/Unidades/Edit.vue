<template>
  <div>

    <Head :title="form.name" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/unidades">unidades</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="unidad.deleted_at" class="mb-6" @restore="restore"> This unidad has been deleted.
    </trashed-message>
    <div class="max-w-3xl rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pb-8 pr-6 w-full lg:w-1/2" label="Phone" />
          <text-input v-model="form.address" :error="form.errors.address" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Address" />
          <text-input v-model="form.city" :error="form.errors.city" class="pb-8 pr-6 w-full lg:w-1/2" label="City" />
          <text-input v-model="form.region" :error="form.errors.region" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Province/State" />
          <select-input v-model="form.country" :error="form.errors.country" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Country">
            <option :value="null" />
            <option value="CA">Canada</option>
            <option value="US">United States</option>
          </select-input>

        </div>
        <div class="flex items-center px-8 py-4 border-t border-gray-100">
          <button v-if="!unidad.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button"
            @click="destroy">Delete unidad</button>
          <loading-button :loading="form.processing" class="btn btn-primary ml-auto" type="submit">Update
            unidad</loading-button>
        </div>
      </form>
    </div>
    <h2 class="mt-12 text-2xl font-bold">responsables</h2>
    <div class="mt-6rounded shadow border rounded-md overflow-x-auto">
      <table class="table table-zebra w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Name</th>
          <th class="pb-4 pt-6 px-6">City</th>
          <th class="pb-4 pt-6 px-6" colspan="2">Phone</th>
        </tr>
        <tbody>
          <tr v-for="responsable in unidad.responsables" :key="responsable.id" class="hover">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/responsables/${responsable.id}/edit`">
              {{ responsable.name }}
              <icon v-if="responsable.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
              {{ responsable.city }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
              {{ responsable.phone }}
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/responsables/${responsable.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="unidad.responsables.length === 0">
            <td class="px-6 py-4 border-t" colspan="4">No responsables found.</td>
          </tr>
        </tbody>

      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    unidad: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.unidad.name,
        email: this.unidad.email,
        phone: this.unidad.phone,
        address: this.unidad.address,
        city: this.unidad.city,
        region: this.unidad.region,
        country: this.unidad.country,

      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/unidades/${this.unidad.id}`)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this unidad?')) {
        this.$inertia.delete(`/unidades/${this.unidad.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this unidad?')) {
        this.$inertia.put(`/unidades/${this.unidad.id}/restore`)
      }
    },
  },
}
</script>
