<template>
  <div>

    <Head title="Crear responsable" />


    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Botón "Nuevo" y título -->
      <div class="flex items-center gap-2">

        <Link class="text-indigo-400 hover:text-indigo-600" href="/responsables">responsables
        <span class="text-indigo-400 font-medium">/</span> Create
        </Link>


        <button type="submit" form="miFormulario" class="btn btn-primary btn-sm">
          Guardar
        </button>
      </div>
      <!-- Centro: Filtro -->
      <div class="flex-1 flex justify-center">

      </div>

      <!-- Derecha: Botón placeholder -->
      <div>



      </div>
    </div>
    <div class="max-w-3xl rounded-md shadow overflow-hidden">
      <form @submit.prevent="store" id="miFormulario">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="First name" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Last name" />
          <select-input v-model="form.unidad_id" :error="form.errors.unidad_id"
            class="pb-8 pr-6 w-full lg:w-1/2" label="Unidades">
            <option :value="null" />
            <option v-for="unidad in unidades" :key="unidad.id" :value="unidad.id">{{
              unidad.name }}</option>
          </select-input>
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
          <text-input v-model="form.postal_code" :error="form.errors.postal_code" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Postal code" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn btn-primary" type="submit">Create
            Responsable</loading-button>
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

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  layout: Layout,
  props: {
    unidades: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        first_name: '',
        last_name: '',
        unidad_id: null,
        email: '',
        phone: '',
        address: '',
        city: '',
        region: '',
        country: '',
        postal_code: '',
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/responsables')
    },
  },
}
</script>
