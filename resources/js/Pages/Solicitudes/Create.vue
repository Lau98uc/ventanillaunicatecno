<template>
  <div>
    <meta name="csrf-token" content="{{ csrf_token() }}"></meta>
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
          :disabled="form.processing"
        >
          <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
          {{ tramiteCosto <= 0 ? 'Crear Solicitud' : 'Generar QR de Pago' }}
        </button>
      </div>
    </div>

    <!-- Layout: Formulario + Panel QR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Formulario (2/3 del ancho) -->
      <div class="lg:col-span-2">
        <div class="rounded-md shadow overflow-hidden">
          <!-- Modificado @submit.prevent: Llama a crearSolicitud si es gratuito, sino a generarQR -->
          <form @submit.prevent="tramiteCosto <= 0 ? crearSolicitud() : generarQR()" id="formSolicitud" enctype="multipart/form-data">
            <div class="flex flex-wrap -mb-8 -mr-6 p-8">

              <!-- **Error General de API** -->
              <div v-if="errors.apiError" class="alert alert-error mb-6 w-full shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ errors.apiError }}</span>
              </div>

              <!-- Título -->
              <div class="form-control pb-8 pr-6 w-full">
                <label class="label font-semibold">Título de la Solicitud</label>
                <input
                  v-model="form.nombre"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.nombre }"
                  placeholder="Ingrese un título"
                  :disabled="mostrarQR && tramiteCosto > 0"
                />
                <label v-if="errors.nombre" class="label">
                  <span class="label-text-alt text-error">{{ errors.nombre }}</span>
                </label>
              </div>

              <!-- Trámite ID (Dropdown) -->
              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Trámite</label>
                <select
                  v-model="form.tramite_id"
                  class="select select-bordered w-full"
                  :class="{ 'select-error': errors.tramite_id }"
                  :disabled="mostrarQR && tramiteCosto > 0"
                >
                  <option :value="''" disabled>Seleccione un trámite</option>
                  <option
                    v-for="tramite in props.tramites"
                    :key="tramite.id"
                    :value="tramite.id"
                  >
                    {{ tramite.nombre }} (Bs. {{ parseFloat(tramite.costo).toFixed(2) }})
                  </option>
                </select>
                <label v-if="errors.tramite_id" class="label">
                  <span class="label-text-alt text-error">{{ errors.tramite_id }}</span>
                </label>
              </div>

              <!-- Usuario ID (Dropdown) -->
              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Usuario Solicitante</label>
                <select
                  v-model="form.usuario_id"
                  class="select select-bordered w-full"
                  :class="{ 'select-error': errors.usuario_id }"
                  :disabled="mostrarQR && tramiteCosto > 0"
                >
                  <option :value="''" disabled>Seleccione un usuario</option>
                  <option
                    v-for="usuario in props.usuarios"
                    :key="usuario.id"
                    :value="usuario.id"
                  >
                    {{ usuario.first_name }} {{ usuario.last_name }} (ID: {{ usuario.id }})
                  </option>
                </select>
                <label v-if="errors.usuario_id" class="label">
                  <span class="label-text-alt text-error">{{ errors.usuario_id }}</span>
                </label>
              </div>

              <!-- Datos de Cliente para Pago Facil -->
              <div v-if="tramiteCosto > 0" class="w-full">
                <h3 class="text-md font-bold mb-4 mt-2">Datos de Pago (Requeridos para QR)</h3>
              </div>

              <div v-if="tramiteCosto > 0" class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Nombre Completo (Pago)</label>
                <input
                  type="text"
                  v-model="form.client_name"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.client_name }"
                  placeholder="Ej: Juan Pérez"
                  :disabled="mostrarQR && tramiteCosto > 0"
                />
                <label v-if="errors.client_name" class="label">
                  <span class="label-text-alt text-error">{{ errors.client_name }}</span>
                </label>
              </div>

              <div v-if="tramiteCosto > 0" class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">CI / RUC</label>
                <input
                  type="text"
                  v-model="form.document_id"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.document_id }"
                  placeholder="Ej: 12345678"
                  :disabled="mostrarQR && tramiteCosto > 0"
                />
                <label v-if="errors.document_id" class="label">
                  <span class="label-text-alt text-error">{{ errors.document_id }}</span>
                </label>
              </div>

              <div v-if="tramiteCosto > 0" class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Correo Electrónico</label>
                <input
                  type="email"
                  v-model="form.email"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.email }"
                  placeholder="Ej: correo@dominio.com"
                  :disabled="mostrarQR && tramiteCosto > 0"
                />
                <label v-if="errors.email" class="label">
                  <span class="label-text-alt text-error">{{ errors.email }}</span>
                </label>
              </div>
              <!-- Fin Datos de Pago Facil -->

              <!-- Estado Actual -->
              <div class="form-control pb-8 pr-6 w-full lg:w-1/2">
                <label class="label font-semibold">Estado Inicial</label>
                <input
                  type="text"
                  v-model="form.estado_actual"
                  class="input input-bordered w-full"
                  :class="{ 'input-error': errors.estado_actual }"
                  :disabled="mostrarQR && tramiteCosto > 0"
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
                  :disabled="mostrarQR && tramiteCosto > 0"
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
                :disabled="(mostrarQR && tramiteCosto > 0) || form.processing"
              >
                <svg v-if="!mostrarQR" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                {{ mostrarQR && tramiteCosto > 0 ? 'QR Generado' : tramiteCosto <= 0 ? 'Crear Solicitud' : 'Generar QR de Pago' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Panel QR (1/3 del ancho) -->
      <div v-if="mostrarQR" class="lg:col-span-1">
        <div class="card bg-base-200 shadow-xl sticky top-4">
          <div class="card-body">
            <h2 class="card-title text-center">{{ tramiteCosto <= 0 ? 'Trámite Gratuito' : 'Pago del Trámite' }}</h2>

            <!-- QR Code (Solo visible si requiere pago) -->
            <div v-if="tramiteCosto > 0" class="flex justify-center my-4">
              <div class="bg-white p-4 rounded-lg shadow-inner">
                <img
                  v-if="qrCode"
                  :src="'data:image/png;base64,' + qrCode"
                  alt="QR de Pago"
                  class="w-48 h-48"
                />
                <div v-else class="w-48 h-48 flex items-center justify-center text-gray-500">
                    Cargando QR...
                </div>
              </div>
            </div>
            <div v-else class="flex justify-center my-4">
                <p class="text-lg font-semibold text-success">No requiere pago</p>
            </div>

            <!-- Información del pago -->
            <div class="alert alert-info text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div>
                <div class="font-bold">Monto: Bs. {{ montoPago }}</div>
                <div class="text-xs">{{ tramiteCosto > 0 ? 'Escanea el QR para proceder' : 'Listo para enviar la solicitud' }}</div>
              </div>
            </div>

            <!-- Estado del pago (Solo visible si requiere pago) -->
            <template v-if="tramiteCosto > 0">
                <div v-if="verificandoPago" class="alert alert-warning text-sm">
                  <span class="loading loading-spinner loading-sm"></span>
                  <span>Verificando pago...</span>
                </div>

                <div v-if="pagoRealizado" class="alert alert-success text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>¡Pago verificado! Número: {{ paymentNumber }}</span>
                </div>
            </template>
            <div v-else class="alert alert-success text-sm">
                <span>Trámite gratuito. ¡Adelante!</span>
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

            <!-- Botón simular pago (desarrollo, solo si requiere pago) -->
            <button
              v-if="tramiteCosto > 0 && !pagoRealizado && !verificandoPago"
              @click="simularPago"
              class="btn btn-outline btn-xs mt-2"
            >
              Simular Pago (Dev)
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import Layout from '../../Shared/Layout.vue'

// Dependencia: Asegúrate de tener la etiqueta <meta name="csrf-token" content="{{ csrf_token() }}">
// en tu layout principal de Laravel.

// Definir Props para recibir los datos del Controller
const props = defineProps({
  tramites: {
    type: Array,
    required: true,
  },
  usuarios: {
    type: Array,
    required: true,
  }
})

defineOptions({
  layout: Layout,
})

const form = useForm({
  nombre: '',
  tramite_id: '',
  usuario_id: '',
  estado_actual: '',
  documentos: [],
  // Campos requeridos por PagoFacilController
  client_name: '',
  document_id: '',
  email: '',
})

const previews = ref([])
// Inicializamos errors.apiError
const errors = ref({
    apiError: null
})
const mostrarQR = ref(false)
const qrCode = ref('')
const pagoRealizado = ref(false)
const verificandoPago = ref(false)
const tramiteCosto = ref(0.00)
const paymentNumber = ref(null); // ID de transacción para verificación
let intervaloPago = null

// Propiedad Computada para el Monto (formateado)
const montoPago = computed(() => tramiteCosto.value.toFixed(2));

// Watcher para actualizar el costo al cambiar el Trámite
watch(() => form.tramite_id, (newTramiteId) => {
  // Limpiar estado al cambiar el trámite
  mostrarQR.value = false;
  pagoRealizado.value = false;
  verificandoPago.value = false;
  qrCode.value = '';
  paymentNumber.value = null;
  errors.value.apiError = null; // Limpiar error de API
  if (intervaloPago) {
    clearInterval(intervaloPago);
    intervaloPago = null;
  }

  const selectedTramite = props.tramites.find(t => t.id == newTramiteId);
  if (selectedTramite) {
    tramiteCosto.value = parseFloat(selectedTramite.costo);
  } else {
    tramiteCosto.value = 0.00;
  }
}, { immediate: true });

const handleFiles = (e) => {
  form.documentos = Array.from(e.target.files)
  previews.value = form.documentos.map((file) => ({
    url: URL.createObjectURL(file),
    type: file.type,
  }))
}

const isImage = (type) => type.startsWith('image/')

const validarForm = () => {
  errors.value = { apiError: errors.value.apiError }; // Mantener el error de API pero limpiar la validación

  if (!form.nombre || form.nombre.trim() === '') {
    errors.value.nombre = 'El título es requerido'
  }

  if (!form.tramite_id) {
    errors.value.tramite_id = 'Debe seleccionar un trámite'
  }

  if (!form.usuario_id) {
    errors.value.usuario_id = 'Debe seleccionar un usuario'
  }

  if (!form.estado_actual || form.estado_actual.trim() === '') {
    errors.value.estado_actual = 'El estado actual es requerido'
  }

  // Validación de campos de pago solo si el trámite tiene costo
  if (tramiteCosto.value > 0) {
      if (!form.client_name || form.client_name.trim() === '') {
          errors.value.client_name = 'El nombre del cliente es requerido para el pago.'
      }
      if (!form.document_id || form.document_id.trim() === '') {
          errors.value.document_id = 'La CI/RUC es requerida para el pago.'
      }
      if (!form.email || form.email.trim() === '') {
          errors.value.email = 'El email es requerido para el pago.'
      }
  }

  // Contar solo los errores de validación de formulario (excluyendo apiError)
  const validationErrors = Object.keys(errors.value).filter(key => key !== 'apiError' && errors.value[key]);

  return validationErrors.length === 0
}

const generarQR = () => { // Ya no necesita ser async si usas router.post
  console.log('Generando QR para el pago...');
  // Limpiar error anterior de API
  errors.value.apiError = null;

  if (!validarForm()) {
    return
  }

  // Lógica para Trámites Gratuitos (sin cambios)
  if (tramiteCosto.value <= 0) {
    pagoRealizado.value = true;
    mostrarQR.value = true;
    return;
  }

  // --- Lógica para Trámites con Costo (Llamada al Backend) ---

  const amountValue = montoPago.value;
  const tramite = props.tramites.find(t => t.id == form.tramite_id);

  // Crear detalle de la orden
  const orderDetail = [{
    serial: 1,
    product: tramite ? `Trámite: ${tramite.nombre}` : form.nombre,
    quantity: 1,
    price: parseFloat(amountValue),
    discount: 0,
    total: parseFloat(amountValue),
  }];

  // Mostrar spinner en el panel QR y deshabilitar form
  mostrarQR.value = true;
  verificandoPago.value = true;
  qrCode.value = ''; // Limpiar QR anterior


  router.post('/api/pagofacil/generar-qr', {
    client_name: form.client_name,
    document_id: form.document_id,
    email: form.email,
    amount: parseFloat(amountValue),
    order_detail: orderDetail,
    // Datos adicionales para el backend
    tramite_id: form.tramite_id,
    usuario_id: form.usuario_id,
  }, {
    // Estas opciones son cruciales para evitar una recarga completa del componente.
    preserveState: true,
    preserveScroll: true,

    onSuccess: (page) => {
      // ⚠️ ADVERTENCIA: Debes asegurarte de que tu controlador Laravel devuelva
      // los datos del QR como props o flash data de Inertia.
      const result = page.props.flash?.qrResult || page.props.qrResult;

      console.log("Resultado de la API de generación de QR:");
      console.log(page.props);

      if (result && result.success) {
        qrCode.value = result.data.qr_image;
        paymentNumber.value = result.data.payment_number;
        iniciarVerificacionPago();
      } else if (result) {
         console.error("Error al generar QR:", result);
         errors.value.apiError = `Error al generar QR: ${result.message || 'Verifique los datos de pago y reintente.'}`;
         mostrarQR.value = false;
         verificandoPago.value = false;
      }
    },
    onError: (errors) => {
      // Los errores de validación y API se manejan aquí
      console.error('Error de Inertia/API:', errors);
      // errors.apiError se actualizará si el controlador devuelve un error de flash
      errors.value.apiError = errors.value.apiError || 'Error al procesar la solicitud.';
      mostrarQR.value = false;
      verificandoPago.value = false;
    },
    onFinish: () => {
      // Este callback se ejecuta siempre al finalizar, útil para spinners
    }
  });
}

const iniciarVerificacionPago = () => {
  if (intervaloPago) {
    clearInterval(intervaloPago);
  }

  let intentos = 0;
  const maxIntentos = 30; // 30 intentos x 2 segundos = 60 segundos (1 minuto)

  // Verificar cada 2 segundos
  intervaloPago = setInterval(() => {
    intentos++;

    if (intentos > maxIntentos) {
      // Detener verificación después de 1 minuto
      clearInterval(intervaloPago);
      intervaloPago = null;
      verificandoPago.value = false;

      // Opcional: Mostrar mensaje al usuario
      errors.value.apiError = 'Tiempo de espera agotado. Por favor, verifica manualmente el estado del pago.';
      return;
    }

    verificarPago();
  }, 2000); // 2000ms = 2 segundos
}

const verificarPago = async () => {
  if (!paymentNumber.value) return;

  try {
    const response = await fetch(`/api/pagofacil/verificar-qr/${paymentNumber.value}`);

    const result = await response.json();

    if (response.ok && result.success) {
      if (result.pagado) {
        // ¡Pago confirmado!
        pagoRealizado.value = true;
        verificandoPago.value = false;

        // Detener el polling
        if (intervaloPago) {
          clearInterval(intervaloPago);
          intervaloPago = null;
        }

        console.log('Pago verificado exitosamente:', result);
      }
    } else {
      console.error('Error en la API de verificación:', result);
    }

  } catch (error) {
    console.error('Error de red/polling:', error);
  }
}

const simularPago = () => {
  // Solo para desarrollo: simular que el callback llegó
  pagoRealizado.value = true
}

const crearSolicitud = () => {
  if (!pagoRealizado.value) {
    return
  }

  // Nota: Los archivos se envían correctamente gracias a forceFormData: true

  form.post('/solicitudes', {
    forceFormData: true,
    onSuccess: () => {
      if (intervaloPago) {
        clearInterval(intervaloPago)
      }
    }
  })
}

// Limpiar intervalo cuando se destruya el componente
onBeforeUnmount(() => {
  if (intervaloPago) {
    clearInterval(intervaloPago)
  }
})
</script>
