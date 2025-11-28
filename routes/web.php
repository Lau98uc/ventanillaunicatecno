<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ResponsablesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UnidadesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SolicitudTramiteController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// routes/web.php
// routes/web.php

// ... otras rutas

// Rutas nombradas para las operaciones AJAX/Fetch
Route::prefix('api/pagofacil')->group(function () {
    // Generar QR (POST)
    Route::post('/generar-qr', [App\Http\Controllers\PagoFacilController::class, 'generarQR'])
        ->name('pagofacil.generar-qr');

    // Verificar Pago Rápido (GET)
    Route::get('/verificar-qr/{paymentNumber}', [App\Http\Controllers\PagoFacilController::class, 'verificarPagoRapido'])
        ->name('pagofacil.verificar-pago');
});

// ... otras rutas que usan inertia

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
// Images públicas (fuera del middleware)
Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

// Ruta de API pública para pruebas
Route::get('/api/public-test', function() {
    return response()->json(['message' => 'API pública funcionando', 'timestamp' => now()]);
})->name('api.public-test');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('users', [UsersController::class, 'index'])->name('users')->middleware('permission:seguridad');
    Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('users', [UsersController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore');

    // Unidades
    Route::get('unidades', [UnidadesController::class, 'index'])->name('unidades');
    Route::get('unidades/create', [UnidadesController::class, 'create'])->name('unidades.create');
    Route::post('unidades', [UnidadesController::class, 'store'])->name('unidades.store');
    Route::get('unidades/{unidad}/edit', [UnidadesController::class, 'edit'])->name('unidades.edit');
    Route::put('unidades/{unidad}', [UnidadesController::class, 'update'])->name('unidades.update');
    Route::delete('unidades/{unidad}', [UnidadesController::class, 'destroy'])->name('unidades.destroy');
    Route::put('unidades/{unidad}/restore', [UnidadesController::class, 'restore'])->name('unidades.restore');

    // Responsables
    Route::get('responsables', [ResponsablesController::class, 'index'])->name('responsables');
    Route::get('responsables/create', [ResponsablesController::class, 'create'])->name('responsables.create');
    Route::post('responsables', [ResponsablesController::class, 'store'])->name('responsables.store');
    Route::get('responsables/{responsable}/edit', [ResponsablesController::class, 'edit'])->name('responsables.edit');
    Route::put('responsables/{responsable}', [ResponsablesController::class, 'update'])->name('responsables.update');
    Route::delete('responsables/{responsable}', [ResponsablesController::class, 'destroy'])->name('responsables.destroy');
    Route::put('responsables/{responsable}/restore', [ResponsablesController::class, 'restore'])->name('responsables.restore');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Permisos
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // Trámites
    Route::get('/tramites', [TramiteController::class, 'index'])->name('tramites');
    Route::get('/tramites/create', [TramiteController::class, 'create'])->name('tramites.create');
    Route::get('/tramites/{tramite}', [TramiteController::class, 'show'])->name('tramites.show');
    Route::get('/tramites/{tramite}/edit', [TramiteController::class, 'edit'])->name('tramites.edit');

    // Workflows
    Route::get('/workflows', [WorkflowController::class, 'index'])->name('workflows.index');
    Route::get('/workflows/create', [WorkflowController::class, 'create'])->name('workflows.create');
    Route::post('/workflows', [WorkflowController::class, 'store'])->name('workflows.store');
    Route::get('/workflows/{workflow}', [WorkflowController::class, 'show'])->name('workflows.show');
    Route::get('/workflows/{workflow}/edit', [WorkflowController::class, 'edit'])->name('workflows.edit');
    Route::put('/workflows/{workflow}', [WorkflowController::class, 'update'])->name('workflows.update');
    Route::delete('/workflows/{workflow}', [WorkflowController::class, 'destroy'])->name('workflows.destroy');

    // Documentos
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
    Route::get('/documentos/{documento}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit');
    Route::put('/documentos/{documento}', [DocumentoController::class, 'update'])->name('documentos.update');
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Solicitudes
    Route::get('/solicitudes', [SolicitudTramiteController::class, 'index'])->name('solicitudes.index');
    Route::post('/solicitudes', [SolicitudTramiteController::class, 'store'])->name('solicitudes.create');
    Route::get('/solicitudes/create', [SolicitudTramiteController::class, 'create'])->name('solicitudes.create');
    Route::get('/solicitudes/{solicitud_tramite}', [SolicitudTramiteController::class, 'show'])->name('solicitudes.show');
    Route::post('/solicitudes/{solicitud}/execute', [SolicitudTramiteController::class, 'execute'])->name('solicitudes.execute');
    Route::post('/solicitudes/{solicitud_tramite}/acciones', [SolicitudTramiteController::class, 'ejecutarAccion'])->name('tramites.ejecutarAccion');

    // Reportes
    Route::get('/reportes', [ReportsController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/por_estados', [ReportsController::class, 'por_estados'])->name('reportes.por_estados');
    Route::get('/reportes/por_tramites', [ReportsController::class, 'porTramites'])->name('reportes.por_tramites');

    // Búsqueda Global
    Route::get('/search', [SearchController::class, 'show'])->name('search.show');
    Route::get('/api/search', [SearchController::class, 'search'])->name('search.api');

    // Ruta de prueba para verificar si el problema es de autenticación
    Route::get('/api/test', function() {
        return response()->json(['message' => 'API funcionando']);
    })->name('api.test');
});


Route::get('/pdf-demo', function () {
    $data = [
        'usuario' => 'Eddy',
        'fecha' => now()->format('d/m/Y H:i')
    ];
    $pdf = Pdf::loadView('mi_reporte', $data);
    return $pdf->download('reporte_demo.pdf');
});


Route::post('/documentos/upload', [SolicitudTramiteController::class, 'uploadDocumentos'])->name('documentos.upload');


Route::post('/upload/xd', function (Request $request) {
    try {

        $file = $request->file('archivo');

        $contenido = file_get_contents($file);

        $contenid =Storage::disk('public')->put('file.jpg', $contenido);

        $url = Storage::url('file.jpg');

        print_r($url);

        exit;
        if (!$request->hasFile('archivo')) {
            return response()->json(['error' => 'No se recibió ningún archivo'], 400);
        }

        $file = $request->file('archivo');

        if (!$file->isValid()) {
            return response()->json(['error' => 'El archivo no es válido'], 400);
        }

        // Obtener contenido del archivo y nombre original
        $contenido = file_get_contents($file->getRealPath());
        $nombre = $file->getClientOriginalName();

        // Guardar usando put (más directo)
        $path = 'prueba/' . $nombre;
        $resultado = Storage::disk('local')->put('/edd', $contenido);

        if (!$resultado) {
            return response()->json(['error' => 'Error al guardar con put()'], 500);
        }

        return response()->json([
            'mensaje' => 'Archivo guardado correctamente',
            'ruta_publica' => Storage::url($path),
            'path_interno' => $path
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => $th->getMessage(),
            'linea' => $th->getLine(),
            'archivo' => $th->getFile(),
        ], 500);
    }
});
