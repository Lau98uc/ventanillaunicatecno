<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class PagoFacilService
{
    private $apiUrl;
    private $tokenService;
    private $tokenSecret;
    private $callbackUrl;

    public function __construct()
    {
        $this->apiUrl = config('pagofacil.api_url');
        $this->tokenService = config('pagofacil.token_service');
        $this->tokenSecret = config('pagofacil.token_secret');
        $this->callbackUrl = config('pagofacil.callback_url');
    }

    /**
     * Obtener token de autenticación
     */
    public function getAuthToken()
    {
        Cache::forget('pagofacil_token'); // FORZAR renovación del token (para pruebas)
        return Cache::remember('pagofacil_token', 60, function () {
            try {
                $response = Http::withHeaders([
                    'tcTokenService' => $this->tokenService,
                    'tcTokenSecret' => $this->tokenSecret,
                ])->post("{$this->apiUrl}/login");

                if ($response->successful()) {
                    $data = $response->json();

                    Log::info('Token PagoFácil obtenido', ['response' => $data]);

                    return $data['values']['accessToken'] ?? $data['token'] ?? null;
                }

                Log::error('Error al obtener token', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                throw new Exception('No se pudo obtener el token');

            } catch (Exception $e) {
                Log::error('Excepción al obtener token: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Generar código QR para pago
     */
    public function generarQR(array $data)
    {
        try {
            $token = $this->getAuthToken();
            // print("data ");
            // Generar un payment_number único (timestamp + random)
            $paymentNumber = time() . '-' . rand(1000, 9999);

            $payload = [
                'paymentMethod' => $data['payment_method'] ?? 4,
                'clientName' => $data['client_name'],
                'documentType' => $data['document_type'] ?? 1,
                'documentId' => $data['document_id'],
                'phoneNumber' => $data['phone_number'] ?? '73973131',
                'email' => $data['email'],
                'paymentNumber' => $paymentNumber, // ID único de la transacción
                'amount' => (float) $data['amount'],
                'currency' => $data['currency'] ?? 2,
                'clientCode' => $data['client_code'] ?? $data['document_id'],
                'callbackUrl' => $this->callbackUrl ?? null,
            ];

            Log::info('Generando QR', ['payload' => $payload]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/generate-qr", $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('QR generado exitosamente', ['response' => $responseData]);

                return [
                    'success' => true,
                    'payment_number' => $paymentNumber,
                    'data' => $responseData
                ];
            }

            Log::error('Error al generar QR', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Error al generar QR',
                'details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('Excepción al generar QR: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Consultar estado de pago
     *
     * @param string $paymentNumber ID único de la transacción
     */
    public function verificarPago($paymentNumber)
    {
        try {
            $token = $this->getAuthToken();

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])->get("{$this->apiUrl}/check-status/{$paymentNumber}");

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Estado consultado', [
                    'payment_number' => $paymentNumber,
                    'response' => $data
                ]);

                return [
                    'success' => true,
                    'pagado' => $data['estado'] == 2, // 2 = pagado
                    'estado' => $data['estado'],
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'pagado' => false,
                'error' => 'No se pudo consultar el estado'
            ];

        } catch (Exception $e) {
            Log::error('Error al verificar pago: ' . $e->getMessage());

            return [
                'success' => false,
                'pagado' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
