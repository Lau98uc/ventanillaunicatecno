<template>
  <div>
    <Head title="Crear Solicitud de Trámite" />

    <!-- Encabezado -->
    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <div class="flex items-center gap-2">
        <h1 class="text-lg font-bold">Nueva Solicitud</h1>
        <button type="submit" form="formSolicitud" class="btn btn-primary btn-sm">Guardar</button>
      </div>
    </div>

    <!-- Formulario -->
    <div class="max-w-3xl rounded-md shadow overflow-hidden">
      <form @submit.prevent="submit" id="formSolicitud" enctype="multipart/form-data">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <!-- Título -->
          <div class="form-control pb-8 pr-6 w-full">
            <label class="label font-semibold">Título</label>
            <input v-model="form.nombre" class="input input-bordered w-full" placeholder="Ingrese un título" />
          </div>
        
          <!-- Trámite ID -->
          <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
            <label class="label font-semibold">Trámite</label>
            <select v-model="form.tramite_id" class="select select-bordered w-full">
              <option value="" disabled selected>Seleccione un trámite</option>
              <option v-for="tramite in tramites" :key="tramite.id" :value="tramite.id">
                {{ tramite.nombre }}
              </option>
            </select>
          </div>

          <!-- Usuario ID -->
          <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
            <label class="label font-semibold">Usuario</label>
            <select v-model="form.usuario_id" class="select select-bordered w-full">
              <option value="" disabled selected>Seleccione un usuario</option>
              <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
                {{ usuario.first_name }}
              </option>
            </select>

          <!-- Estado Actual -->
          <div class="form-control pb-8 pr-6 w-full">
            <label class="label font-semibold">Estado Actual</label>
            <input type="text" v-model="form.estado_actual" class="input input-bordered w-full" />
          </div>
</div>
          <!-- Archivos -->
          <div class="form-control pb-8 pr-6 w-full">
            <label class="label font-semibold">Documentos (PDF o Imagen)</label>
            <input type="file" multiple @change="handleFiles" class="file-input file-input-bordered w-full" />

            <!-- Previsualización -->
            <div class="mt-4 flex gap-4 flex-wrap">
              <div
                v-for="(doc, index) in previews"
                :key="index"
                class="w-32 border rounded p-1 shadow-sm bg-base-100"
              >
                <img
                  v-if="isImage(doc.type)"
                  :src="doc.url"
                  class="w-full h-24 object-cover rounded"
                />
                <embed
                  v-else
                  :src="doc.url"
                  type="application/pdf"
                  width="100%"
                  height="100px"
                  class="rounded"
                />
              </div>
            </div>
            
          </div>
        </div>

        <!-- Botón inferior -->
        <div class="flex items-center px-8 py-4 border-t border-gray-100 justify-end">
          <button class="btn btn-primary" type="submit" :disabled="form.processing">
            Guardar Solicitud
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script >
import Layout from '@/Shared/Layout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
export default {
  components: {
    Head, Link
  },
  props: {
    workflows: Array,
    usuarios: Object,
    tramites: Object,
  },
  layout: Layout,
  data() {
    return {
      form: {
        nombre: '',
        tramite_id: '',
        usuario_id: '',
        estado_actual: '',
        documentos: [],
        processing: false,
      },
    }
  },
  methods: {
    submit() {
      router.post('/tramites', form)
    }
  }
}
const previews = ref([])

const handleFiles = (e) => {
  form.documentos = Array.from(e.target.files)
  previews.value = form.documentos.map((file) => ({
    url: URL.createObjectURL(file),
    type: file.type,
  }))
}

const isImage = (type) => type.startsWith('image/')

const submit = () => {
  form.post('/solicitudes', {
    forceFormData: true,
  })
}
</script>

