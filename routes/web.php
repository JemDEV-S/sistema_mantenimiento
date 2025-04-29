<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth'])->group(function () {

    Route::resource('users', UserController::class);

    // Roles
    Route::resource('roles', RoleController::class)->except(['show']);

    // Departamentos
    Route::resource('departments', DepartmentController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Assets
    Route::get('/assets/sync', [AssetController::class, 'sync'])->name('assets.sync');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    
    
    // Maintenances
    Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
    Route::get('/maintenances/create/{asset_id?}', [MaintenanceController::class, 'create'])->name('maintenances.create');
    Route::post('/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
    Route::get('/maintenances/{id}', [MaintenanceController::class, 'show'])->name('maintenances.show');
    Route::get('/maintenances/{id}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
    Route::put('/maintenances/{id}', [MaintenanceController::class, 'update'])->name('maintenances.update');
    Route::put('/maintenances/{id}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
    
    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{id}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/create/{maintenance_id}', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates/{maintenance_id}', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{id}/sign', [CertificateController::class, 'sign'])->name('certificates.sign');
    Route::post('/certificates/{id}/sign', [CertificateController::class, 'registerSignature'])->name('certificates.registerSignature');
    Route::get('/certificates/{id}/download', [CertificateController::class, 'download'])->name('certificates.download');

    
});

// Rutas del nuevo flujo de mantenimiento (agregar a routes/web.php)

// Flujo mejorado de mantenimiento
Route::middleware(['auth'])->prefix('maintenance-flow')->name('maintenance-flow.')->group(function () {
    // Selección de activo
    Route::get('/select-asset', [App\Http\Controllers\MaintenanceFlowController::class, 'selectAsset'])
        ->name('select-asset');
    
    // Búsqueda de activos (para AJAX)
    Route::get('/search-assets', [App\Http\Controllers\MaintenanceFlowController::class, 'searchAssets'])
        ->name('search-assets');
    
    // Buscar por código QR/barras
    Route::post('/find-by-code', [App\Http\Controllers\MaintenanceFlowController::class, 'findByCode'])
        ->name('find-by-code');
    
    // Crear nuevo activo
    Route::get('/create-asset', [App\Http\Controllers\MaintenanceFlowController::class, 'createAsset'])
        ->name('create-asset');
    Route::post('/store-asset', [App\Http\Controllers\MaintenanceFlowController::class, 'storeAsset'])
        ->name('store-asset');
    
    // Formulario de mantenimiento
    Route::get('/maintenance-form/{asset_id}', [App\Http\Controllers\MaintenanceFlowController::class, 'maintenanceForm'])
        ->name('maintenance-form');
    Route::post('/store-maintenance/{asset_id}', [App\Http\Controllers\MaintenanceFlowController::class, 'storeMaintenance'])
        ->name('store-maintenance');
    
    // Editar mantenimiento
    Route::get('/maintenance-form-edit/{maintenance_id}', [App\Http\Controllers\MaintenanceFlowController::class, 'maintenanceFormEdit'])
        ->name('maintenance-form-edit');
    Route::put('/update-maintenance/{maintenance_id}', [App\Http\Controllers\MaintenanceFlowController::class, 'updateMaintenance'])
        ->name('update-maintenance');
    
    // Confirmación
    Route::get('/confirmation/{maintenance_id}', [App\Http\Controllers\MaintenanceFlowController::class, 'confirmation'])
        ->name('confirmation');
});

// Route::get('/', function () {
//     return view('welcome');
// });
