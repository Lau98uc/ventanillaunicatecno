<template>
  <div>
    <Head title="Crear Solicitud de Trámite" />

    <!-- Encabezado -->
    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <div class="flex items-center gap-2">
        <h1 class="text-lg font-bold">Nueva Solicitud</h1>
        <button
          v-if="!mostrarQR"
          type="submit"
          form="formSolicitud"
          class="btn btn-primary btn-sm"
        >
          Generar QR de Pago
        </button>
      </div>
    </div>

    <!-- Layout: Formulario + Panel QR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Formulario (2/3 del ancho) -->
      <div class="lg:col-span-2">
        <div class="rounded-md shadow overflow-hidden">
          <form @submit.prevent="generarQR" id="formSolicitud" enctype="multipart/form-data">
            <div class="flex flex-wrap -mb-8 -mr-6 p-8">

              <!-- Título -->
              <div class="form-control pb-8 pr-6 w-full">
                <label class="label font-semibold">Título</label>
                <input
                  v-model="form.nombre"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.nombre }"
                  placeholder="Ingrese un título"
                />
                <label v-if="errors.nombre" class="label">
                  <span class="label-text-alt text-error">{{ errors.nombre }}</span>
                </label>
              </div>

              <!-- Trámite ID -->
              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Trámite ID</label>
                <input
                  type="number"
                  v-model="form.tramite_id"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.tramite_id }"
                />
                <label v-if="errors.tramite_id" class="label">
                  <span class="label-text-alt text-error">{{ errors.tramite_id }}</span>
                </label>
              </div>

              <!-- Usuario ID -->
              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Usuario ID</label>
                <input
                  type="number"
                  v-model="form.usuario_id"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.usuario_id }"
                />
                <label v-if="errors.usuario_id" class="label">
                  <span class="label-text-alt text-error">{{ errors.usuario_id }}</span>
                </label>
              </div>

              <!-- Datos del Cliente para PagoFácil -->
              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Nombre Completo</label>
                <input
                  type="text"
                  v-model="form.client_name"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.client_name }"
                  placeholder="Ej: Juan Pérez"
                />
                <label v-if="errors.client_name" class="label">
                  <span class="label-text-alt text-error">{{ errors.client_name }}</span>
                </label>
              </div>

              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Cédula de Identidad</label>
                <input
                  type="text"
                  v-model="form.document_id"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.document_id }"
                  placeholder="Ej: 12345678"
                />
                <label v-if="errors.document_id" class="label">
                  <span class="label-text-alt text-error">{{ errors.document_id }}</span>
                </label>
              </div>

              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Email</label>
                <input
                  type="email"
                  v-model="form.email"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.email }"
                  placeholder="ejemplo@email.com"
                />
                <label v-if="errors.email" class="label">
                  <span class="label-text-alt text-error">{{ errors.email }}</span>
                </label>
              </div>

              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Teléfono (opcional)</label>
                <input
                  type="text"
                  v-model="form.phone_number"
                  class="input input-bordered w-full"
                  placeholder="Ej: 70123456"
                />
              </div>

              <!-- Estado Actual -->
              <div class="form-control pb-8 pr-6 w-full">
                <label class="label font-semibold">Estado Actual</label>
                <input
                  type="text"
                  v-model="form.estado_actual"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.estado_actual }"
                />
                <label v-if="errors.estado_actual" class="label">
                  <span class="label-text-alt text-error">{{ errors.estado_actual }}</span>
                </label>
              </div>

              <!-- Archivos -->
              <div class="form-control pb-8 pr-6 w-full">
                <label class="label font-semibold">Documentos (PDF o Imagen)</label>
                <input
                  type="file"
                  multiple
                  @change="handleFiles"
                  class="file-input file-input-bordered w-full"
                  :disabled="mostrarQR"
                />

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
                      class="w-full h-32 object-cover rounded"
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

            <!-- Botón inferior del formulario -->
            <div class="flex items-center px-8 py-4 border-t border-gray-100 justify-end">
              <button
                class="btn btn-primary"
                type="submit"
                :disabled="mostrarQR || generandoQR"
              >
                <span v-if="generandoQR" class="loading loading-spinner loading-sm"></span>
                <svg v-else-if="!mostrarQR" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                {{ generandoQR ? 'Generando QR...' : (mostrarQR ? 'QR Generado' : 'Generar QR de Pago') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Panel QR (1/3 del ancho) -->
      <div v-if="mostrarQR" class="lg:col-span-1">
        <div class="card bg-base-200 shadow-xl sticky top-4">
          <div class="card-body">
            <h2 class="card-title text-center">Pago del Trámite</h2>

            <!-- QR Code -->
            <div class="flex justify-center my-4">
              <div class="bg-white p-4 rounded-lg">
                <img
                  v-if="qrImage"
                  :src="qrImage"
                  alt="QR de Pago"
                  class="w-48 h-48"
                />
                <div v-else class="w-48 h-48 flex items-center justify-center">
                  <span class="loading loading-spinner loading-lg"></span>
                </div>
              </div>
            </div>

            <!-- Información del pago -->
            <div class="alert alert-info text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div>
                <div class="font-bold">Monto: Bs. {{ montoPago }}</div>
                <div class="text-xs">Escanea el QR para pagar</div>
                <div v-if="paymentNumber" class="text-xs mt-1">
                  Nº Pago: {{ paymentNumber }}
                </div>
              </div>
            </div>

            <!-- Estado del pago -->
            <div v-if="verificandoPago" class="alert alert-warning text-sm">
              <span class="loading loading-spinner loading-sm"></span>
              <span>Verificando pago...</span>
            </div>

            <div v-if="pagoRealizado" class="alert alert-success text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>¡Pago verificado!</span>
            </div>

            <div v-if="errorPago" class="alert alert-error text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ errorPago }}</span>
            </div>

            <!-- Botón crear solicitud -->
            <button
              @click="crearSolicitud"
              class="btn btn-success btn-block mt-4"
              :disabled="!pagoRealizado || form.processing"
            >
              <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
              {{ form.processing ? 'Creando...' : 'Crear Solicitud' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, onBeforeUnmount } from 'vue'
import Layout from '../../Shared/Layout.vue'
import axios from 'axios'

defineOptions({
  layout: Layout,
})

const form = useForm({
  nombre: '',
  tramite_id: '',
  usuario_id: '',
  estado_actual: '',
  documentos: [],
  client_name: '',
  document_id: '',
  email: '',
  phone_number: '',
})

const previews = ref([])
const errors = ref({})
const mostrarQR = ref(false)
const generandoQR = ref(false)
const qrImage = ref('')
const pagoRealizado = ref(false)
const verificandoPago = ref(false)
const montoPago = ref(50.00)
const paymentNumber = ref(null)
const errorPago = ref(null)
let intervaloPago = null

const handleFiles = (e) => {
  form.documentos = Array.from(e.target.files)
  previews.value = form.documentos.map((file) => ({
    url: URL.createObjectURL(file),
    type: file.type,
  }))
}

const isImage = (type) => type.startsWith('image/')

const validarForm = () => {
  errors.value = {}

  if (!form.nombre || form.nombre.trim() === '') {
    errors.value.nombre = 'El título es requerido'
  }

  if (!form.tramite_id) {
    errors.value.tramite_id = 'El trámite ID es requerido'
  }

  if (!form.usuario_id) {
    errors.value.usuario_id = 'El usuario ID es requerido'
  }

  if (!form.estado_actual || form.estado_actual.trim() === '') {
    errors.value.estado_actual = 'El estado actual es requerido'
  }

  if (!form.client_name || form.client_name.trim() === '') {
    errors.value.client_name = 'El nombre completo es requerido'
  }

  if (!form.document_id || form.document_id.trim() === '') {
    errors.value.document_id = 'La cédula de identidad es requerida'
  }

  if (!form.email || form.email.trim() === '') {
    errors.value.email = 'El email es requerido'
  }

  return Object.keys(errors.value).length === 0
}

const generarQR = async () => {
  if (!validarForm()) {
    return
  }

  generandoQR.value = true
  errorPago.value = null

  try {
    const response = await axios.post('/api/pagofacil/generar-qr', {
      client_name: form.client_name,
      document_id: form.document_id,
      email: form.email,
      phone_number: form.phone_number,
      amount: montoPago.value,
      order_detail: [
        {
          serial: 1,
          product: form.nombre,
          quantity: 1,
          price: montoPago.value,
          discount: 0,
          total: montoPago.value
        }
      ]
    })

    if (response.data.success) {
      qrImage.value = response.data.data.qr_image
      paymentNumber.value = response.data.data.payment_number
      mostrarQR.value = true

      // Iniciar verificación automática del pago
      iniciarVerificacionPago()
    } else {
      errorPago.value = response.data.message || 'Error al generar QR'
    }

  } catch (error) {
    console.error('Error al generar QR:', error)
    errorPago.value = error.response?.data?.message || 'Error al generar el QR de pago'
  } finally {
    generandoQR.value = false
  }
}

const iniciarVerificacionPago = () => {
  // Verificar cada 5 segundos
  intervaloPago = setInterval(() => {
    verificarPago()
  }, 5000)
}

const verificarPago = async () => {
  if (!paymentNumber.value) return

  verificandoPago.value = true

  try {
    const response = await axios.get(`/api/pagofacil/verificar-pago/${paymentNumber.value}`)

    if (response.data.pagado) {
      pagoRealizado.value = true
      if (intervaloPago) {
        clearInterval(intervaloPago)
      }
    }

  } catch (error) {
    console.error('Error verificando pago:', error)
  } finally {
    verificandoPago.value = false
  }
}

const crearSolicitud = () => {
  if (!pagoRealizado.value) {
    return
  }

  form.post('/solicitudes', {
    forceFormData: true,
    onSuccess: () => {
      if (intervaloPago) {
        clearInterval(intervaloPago)
      }
    }
  })
}

onBeforeUnmount(() => {
  if (intervaloPago) {
    clearInterval(intervaloPago)
  }
})
</script>