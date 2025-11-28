<template>
  <div>
    <Head :title="`${form.first_name} ${form.last_name}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/responsables">responsables</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.first_name }} {{ form.last_name }}
    </h1>
    <trashed-message v-if="responsable.deleted_at" class="mb-6" @restore="restore"> This responsable has been deleted. </trashed-message>
    <div class="max-w-3xl rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2" label="First name" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Last name" />
          <select-input v-model="form.unidad_id" :error="form.errors.unidad_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Unidad">
            <option :value="null" />
            <option v-for="unidad in unidades" :key="unidad.id" :value="unidad.id">{{ unidad.name }}</option>
          </select-input>
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pb-8 pr-6 w-full lg:w-1/2" label="Phone" />
          <text-input v-model="form.address" :error="form.errors.address" class="pb-8 pr-6 w-full lg:w-1/2" label="Address" />
          <text-input v-model="form.city" :error="form.errors.city" class="pb-8 pr-6 w-full lg:w-1/2" label="City" />
          <text-input v-model="form.region" :error="form.errors.region" class="pb-8 pr-6 w-full lg:w-1/2" label="Province/State" />
          <select-input v-model="form.country" :error="form.errors.country" class="pb-8 pr-6 w-full lg:w-1/2" label="Country">
            <option :value="null" />
            <option value="CA">Canada</option>
            <option value="US">United States</option>
          </select-input>
          <text-input v-model="form.postal_code" :error="form.errors.postal_code" class="pb-8 pr-6 w-full lg:w-1/2" label="Postal code" />
        </div>
        <div class="flex items-center px-8 py-4 border-t border-gray-100">
          <button v-if="!responsable.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete responsable</button>
          <loading-button :loading="form.processing" class="btn btn-primary ml-auto" type="submit">Update responsable</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    responsable: Object,
    unidades: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        first_name: this.responsable.first_name,
        last_name: this.responsable.last_name,
        unidad_id: this.responsable.unidad_id,
        email: this.responsable.email,
        phone: this.responsable.phone,
        address: this.responsable.address,
        city: this.responsable.city,
        region: this.responsable.region,
        country: this.responsable.country,

      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/responsables/${this.responsable.id}`)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this responsable?')) {
        this.$inertia.delete(`/responsables/${this.responsable.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this responsable?')) {
        this.$inertia.put(`/responsables/${this.responsable.id}/restore`)
      }
    },
  },
}
</script>
