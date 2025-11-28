<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagoFacilController;// Importar el nuevo controlador

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Endpoint de notificaciones de Pago Fácil (CALLBACK/WEBHOOK)
// ¡Importante! Esta ruta NO debe tener el middleware 'web' para evitar la protección CSRF
Route::post('/notificacionesPagoFacil', [PagoFacilController::class, 'notificacionPagoFacil']);