<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

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

// Ruta principal para configuración de impresora
Route::get('/', [PosController::class, 'mostrarConfiguracion'])->name('home');

// Rutas para configuración
Route::post('/guardar-config', [PosController::class, 'guardarConfiguracion'])->name('guardar-config');
Route::post('/test-print', [PosController::class, 'imprimirPrueba'])->name('test-print');
Route::get('/detectar-impresoras', [PosController::class, 'detectarImpresoras'])->name('detectar-impresoras');

// Rutas de impresión
Route::get('/venta/{venta}', [PosController::class, 'imprimirVenta'])->name('venta');
Route::get('/boleta/{prestamo}', [PosController::class, 'imprimirBoleta'])->name('boleta');
Route::get('/transferencia/{transferencia}', [PosController::class, 'imprimirTransferencia'])->name('transferencia');
