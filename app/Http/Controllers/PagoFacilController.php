<?php

namespace App\Http\Controllers;

use App\Models\TransaccionQR;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect; // Importar Redirect

class PagoFacilController extends Controller
{
    protected $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    /**
     * Generar QR para pago (Ahora es compatible con Inertia y JSON/Fetch)
     */
    public function generarQR(Request $request)
    {
        // 1. Validación de la solicitud (Mantener la validación)
        $request->validate([
            // ... (Reglas de validación sin cambios)
            'client_name' => 'required|string',
            'document_id' => 'required|string',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0',
            'order_detail' => 'required|array',
            'order_detail.*.serial' => 'required|integer',
            'order_detail.*.product' => 'required|string',
            'order_detail.*.quantity' => 'required|integer|min:1',
            'order_detail.*.price' => 'required|numeric',
            'order_detail.*.discount' => 'required|numeric|min:0',
            'order_detail.*.total' => 'required|numeric',
        ]);

        try {
            // 2. Lógica de Servicio (Sin cambios)
            $resultado = $this->pagoFacilService->generarQR([
                'payment_method' => 4, // QR
                'client_name' => $request->client_name,
                'document_type' => 1, // CI
                'document_id' => $request->document_id,
                'phone_number' => $request->phone_number ?? '73973131',
                'email' => $request->email,
                'amount' => (float) $request->amount,
                'currency' => 2, // Bolivianos
                'client_code' => "Grupo23SA",
                'callback_url' => "https://tu-dominio.com/callback",
            ]);
            // 3. Preparar la Respuesta de la API

            $transaccion = TransaccionQR::create([
                'payment_number' => $resultado['payment_number'],
                'transaction_id' => $resultado['data']['values']['transactionId'] ?? null,
                'monto' => $request->amount,
                'estado' => TransaccionQR::ESTADO_PENDIENTE,
                'client_name' => $request->client_name,
                'document_id' => $request->document_id,
                'email' => $request->email,
                'tramite_id' => $request->tramite_id,
                'usuario_id' => $request->usuario_id,
                'qr_image' => $resultado['data']['values']['qrBase64'] ?? null,
                'qr_url' => $resultado['data']['qrUrl'] ?? null,
                'expires_at' => $resultado['data']['values']['expirationDate'] ?? null,
                'order_detail' => $request->order_detail,
                'response_data' => $resultado['data'] ?? null,
            ]);

            $apiResponse = [
                'success' => $resultado['success'],
                'message' => $resultado['success'] ? 'QR generado exitosamente' : 'Error al generar QR',
                'data' => [
                    'payment_number' => $resultado['payment_number'] ?? null,
                    'qr_image' => $resultado['data']['values']['qrBase64'] ?? null,
                    'qr_url' => $resultado['data']['qrUrl'] ?? null,
                    'amount' => $request->amount,
                    'expires_at' => $resultado['data']['values']['expirationDate'] ?? null,
                    'transaction_id' => $resultado['data']['values']['transactionId'] ?? null,
                ]
            ];

            // Si hubo un error en el servicio, ajustar el mensaje y el código de estado
            if (!$resultado['success']) {
                 $apiResponse['details'] = $resultado['error'];
                 $statusCode = 400;
            } else {
                 $statusCode = 200;
            }

            // if ($request->header('X-Inertia')) {
                // Retorna una redirección suave con los resultados de la API como 'flash data'
                return Redirect::back()->with('qrResult', $apiResponse);
            // }

            // 🟢 OPCIÓN 2: Petición Inertia (router.post/get)
            // Redirigir hacia atrás, pero adjuntar los resultados de la API como 'flash data'
            // return response()->json($apiResponse, $statusCode);


        } catch (\Exception $e) {
            $errorResponse = [
                'error' => true,
                'message' => 'Error al procesar la solicitud',
                'details' => $e->getMessage()
            ];

            // Si es Inertia, redirige hacia atrás con error flash.
            if ($request->header('x-inertia')) {
                // Redirige con los errores de validación estándar de Inertia.
                return Redirect::back()->withErrors(['apiError' => $errorResponse['message']])->withInput();
            }

            // Si es JSON, devuelve la respuesta de error JSON.
            return response()->json($errorResponse, 500);
        }
    }

       public function notificacionPagoFacil(Request $request)
    {
        Log::info("Callback PagoFacil Recibido:", $request->all());

        try {
            $pedidoID = $request->input('PedidoID');
            $estado = $request->input('Estado');

            if (empty($pedidoID) || empty($estado)) {
                return response()->json([
                    'error' => 1,
                    'status' => 0,
                    'message' => 'Parámetros inválidos',
                    'values' => false
                ], 400);
            }

            // Buscar la transacción
            $transaccion = TransaccionQR::where('payment_number', $pedidoID)->first();

            if (!$transaccion) {
                Log::warning("Transacción no encontrada: $pedidoID");
                // Aún así devolvemos 200 para no generar reintentos
                return response()->json([
                    'error' => 0,
                    'status' => 1,
                    'message' => 'Transacción no encontrada en la BD',
                    'values' => true
                ], 200);
            }

            // Estado 2 = Pagado (ajustar según tu documentación de PagoFacil)
            if ($estado == 2) {
                $transaccion->marcarComoPagado();

                Log::info('Transacción Actualizada a PAGADO:', [
                    'payment_number' => $pedidoID,
                    'transaccion_id' => $transaccion->id
                ]);

                return response()->json([
                    'error' => 0,
                    'status' => 1,
                    'message' => 'Notificación de pago recibida y procesada correctamente.',
                    'values' => true
                ], 200);
            } else {
                // Estado pendiente o rechazado
                Log::info("Estado $estado recibido para transacción $pedidoID");

                return response()->json([
                    'error' => 0,
                    'status' => 1,
                    'message' => "Estado $estado recibido, transacción no completada.",
                    'values' => true
                ], 200);
            }

        } catch (\Exception $e) {
            Log::error('Error FATAL en el Callback: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);

            // Devolver siempre 200 OK para evitar reintentos
            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => 'Error interno del servidor al procesar la notificación.',
                'values' => false
            ], 200);
        }
    }

        public function verificarPagoRapido($paymentNumber)
    {
        try {
            $transaccion = TransaccionQR::where('payment_number', $paymentNumber)->first();

            if (!$transaccion) {
                return response()->json([
                    'error' => true,
                    'message' => 'Transacción no encontrada',
                    'pagado' => false
                ], 404);
            }

            // Verificar si expiró
            if ($transaccion->estaExpirado() && $transaccion->estaPendiente()) {
                $transaccion->update(['estado' => TransaccionQR::ESTADO_EXPIRADO]);
            }

            return response()->json([
                'success' => true,
                'pagado' => $transaccion->estaPagado(),
                'estado' => $transaccion->estado,
                'payment_number' => $transaccion->payment_number,
                'pagado_at' => $transaccion->pagado_at,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar pago: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Error al verificar el pago',
                'pagado' => false
            ], 500);
        }
    }

}
