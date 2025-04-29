@extends('layouts.app')

@section('styles')
<style>
    .asset-card {
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .asset-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .asset-selection-methods {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .method-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 0.5rem;
        flex: 1;
        min-width: 200px;
        max-width: 300px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .method-card:hover {
        transform: scale(1.05);
    }
    
    .method-card i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .scanner-container {
        max-width: 500px;
        margin: 0 auto;
    }
    
    #scanner-preview {
        width: 100%;
        border: 2px solid #ddd;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .search-results {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .result-item {
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }
    
    .result-item:hover {
        background-color: rgba(0,123,255,0.1);
        border-left: 3px solid #007bff;
    }
    
    .type-icon {
        font-size: 2rem;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .asset-status {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
    }
    
    #loading-indicator {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255,255,255,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }
    
    .pulse {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.7;
        }
        50% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(0.95);
            opacity: 0.7;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Registro de Mantenimiento - Selección de Activo</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    
    <!-- Indicador de carga -->
    <div id="loading-indicator">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h4 class="text-primary">Procesando su solicitud...</h4>
        </div>
    </div>
    
    <!-- Métodos de selección -->
    <div class="asset-selection-methods">
        <div class="method-card bg-light" id="search-method" data-method="search">
            <i class="fas fa-search text-primary"></i>
            <h4>Búsqueda</h4>
            <p class="text-muted">Buscar activo por nombre, código o número de serie</p>
        </div>
        <div class="method-card bg-light" id="scan-method" data-method="scan">
            <i class="fas fa-qrcode text-success"></i>
            <h4>Escanear Código</h4>
            <p class="text-muted">Usar la cámara para escanear código QR o barras</p>
        </div>
        <div class="method-card bg-light" id="create-method" data-method="create">
            <i class="fas fa-plus-circle text-danger"></i>
            <h4>Nuevo Activo</h4>
            <p class="text-muted">Registrar un activo que no está en el sistema</p>
        </div>
    </div>
    
    <!-- Contenedor para cada método -->
    <div class="method-content" id="search-content">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-search me-1"></i> Buscar Activo
            </div>
            <div class="card-body">
                <form id="search-form" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" placeholder="Nombre, serial, código patrimonial...">
                    </div>
                    <div class="col-md-2">
                        <label for="type-filter" class="form-label">Tipo</label>
                        <select class="form-select" id="type-filter">
                            <option value="">Todos</option>
                            @foreach($assetTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="department-filter" class="form-label">Departamento</label>
                        <select class="form-select" id="department-filter">
                            <option value="">Todos</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status-filter" class="form-label">Estado</label>
                        <select class="form-select" id="status-filter">
                            <option value="">Todos</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                            <option value="in_maintenance">En Mantenimiento</option>
                            <option value="decommissioned">De Baja</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <button type="button" id="clear-filters" class="btn btn-outline-secondary">
                            Limpiar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Resultados de búsqueda -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-1"></i> Resultados de la Búsqueda
                </div>
                <span class="badge bg-primary" id="results-count">0</span>
            </div>
            <div class="card-body search-results p-0">
                <div id="search-results" class="list-group list-group-flush">
                    <div class="text-center p-4 text-muted">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <p>Utilice los filtros para buscar activos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="method-content" id="scan-content" style="display: none;">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-qrcode me-1"></i> Escanear Código QR o Barras
            </div>
            <div class="card-body">
                <div class="scanner-container">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Coloque el código QR o de barras frente a la cámara para escanear.
                    </div>
                    
                    <div class="text-center mb-3">
                        <button id="start-scan" class="btn btn-primary">
                            <i class="fas fa-camera"></i> Iniciar Escáner
                        </button>
                        <button id="stop-scan" class="btn btn-danger" style="display: none;">
                            <i class="fas fa-stop"></i> Detener Escáner
                        </button>
                    </div>
                    
                    <div id="scanner-preview" class="mb-3" style="display: none;">
                        <video id="scanner-video" width="100%" height="300"></video>
                    </div>
                    
                    <div class="mb-3">
                        <label for="manual-code" class="form-label">O ingrese el código manualmente:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="manual-code" placeholder="Ingrese el código...">
                            <button class="btn btn-outline-primary" type="button" id="search-manual-code">Buscar</button>
                        </div>
                    </div>
                    
                    <div id="scan-result" class="border rounded p-3 mb-3" style="display: none;">
                        <!-- Resultados del escaneo -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="method-content" id="create-content" style="display: none;">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus-circle me-1"></i> Registrar Nuevo Activo
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> 
                    <strong>Importante:</strong> Verifique primero que el activo realmente no exista en el sistema usando las opciones de búsqueda o escaneo.
                </div>
                
                <form id="create-asset-form" action="{{ route('maintenance-flow.store-asset') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre del Activo *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Tipo de Activo *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Desktop">Computadora de Escritorio</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Printer">Impresora</option>
                                <option value="Server">Servidor</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Network">Equipo de Red</option>
                                <option value="Other">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="brand" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="brand" name="brand">
                        </div>
                        <div class="col-md-6">
                            <label for="model" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="model" name="model">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="serial" class="form-label">Número de Serie *</label>
                            <input type="text" class="form-control" id="serial" name="serial" required>
                        </div>
                        <div class="col-md-6">
                            <label for="patrimony_code" class="form-label">Código Patrimonial</label>
                            <input type="text" class="form-control" id="patrimony_code" name="patrimony_code">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="department_id" class="form-label">Departamento *</label>
                            <select class="form-select" id="department_id" name="department_id" required>
                                <option value="">Seleccionar departamento</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar y Continuar al Mantenimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Activos recientes -->
    <div class="card mt-4" id="recent-assets-container">
        <div class="card-header">
            <i class="fas fa-history me-1"></i> Activos Recientes
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($recentAssets as $asset)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card asset-card" onclick="selectAsset({{ $asset->id }})">
                            <div class="card-body">
                                <span class="asset-status">
                                    @if($asset->status == 'active')
                                        <span class="badge bg-success">Activo</span>
                                    @elseif($asset->status == 'in_maintenance')
                                        <span class="badge bg-warning">En Mantenimiento</span>
                                    @elseif($asset->status == 'inactive')
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @else
                                        <span class="badge bg-danger">De Baja</span>
                                    @endif
                                </span>
                                <div class="text-center mb-3">
                                    @if($asset->type == 'Desktop')
                                        <i class="fas fa-desktop fa-3x text-primary"></i>
                                    @elseif($asset->type == 'Laptop')
                                        <i class="fas fa-laptop fa-3x text-info"></i>
                                    @elseif($asset->type == 'Printer')
                                        <i class="fas fa-print fa-3x text-success"></i>
                                    @elseif($asset->type == 'Server')
                                        <i class="fas fa-server fa-3x text-danger"></i>
                                    @elseif($asset->type == 'Monitor')
                                        <i class="fas fa-desktop fa-3x text-warning"></i>
                                    @elseif($asset->type == 'Network')
                                        <i class="fas fa-network-wired fa-3x text-secondary"></i>
                                    @else
                                        <i class="fas fa-hdd fa-3x text-muted"></i>
                                    @endif
                                </div>
                                <h5 class="card-title text-center mb-2">{{ $asset->name }}</h5>
                                <div class="small text-muted mb-1">
                                    <strong>S/N:</strong> {{ $asset->serial }}
                                </div>
                                <div class="small text-muted mb-1">
                                    <strong>Dep:</strong> {{ $asset->department->name }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center p-4 text-muted">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <p>No hay activos recientes disponibles</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables globales
        let codeReader = null;
        let selectedVideoDevice = null;
        
        // ====== Manejo de métodos de selección ======
        const methods = document.querySelectorAll('.method-card');
        const methodContents = document.querySelectorAll('.method-content');
        
        methods.forEach(method => {
            method.addEventListener('click', function() {
                const methodName = this.dataset.method;
                
                // Ocultar todos los contenidos
                methodContents.forEach(content => {
                    content.style.display = 'none';
                });
                
                // Remover clase activa
                methods.forEach(m => m.classList.remove('bg-primary', 'text-white'));
                methods.forEach(m => m.classList.add('bg-light'));
                
                // Mostrar el contenido seleccionado y activar el método
                document.getElementById(`${methodName}-content`).style.display = 'block';
                this.classList.remove('bg-light');
                this.classList.add('bg-primary', 'text-white');
                
                // Si se selecciona escaneo, detener escáner
                if (methodName !== 'scan' && codeReader && codeReader.isRunning) {
                    codeReader.reset();
                    document.getElementById('scanner-preview').style.display = 'none';
                    document.getElementById('start-scan').style.display = 'inline-block';
                    document.getElementById('stop-scan').style.display = 'none';
                }
                
                // Si se muestra el formulario de activos recientes
                document.getElementById('recent-assets-container').style.display = 
                    (methodName === 'search') ? 'block' : 'none';
            });
        });
        
        // Inicialmente seleccionar búsqueda
        document.getElementById('search-method').click();
        
        // ====== Búsqueda de activos ======
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search');
        const typeFilter = document.getElementById('type-filter');
        const departmentFilter = document.getElementById('department-filter');
        const statusFilter = document.getElementById('status-filter');
        const searchResults = document.getElementById('search-results');
        const resultsCount = document.getElementById('results-count');
        const clearFiltersBtn = document.getElementById('clear-filters');
        
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
        
        // También buscar cuando cambie algún filtro
        typeFilter.addEventListener('change', performSearch);
        departmentFilter.addEventListener('change', performSearch);
        statusFilter.addEventListener('change', performSearch);
        
        // Limpiar filtros
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            typeFilter.value = '';
            departmentFilter.value = '';
            statusFilter.value = '';
            
            // Limpiar resultados
            searchResults.innerHTML = `
                <div class="text-center p-4 text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p>Utilice los filtros para buscar activos</p>
                </div>
            `;
            resultsCount.textContent = '0';
        });
        
        function performSearch() {
            // Mostrar indicador de carga
            document.getElementById('loading-indicator').style.display = 'flex';
            
            // Construir parámetros de búsqueda
            const params = new URLSearchParams();
            if (searchInput.value) params.append('q', searchInput.value);
            if (typeFilter.value) params.append('type', typeFilter.value);
            if (departmentFilter.value) params.append('department_id', departmentFilter.value);
            if (statusFilter.value) params.append('status', statusFilter.value);
            
            // Hacer petición AJAX
            fetch(`{{ route('maintenance-flow.search-assets') }}?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    // Actualizar contador
                    resultsCount.textContent = data.length;
                    
                    // Actualizar resultados
                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <p>No se encontraron activos con los criterios especificados</p>
                                <button class="btn btn-sm btn-outline-primary" id="create-asset-from-search">
                                    <i class="fas fa-plus-circle"></i> Crear Nuevo Activo
                                </button>
                            </div>
                        `;
                        
                        // Eventualmente conectar el botón para crear activo
                        document.getElementById('create-asset-from-search').addEventListener('click', function() {
                            document.getElementById('create-method').click();
                        });
                    } else {
                        // Crear elementos para cada resultado
                        searchResults.innerHTML = '';
                        data.forEach(asset => {
                            const item = document.createElement('a');
                            item.href = `{{ route('maintenance-flow.maintenance-form', '') }}/${asset.id}`;
                            item.className = 'list-group-item list-group-item-action result-item';
                            
                            // Determinar el icono según el tipo
                            let icon = '<i class="fas fa-hdd"></i>';
                            if (asset.type === 'Desktop') icon = '<i class="fas fa-desktop"></i>';
                            if (asset.type === 'Laptop') icon = '<i class="fas fa-laptop"></i>';
                            if (asset.type === 'Printer') icon = '<i class="fas fa-print"></i>';
                            if (asset.type === 'Server') icon = '<i class="fas fa-server"></i>';
                            if (asset.type === 'Monitor') icon = '<i class="fas fa-desktop"></i>';
                            if (asset.type === 'Network') icon = '<i class="fas fa-network-wired"></i>';
                            
                            // Determinar la etiqueta de estado
                            let statusBadge = '<span class="badge bg-secondary">Inactivo</span>';
                            if (asset.status === 'active') statusBadge = '<span class="badge bg-success">Activo</span>';
                            if (asset.status === 'in_maintenance') statusBadge = '<span class="badge bg-warning">En Mantenimiento</span>';
                            if (asset.status === 'decommissioned') statusBadge = '<span class="badge bg-danger">De Baja</span>';
                            
                            item.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="type-icon text-primary me-3">
                                        ${icon}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-1">${asset.name}</h5>
                                            ${statusBadge}
                                        </div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <small class="text-muted">
                                                <strong>Tipo:</strong> ${asset.type}
                                            </small>
                                            <small class="text-muted">
                                                <strong>S/N:</strong> ${asset.serial || 'N/A'}
                                            </small>
                                            <small class="text-muted">
                                                <strong>Departamento:</strong> ${asset.department.name}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            searchResults.appendChild(item);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = `
                        <div class="text-center p-4 text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <p>Error al buscar activos. Intente nuevamente.</p>
                        </div>
                    `;
                })
                .finally(() => {
                    // Ocultar indicador de carga
                    document.getElementById('loading-indicator').style.display = 'none';
                });
        }
        
        // ====== Escaneo de códigos ======
        const startScanBtn = document.getElementById('start-scan');
        const stopScanBtn = document.getElementById('stop-scan');
        const scannerPreview = document.getElementById('scanner-preview');
        const scanResult = document.getElementById('scan-result');
        const manualCodeInput = document.getElementById('manual-code');
        const searchManualCodeBtn = document.getElementById('search-manual-code');
        
        startScanBtn.addEventListener('click', function() {
            startScanner();
        });
        
        stopScanBtn.addEventListener('click', function() {
            if (codeReader && codeReader.isRunning) {
                codeReader.reset();
                scannerPreview.style.display = 'none';
                startScanBtn.style.display = 'inline-block';
                stopScanBtn.style.display = 'none';
            }
        });
        
        searchManualCodeBtn.addEventListener('click', function() {
            const code = manualCodeInput.value.trim();
            if (code) {
                searchByCode(code);
            } else {
                alert('Por favor, ingrese un código válido');
            }
        });
        
        function startScanner() {
            codeReader = new ZXing.BrowserMultiFormatReader();
            
            // Mostrar indicador de carga
            document.getElementById('loading-indicator').style.display = 'flex';
            
            codeReader.listVideoInputDevices()
                .then(videoInputDevices => {
                    if (videoInputDevices.length > 0) {
                        // Usar la primera cámara disponible
                        selectedVideoDevice = videoInputDevices[0].deviceId;
                        
                        // Si hay cámaras traseras, usarlas preferentemente
                        videoInputDevices.forEach(device => {
                            if (device.label.toLowerCase().includes('back') || 
                                device.label.toLowerCase().includes('trasera')) {
                                selectedVideoDevice = device.deviceId;
                            }
                        });
                        
                        // Mostrar el preview y comenzar escaneo
                        scannerPreview.style.display = 'block';
                        startScanBtn.style.display = 'none';
                        stopScanBtn.style.display = 'inline-block';
                        
                        codeReader.decodeFromVideoDevice(
                            selectedVideoDevice, 
                            'scanner-video', 
                            (result, err) => {
                                if (result) {
                                    // Código escaneado exitosamente
                                    console.log('Código detectado:', result.getText());
                                    // Detener escáner
                                    codeReader.reset();
                                    scannerPreview.style.display = 'none';
                                    startScanBtn.style.display = 'inline-block';
                                    stopScanBtn.style.display = 'none';
                                    
                                    // Buscar el código escaneado
                                    searchByCode(result.getText());
                                }
                                
                                if (err && !(err instanceof ZXing.NotFoundException)) {
                                    console.error('Error al escanear:', err);
                                }
                            }
                        );
                    } else {
                        alert('No se detectaron cámaras en este dispositivo');
                    }
                    
                    // Ocultar indicador de carga
                    document.getElementById('loading-indicator').style.display = 'none';
                })
                .catch(err => {
                    console.error('Error al listar dispositivos de video:', err);
                    alert('Error al iniciar el escáner: ' + err.message);
                    document.getElementById('loading-indicator').style.display = 'none';
                });
        }
        
        function searchByCode(code) {
            // Mostrar indicador de carga
            document.getElementById('loading-indicator').style.display = 'flex';
            scanResult.style.display = 'none';
            
            // Hacer petición AJAX
            fetch('{{ route('maintenance-flow.find-by-code') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Activo no encontrado');
                    }
                    return response.json();
                })
                .then(asset => {
                    // Determinar el icono según el tipo
                    let icon = '<i class="fas fa-hdd fa-3x"></i>';
                    if (asset.type === 'Desktop') icon = '<i class="fas fa-desktop fa-3x"></i>';
                    if (asset.type === 'Laptop') icon = '<i class="fas fa-laptop fa-3x"></i>';
                    if (asset.type === 'Printer') icon = '<i class="fas fa-print fa-3x"></i>';
                    if (asset.type === 'Server') icon = '<i class="fas fa-server fa-3x"></i>';
                    if (asset.type === 'Monitor') icon = '<i class="fas fa-desktop fa-3x"></i>';
                    if (asset.type === 'Network') icon = '<i class="fas fa-network-wired fa-3x"></i>';
                    
                    // Determinar la etiqueta de estado
                    let statusBadge = '<span class="badge bg-secondary">Inactivo</span>';
                    if (asset.status === 'active') statusBadge = '<span class="badge bg-success">Activo</span>';
                    if (asset.status === 'in_maintenance') statusBadge = '<span class="badge bg-warning">En Mantenimiento</span>';
                    if (asset.status === 'decommissioned') statusBadge = '<span class="badge bg-danger">De Baja</span>';
                    
                    // Mostrar el resultado
                    scanResult.innerHTML = `
                        <div class="text-center mb-3">
                            <div class="text-primary pulse mb-2">
                                ${icon}
                            </div>
                            <h4>${asset.name}</h4>
                            <div>${statusBadge}</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo:</strong> ${asset.type}</p>
                                <p><strong>Serial:</strong> ${asset.serial || 'N/A'}</p>
                                <p><strong>Marca/Modelo:</strong> ${asset.brand || 'N/A'} ${asset.model || ''}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Código:</strong> ${asset.patrimony_code || 'N/A'}</p>
                                <p><strong>Departamento:</strong> ${asset.department.name}</p>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('maintenance-flow.maintenance-form', '') }}/${asset.id}" class="btn btn-primary">
                                <i class="fas fa-tools"></i> Iniciar Mantenimiento
                            </a>
                        </div>
                    `;
                    scanResult.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    scanResult.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> No se encontró ningún activo con el código "${code}"
                        </div>
                        <div class="text-center">
                            <button id="create-from-code" class="btn btn-outline-primary">
                                <i class="fas fa-plus-circle"></i> Registrar Nuevo Activo
                            </button>
                        </div>
                    `;
                    scanResult.style.display = 'block';
                    
                    // Eventualmente conectar el botón para crear activo
                    document.getElementById('create-from-code').addEventListener('click', function() {
                        document.getElementById('create-method').click();
                        // Si es un código con formato reconocible, prellenamos el campo de serial
                        if (code.length > 5) {
                            document.getElementById('serial').value = code;
                        }
                    });
                })
                .finally(() => {
                    // Ocultar indicador de carga
                    document.getElementById('loading-indicator').style.display = 'none';
                });
        }
        
        // ====== Formulario de registro de activo ======
        const createAssetForm = document.getElementById('create-asset-form');
        
        createAssetForm.addEventListener('submit', function(e) {
            // Si el formulario tiene Ajax habilitado, manejamos aquí
            if (this.dataset.ajax === 'true') {
                e.preventDefault();
                
                // Mostrar indicador de carga
                document.getElementById('loading-indicator').style.display = 'flex';
                
                // Enviar formulario por AJAX
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirigir al formulario de mantenimiento
                            window.location.href = `{{ route('maintenance-flow.maintenance-form', '') }}/${data.asset.id}`;
                        } else {
                            throw new Error(data.message || 'Error al guardar el activo');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al guardar el activo: ' + error.message);
                    })
                    .finally(() => {
                        // Ocultar indicador de carga
                        document.getElementById('loading-indicator').style.display = 'none';
                    });
            }
            // De lo contrario, se enviará normalmente
        });
    });
    
    // Función para seleccionar activo y redirigir al formulario de mantenimiento
    function selectAsset(assetId) {
        window.location.href = `{{ route('maintenance-flow.maintenance-form', '') }}/${assetId}`;
    }
</script>
@endsection