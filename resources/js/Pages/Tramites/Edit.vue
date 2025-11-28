<!-- Edit.vue -->
<template>
  <div>

    <Head title="Editar Trámite" />
    <h1 class="text-2xl font-bold mb-4">Editar Trámite</h1>
    <form @submit.prevent="submit">

      <div class="form-control mb-4">
        <label class="label">Título</label>
        <input v-model="form.nombre" class="input input-bordered" />
      </div>

      <div class="form-control mb-4">
        <label class="label">Workflow</label>
        <select v-model="form.workflow_id" class="select select-bordered">
          <option v-for="w in workflows" :key="w.id" :value="w.id">{{ w.nombre }}</option>
        </select>
      </div>
      <button class="btn btn-primary" type="submit">Actualizar</button>
    </form>
    <flujo-diagrama :flujo='flujo'></flujo-diagrama>


  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue';
import TextInput from '@/Shared/TextInput.vue'
import FileInput from '@/Shared/FileInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'
import FlujoDiagrama from './flujo-diagrama.vue';



export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    FlujoDiagrama
  },
  remember: 'form',
  props: {
    tramite: Object,
    workflows: Array,
    flujo: Object
  },
  data() {
    return {
      form: this.$inertia.form({
        nombre: this.tramite.nombre,
        workflow_id: this.tramite.workflow_id
      })
    }
  },
  layout:Layout,
  methods: {
    submit() {
      this.form.post(`/tramites/${tramite.id}`, {
        onSuccess: () => this.form.reset(),
      })
    }
  },

}

</script>
