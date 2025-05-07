@extends('layouts.app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .form-wizard {
        position: relative;
        margin-bottom: 3rem;
    }
    
    .wizard-progress {
        position: relative;
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    .wizard-progress::before {
        content: "";
        position: absolute;
        top: 25px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #e9ecef;
        z-index: 1;
    }
    
    .progress-step {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 20%;
    }
    
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .step-name {
        font-size: 0.85rem;
        color: #6c757d;
        text-align: center;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .progress-step.active .step-number {
        background-color: #007bff;
        color: white;
    }
    
    .progress-step.active .step-name {
        color: #007bff;
        font-weight: bold;
    }
    
    .progress-step.completed .step-number {
        background-color: #28a745;
        color: white;
    }
    
    .wizard-content .tab-pane {
        display: none;
    }
    
    .wizard-content .tab-pane.active {
        display: block;
    }
    
    .wizard-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .maintenance-type-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .maintenance-type-card:hover {
        transform: translateY(-5px);
    }
    
    .maintenance-type-card.selected {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .priority-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }
    
    .img-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .img-preview:hover {
        transform: scale(1.05);
    }
    
    .audio-recorder {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .maintenance-template {
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    
    .maintenance-template:hover {
        background-color: #f8f9fa;
        border-left-color: #007bff;
    }
    
    .checklist-item {
        margin-bottom: 0.5rem;
    }
    
    .checklist-item label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .checklist-item input[type="checkbox"] {
        margin-right: 0.5rem;
    }
    
    .note-editor {
        border-radius: 0.375rem;
    }
    
    .note-editor .note-toolbar {
        background-color: #f8f9fa;
    }
    
    .note-editor .note-editable {
        min-height: 200px;
    }
    
    .timer-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 0.375rem;
    }
    
    .timer-display {
        font-size: 1.5rem;
        font-weight: bold;
        font-family: monospace;
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
    
    .asset-info-box {
        border-left: 4px solid #007bff;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .maintenance-status-box {
        border-left: 4px solid #ffc107;
        border-radius: 0.375rem;
        background-color: #fff8e1;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Indicador de carga -->
    <div id="loading-indicator">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h4 class="text-primary">Procesando su solicitud...</h4>
        </div>
    </div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h2">Editar Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Detalles
                </a>
                <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-list"></i> Listado de Mantenimientos
                </a>
            </div>
        </div>
    </div>
    
    <!-- Información del activo -->
    <div class="asset-info-box">
        <div class="row">
            <div class="col-md-1 text-center">
                @if($maintenance->asset->type == 'Desktop')
                    <i class="fas fa-desktop fa-3x text-primary"></i>
                @elseif($maintenance->asset->type == 'Laptop')
                    <i class="fas fa-laptop fa-3x text-info"></i>
                @elseif($maintenance->asset->type == 'Printer')
                    <i class="fas fa-print fa-3x text-success"></i>
                @elseif($maintenance->asset->type == 'Server')
                    <i class="fas fa-server fa-3x text-danger"></i>
                @elseif($maintenance->asset->type == 'Monitor')
                    <i class="fas fa-desktop fa-3x text-warning"></i>
                @elseif($maintenance->asset->type == 'Network')
                    <i class="fas fa-network-wired fa-3x text-secondary"></i>
                @else
                    <i class="fas fa-hdd fa-3x text-muted"></i>
                @endif
            </div>
            <div class="col-md-5">
                <h4>{{ $maintenance->asset->name }}</h4>
                <div class="d-flex gap-3 flex-wrap">
                    <div><strong>Tipo:</strong> {{ $maintenance->asset->type }}</div>
                    <div><strong>S/N:</strong> {{ $maintenance->asset->serial }}</div>
                    <div><strong>Marca/Modelo:</strong> {{ $maintenance->asset->brand }} {{ $maintenance->asset->model }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div><strong>Departamento:</strong> {{ $maintenance->asset->department->name }}</div>
                <div><strong>Código Patrimonial:</strong> {{ $maintenance->asset->patrimony_code ?? 'No asignado' }}</div>
                <div><strong>Estado:</strong> 
                    @if($maintenance->asset->status == 'active')
                        <span class="badge bg-success">Activo</span>
                    @elseif($maintenance->asset->status == 'in_maintenance')
                        <span class="badge bg-warning">En Mantenimiento</span>
                    @elseif($maintenance->asset->status == 'inactive')
                        <span class="badge bg-secondary">Inactivo</span>
                    @else
                        <span class="badge bg-danger">De Baja</span>
                    @endif
                </div>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('assets.show', $maintenance->asset->id) }}" class="btn btn-sm btn-outline-info" target="_blank">
                    <i class="fas fa-info-circle"></i> Ver Detalles
                </a>
            </div>
        </div>
    </div>
    
    <!-- Estado del mantenimiento -->
    <div class="maintenance-status-box">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Información del Mantenimiento</h5>
                <div>
                    <strong>ID:</strong> {{ $maintenance->id }}
                </div>
                <div>
                    <strong>Tipo:</strong> 
                    @if($maintenance->maintenance_type == 'preventive')
                        <span class="badge bg-success">Preventivo</span>
                    @elseif($maintenance->maintenance_type == 'corrective')
                        <span class="badge bg-danger">Correctivo</span>
                    @else
                        <span class="badge bg-info">Predictivo</span>
                    @endif
                </div>
                <div>
                    <strong>Iniciado:</strong> {{ $maintenance->start_date->format('d/m/Y H:i') }}
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Estado Actual</h5>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-tools me-2"></i>
                    <strong>Mantenimiento en Progreso</strong> - El activo está actualmente en estado de mantenimiento. Complete el proceso para restaurar su disponibilidad.
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario por pasos -->
    <div class="form-wizard">
        <!-- Barra de progreso -->
        <div class="wizard-progress">
            <div class="progress-step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-name">Diagnóstico</div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-name">Procedimiento</div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-name">Materiales</div>
            </div>
            <div class="progress-step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-name">Finalización</div>
            </div>
        </div>
        
        <!-- Contenido del formulario -->
        <form id="maintenance-form" action="{{ route('maintenance-flow.update-maintenance', $maintenance->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="wizard-content">
                <!-- Paso 1: Diagnóstico -->
                <div class="tab-pane active" id="step1">
                    <h4 class="mb-4">Actualizar Diagnóstico</h4>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="priority" class="form-label">Prioridad</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Seleccionar prioridad</option>
                                <option value="low" @if(isset($additionalInfo['priority']) && $additionalInfo['priority'] == 'low') selected @endif>Baja</option>
                                <option value="medium" @if(isset($additionalInfo['priority']) && $additionalInfo['priority'] == 'medium') selected @endif>Media</option>
                                <option value="high" @if(isset($additionalInfo['priority']) && $additionalInfo['priority'] == 'high') selected @endif>Alta</option>
                                <option value="critical" @if(isset($additionalInfo['priority']) && $additionalInfo['priority'] == 'critical') selected @endif>Crítica</option>
                            </select>
                            <div class="form-text" id="priority-description"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="estimated_time" class="form-label">Tiempo Estimado (minutos)</label>
                            <input type="number" class="form-control" id="estimated_time" name="estimated_time" min="5" placeholder="Ingrese tiempo estimado" value="{{ $additionalInfo['estimated_time'] ?? '' }}">
                            <div class="form-text">Aproximado del tiempo que tomará realizar este mantenimiento</div>
                        </div>
                    </div>
                    
                    <!-- Herramientas rápidas -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-lightbulb me-1"></i> Herramientas de Diagnóstico
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="audio-recorder mb-3">
                                        <h6><i class="fas fa-microphone me-1"></i> Dictado por Voz</h6>
                                        <div class="d-flex gap-2 mb-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="start-recording">
                                                <i class="fas fa-microphone"></i> Iniciar Dictado
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="stop-recording" disabled>
                                                <i class="fas fa-stop"></i> Detener
                                            </button>
                                        </div>
                                        <div class="recording-status small text-muted">
                                            Estado: No activo
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6><i class="fas fa-camera me-1"></i> Adjuntar Imágenes</h6>
                                        <input class="form-control" type="file" id="diagnosis_images" name="images[]" multiple accept="image/*">
                                        <div class="form-text">Máximo 5 imágenes, 5MB cada una</div>
                                        <div class="image-preview-container d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="fas fa-clipboard-list me-1"></i> Problemas Comunes</h6>
                                    <div class="list-group common-issues">
                                        @if($maintenance->asset->type == 'Desktop' || $maintenance->asset->type == 'Laptop')
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Sistema operativo lento o inestable">Sistema operativo lento o inestable</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Problemas de inicio o arranque">Problemas de inicio o arranque</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Sobrecalentamiento">Sobrecalentamiento</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Ruidos extraños en discos duros o ventiladores">Ruidos extraños en discos duros o ventiladores</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Fallas de memoria RAM">Fallas de memoria RAM</button>
                                        @elseif($maintenance->asset->type == 'Printer')
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Atasco de papel">Atasco de papel</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Calidad de impresión deficiente">Calidad de impresión deficiente</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Error de conexión">Error de conexión</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Sistema de alimentación fallando">Sistema de alimentación fallando</button>
                                        @elseif($maintenance->asset->type == 'Server')
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Problemas de conectividad o red">Problemas de conectividad o red</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Rendimiento deficiente o lentitud">Rendimiento deficiente o lentitud</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Errores en discos del arreglo RAID">Errores en discos del arreglo RAID</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Problemas de capacidad o almacenamiento">Problemas de capacidad o almacenamiento</button>
                                        @else
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="No enciende o problemas de alimentación">No enciende o problemas de alimentación</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Fallas intermitentes">Fallas intermitentes</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Errores de conexión o comunicación">Errores de conexión o comunicación</button>
                                            <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Daño físico visible">Daño físico visible</button>
                                        @endif
                                        <button type="button" class="list-group-item list-group-item-action maintenance-template" data-template="Mantenimiento preventivo rutinario">Mantenimiento preventivo rutinario</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo de diagnóstico -->
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnóstico Detallado <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote" id="diagnosis" name="diagnosis" required>{{ $maintenance->diagnosis }}</textarea>
                        <div class="form-text">Describa los problemas identificados o el estado actual del equipo</div>
                    </div>
                </div>
                
                <!-- Paso 2: Procedimiento -->
                <div class="tab-pane" id="step2">
                    <h4 class="mb-4">Actualizar Procedimiento</h4>
                    
                    <!-- Herramientas de procedimiento -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-tools me-1"></i> Tareas Comunes
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-clipboard-check me-1"></i> Lista de Verificación</h6>
                                    <div class="checklist-container">
                                        @if($maintenance->asset->type == 'Desktop' || $maintenance->asset->type == 'Laptop')
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Limpieza interna de polvo y residuos"> 
                                                    Limpieza interna de polvo y residuos
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Verificación de temperatura de componentes"> 
                                                    Verificación de temperatura de componentes
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Limpieza de ventiladores y disipadores"> 
                                                    Limpieza de ventiladores y disipadores
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Verificación de estado de discos duros"> 
                                                    Verificación de estado de discos duros
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Actualización de controladores y sistema operativo"> 
                                                    Actualización de controladores y sistema operativo
                                                </label>
                                            </div>
                                        @elseif($maintenance->asset->type == 'Printer')
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Limpieza de rodillos y sistema de alimentación"> 
                                                    Limpieza de rodillos y sistema de alimentación
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Limpieza de cabezales de impresión"> 
                                                    Limpieza de cabezales de impresión
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Verificación de niveles de tinta/tóner"> 
                                                    Verificación de niveles de tinta/tóner
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Actualización de firmware"> 
                                                    Actualización de firmware
                                                </label>
                                            </div>
                                        @else
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Limpieza general de polvo y suciedad"> 
                                                    Limpieza general de polvo y suciedad
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Verificación de conexiones y cables"> 
                                                    Verificación de conexiones y cables
                                                </label>
                                            </div>
                                            <div class="checklist-item">
                                                <label>
                                                    <input type="checkbox" class="procedure-checkbox" data-task="Actualización de software/firmware"> 
                                                    Actualización de software/firmware
                                                </label>
                                            </div>
                                        @endif
                                        <div class="checklist-item">
                                            <label>
                                                <input type="checkbox" class="procedure-checkbox" data-task="Verificación de estado general"> 
                                                Verificación de estado general
                                            </label>
                                        </div>
                                        <div class="checklist-item">
                                            <label>
                                                <input type="checkbox" class="procedure-checkbox" data-task="Pruebas de funcionamiento"> 
                                                Pruebas de funcionamiento
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="fas fa-camera me-1"></i> Adjuntar Imágenes del Procedimiento</h6>
                                    <input class="form-control" type="file" id="procedure_images" name="procedure_images[]" multiple accept="image/*">
                                    <div class="form-text">Imágenes del proceso de mantenimiento</div>
                                    <div class="procedure-image-preview d-flex flex-wrap gap-2 mt-2"></div>
                                    
                                    <h6 class="mt-3"><i class="fas fa-book me-1"></i> Manuales Técnicos</h6>
                                    <div class="list-group">
                                        @if($maintenance->asset->type == 'Desktop' || $maintenance->asset->type == 'Laptop')
                                            <a href="#" class="list-group-item list-group-item-action" target="_blank">
                                                <i class="fas fa-file-pdf me-1"></i> Manual de mantenimiento de PCs
                                            </a>
                                        @elseif($maintenance->asset->type == 'Printer')
                                            <a href="#" class="list-group-item list-group-item-action" target="_blank">
                                                <i class="fas fa-file-pdf me-1"></i> Manual de mantenimiento de impresoras
                                            </a>
                                        @elseif($maintenance->asset->type == 'Server')
                                            <a href="#" class="list-group-item list-group-item-action" target="_blank">
                                                <i class="fas fa-file-pdf me-1"></i> Manual de mantenimiento de servidores
                                            </a>
                                        @endif
                                        <a href="#" class="list-group-item list-group-item-action" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> Guía general de mantenimiento
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo de procedimiento -->
                    <div class="mb-3">
                        <label for="procedure" class="form-label">Procedimiento Realizado <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote" id="procedure" name="procedure" required>{{ $maintenance->procedure }}</textarea>
                        <div class="form-text">Describa paso a paso el procedimiento realizado para el mantenimiento</div>
                    </div>
                </div>
                
                <!-- Paso 3: Materiales -->
                <div class="tab-pane" id="step3">
                    <h4 class="mb-4">Materiales y Repuestos Utilizados</h4>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="materials_used" class="form-label">Materiales Utilizados</label>
                                <textarea class="form-control" id="materials_used" name="materials_used" rows="4">{{ $additionalInfo['materials_used'] ?? '' }}</textarea>
                                <div class="form-text">Liste todos los materiales y repuestos utilizados en el mantenimiento</div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Los materiales registrados aquí ayudarán a llevar un control del inventario de repuestos y consumibles de la municipalidad.
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-boxes me-1"></i> Inventario Disponible
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Categoría</th>
                                                    <th>Stock</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Pasta térmica</td>
                                                    <td>Consumible</td>
                                                    <td><span class="badge bg-success">Disponible</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-material" data-material="Pasta térmica">
                                                            <i class="fas fa-plus-circle"></i> Agregar
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Kit de limpieza</td>
                                                    <td>Consumible</td>
                                                    <td><span class="badge bg-success">Disponible</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-material" data-material="Kit de limpieza">
                                                            <i class="fas fa-plus-circle"></i> Agregar
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Memoria RAM DDR4 8GB</td>
                                                    <td>Repuesto</td>
                                                    <td><span class="badge bg-warning">Stock bajo</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-material" data-material="Memoria RAM DDR4 8GB">
                                                            <i class="fas fa-plus-circle"></i> Agregar
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Disco duro 1TB</td>
                                                    <td>Repuesto</td>
                                                    <td><span class="badge bg-danger">Sin stock</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                            <i class="fas fa-times-circle"></i> No disponible
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-shopping-cart me-1"></i> Solicitar Repuestos
                                </div>
                                <div class="card-body">
                                    <p>Si requiere repuestos que no están disponibles en el inventario, puede registrar aquí la solicitud:</p>
                                    
                                    <div class="mb-3">
                                        <label for="request_item" class="form-label">Repuesto Necesario</label>
                                        <input type="text" class="form-control" id="request_item">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="request_quantity" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="request_quantity" min="1" value="1">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="request_urgency" class="form-label">Urgencia</label>
                                        <select class="form-select" id="request_urgency">
                                            <option value="low">Baja</option>
                                            <option value="medium">Media</option>
                                            <option value="high">Alta</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-outline-primary" id="add-request">
                                            <i class="fas fa-plus-circle"></i> Agregar Solicitud
                                        </button>
                                    </div>
                                    
                                    <div id="request-list" class="mt-3">
                                        <!-- Aquí se mostrarán las solicitudes -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paso 4: Finalización -->
                <div class="tab-pane" id="step4">
                    <h4 class="mb-4">Finalización y Recomendaciones</h4>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="recommendations" class="form-label">Recomendaciones Futuras</label>
                                <textarea class="form-control" id="recommendations" name="recommendations" rows="4">{{ $additionalInfo['recommendations'] ?? '' }}</textarea>
                                <div class="form-text">Ingrese recomendaciones para mantener el activo en buen estado o futuras acciones a realizar</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Programar Próximo Mantenimiento</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="date" class="form-control" id="next_maintenance_date" 
                                            value="{{ $additionalInfo['next_maintenance_date'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" id="next_maintenance_type">
                                            <option value="preventive" @if(isset($additionalInfo['next_maintenance_type']) && $additionalInfo['next_maintenance_type'] == 'preventive') selected @endif>Preventivo</option>
                                            <option value="corrective" @if(isset($additionalInfo['next_maintenance_type']) && $additionalInfo['next_maintenance_type'] == 'corrective') selected @endif>Correctivo</option>
                                            <option value="predictive" @if(isset($additionalInfo['next_maintenance_type']) && $additionalInfo['next_maintenance_type'] == 'predictive') selected @endif>Predictivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-text">Esta información se utilizará para generar recordatorios</div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Completar Mantenimiento</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="complete_now" name="complete_now">
                                    <label class="form-check-label" for="complete_now">Marcar como completado ahora</label>
                                </div>
                                <div class="form-text">
                                    Si marca esta opción, el mantenimiento se registrará como completado y el activo volverá a estado "Activo".
                                    De lo contrario, quedará en estado "En Mantenimiento" y deberá completarlo después.
                                </div>
                            </div>
                            
                            <div class="mb-4" id="generate-certificate-option" style="display: none;">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="generate_certificate" name="generate_certificate" checked>
                                    <label class="form-check-label" for="generate_certificate">Generar acta de mantenimiento automáticamente</label>
                                </div>
                                <div class="form-text">
                                    Al completar el mantenimiento, se generará automáticamente un acta que requerirá firmas.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-check-circle me-1"></i> Resumen del Mantenimiento
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush" id="maintenance-summary">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Tipo de Mantenimiento:</span>
                                            <span>
                                                @if($maintenance->maintenance_type == 'preventive')
                                                    Preventivo
                                                @elseif($maintenance->maintenance_type == 'corrective')
                                                    Correctivo
                                                @else
                                                    Predictivo
                                                @endif
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Activo:</span>
                                            <span>{{ $maintenance->asset->name }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Prioridad:</span>
                                            <span id="summary-priority">
                                                @php
                                                    $priorityLabel = 'No especificada';
                                                    
                                                    if (!empty($additionalInfo['priority'])) {
                                                        switch($additionalInfo['priority']) {
                                                            case 'low':
                                                                $priorityLabel = 'Baja';
                                                                break;
                                                            case 'medium':
                                                                $priorityLabel = 'Media';
                                                                break;
                                                            case 'high':
                                                                $priorityLabel = 'Alta';
                                                                break;
                                                            case 'critical':
                                                                $priorityLabel = 'Crítica';
                                                                break;
                                                        }
                                                    }
                                                @endphp
                                                {{ $priorityLabel }}
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Tiempo Estimado:</span>
                                            <span id="summary-time">
                                                {{ !empty($additionalInfo['estimated_time']) ? $additionalInfo['estimated_time'] . ' minutos' : 'No especificado' }}
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Iniciado:</span>
                                            <span>{{ $maintenance->start_date->format('d/m/Y H:i') }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-light text-center">
                                    <p class="mb-0">
                                        <small class="text-muted">Siendo actualizado por: {{ Auth::user()->name }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botones de navegación -->
            <div class="wizard-buttons">
                <div>
                    <button type="button" class="btn btn-secondary" id="prev-btn" disabled>
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" id="next-btn">
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submit-btn" style="display: none;">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery (ensure this is loaded first) -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

<!-- Popper.js (required by Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Summernote JS (load after jQuery and Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    // Wait for the document to be fully loaded
    $(document).ready(function() {
        // Inicializar editor Summernote
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Escriba aquí...',
            callbacks: {
                onImageUpload: function(files) {
                    // Prevenir carga de imágenes directamente en el editor
                    alert('Por favor, utilice el botón de "Adjuntar Imágenes" para subir imágenes');
                }
            }
        });
        
        // Variables para navegación
        const progressSteps = document.querySelectorAll('.progress-step');
        const tabPanes = document.querySelectorAll('.tab-pane');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');
        let currentStep = 1;
        
        // Inicializar carga
        const loadingIndicator = document.getElementById('loading-indicator');
        
        // Prioridad del mantenimiento
        const prioritySelect = document.getElementById('priority');
        const priorityDescription = document.getElementById('priority-description');
        
        prioritySelect.addEventListener('change', function() {
            let description = '';
            let textClass = '';
            
            switch(this.value) {
                case 'low':
                    description = 'Puede ser atendido sin urgencia, no afecta el funcionamiento normal.';
                    textClass = 'text-success';
                    break;
                case 'medium':
                    description = 'Requiere atención dentro de un tiempo razonable.';
                    textClass = 'text-primary';
                    break;
                case 'high':
                    description = 'Requiere atención pronta, afecta parcialmente la operación.';
                    textClass = 'text-warning';
                    break;
                case 'critical':
                    description = 'Requiere atención inmediata, impide totalmente la operación.';
                    textClass = 'text-danger';
                    break;
            }
            
            priorityDescription.className = textClass;
            priorityDescription.textContent = description;
            
            // Actualizar resumen
            document.getElementById('summary-priority').textContent = this.options[this.selectedIndex].text;
        });
        
        // Tiempo estimado
        const estimatedTimeInput = document.getElementById('estimated_time');
        estimatedTimeInput.addEventListener('input', function() {
            if (this.value) {
                document.getElementById('summary-time').textContent = `${this.value} minutos`;
            } else {
                document.getElementById('summary-time').textContent = 'No especificado';
            }
        });
        
        // Paso 2: Diagnóstico
        // Grabación de audio
        let mediaRecorder;
        let audioChunks = [];
        const startRecording = document.getElementById('start-recording');
        const stopRecording = document.getElementById('stop-recording');
        const recordingStatus = document.querySelector('.recording-status');
        
        startRecording.addEventListener('click', function() {
            // Comprobar soporte para API de grabación
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        mediaRecorder = new MediaRecorder(stream);
                        mediaRecorder.start();
                        
                        // Actualizar estado
                        recordingStatus.innerHTML = '<span class="text-danger"><i class="fas fa-circle-pulse"></i> Grabando...</span>';
                        startRecording.disabled = true;
                        stopRecording.disabled = false;
                        
                        // Recolectar datos
                        audioChunks = [];
                        mediaRecorder.addEventListener('dataavailable', event => {
                            audioChunks.push(event.data);
                        });
                        
                        // Cuando se complete la grabación
                        mediaRecorder.addEventListener('stop', () => {
                            // Convertir grabación a texto (simulado)
                            setTimeout(() => {
                                recordingStatus.innerHTML = 'Estado: Procesando audio...';
                                
                                // Simular reconocimiento de voz
                                setTimeout(() => {
                                    const diagnosisEditor = $('#diagnosis').summernote('code');
                                    $('#diagnosis').summernote('code', diagnosisEditor + '<p>[Texto transcrito del audio] Actualizando el diagnóstico: Se ha identificado que el problema persiste después de las primeras intervenciones. Se requiere una revisión más profunda de los componentes internos.</p>');
                                    
                                    recordingStatus.innerHTML = 'Estado: Transcripción completa';
                                    startRecording.disabled = false;
                                }, 1500);
                            }, 1000);
                        });
                    })
                    .catch(error => {
                        console.error('Error al acceder al micrófono:', error);
                        recordingStatus.innerHTML = `Error: ${error.message}`;
                        alert('No se pudo acceder al micrófono. Verifique los permisos.');
                    });
            } else {
                alert('Su navegador no soporta grabación de audio.');
                recordingStatus.innerHTML = 'Error: Navegador no compatible';
            }
        });
        
        stopRecording.addEventListener('click', function() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                stopRecording.disabled = true;
            }
        });
        
        // Vista previa de imágenes
        const diagnosisImages = document.getElementById('diagnosis_images');
        const imagePreviewContainer = document.querySelector('.image-preview-container');
        
        diagnosisImages.addEventListener('change', function() {
            imagePreviewContainer.innerHTML = '';
            
            if (this.files.length > 5) {
                alert('Solo puede seleccionar hasta 5 imágenes');
                this.value = '';
                return;
            }
            
            for (const file of this.files) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('img');
                        preview.src = e.target.result;
                        preview.className = 'img-preview border';
                        preview.setAttribute('title', file.name);
                        imagePreviewContainer.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        
        // Plantillas de problemas comunes
        const issueTemplates = document.querySelectorAll('.maintenance-template');
        issueTemplates.forEach(template => {
            template.addEventListener('click', function() {
                const diagnosisText = this.dataset.template;
                const currentContent = $('#diagnosis').summernote('code');
                
                if (currentContent.trim() === '') {
                    $('#diagnosis').summernote('code', `<p>${diagnosisText}</p>`);
                } else {
                    $('#diagnosis').summernote('code', currentContent + `<p>${diagnosisText}</p>`);
                }
            });
        });
        
        // Paso 3: Procedimiento
        // Checklist de tareas
        const procedureCheckboxes = document.querySelectorAll('.procedure-checkbox');
        procedureCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    const taskText = this.dataset.task;
                    const currentContent = $('#procedure').summernote('code');
                    
                    if (currentContent.trim() === '') {
                        $('#procedure').summernote('code', `<ol><li>${taskText}</li></ol>`);
                    } else {
                        // Verificar si ya existe una lista
                        if (currentContent.includes('<ol>')) {
                            // Agregar a la lista existente
                            const updatedContent = currentContent.replace('</ol>', `<li>${taskText}</li></ol>`);
                            $('#procedure').summernote('code', updatedContent);
                        } else {
                            // Crear nueva lista
                            $('#procedure').summernote('code', currentContent + `<ol><li>${taskText}</li></ol>`);
                        }
                    }
                }
            });
        });
        
        // Paso 4: Materiales
        // Agregar material del inventario
        const addMaterialButtons = document.querySelectorAll('.add-material');
        addMaterialButtons.forEach(button => {
            button.addEventListener('click', function() {
                const materialName = this.dataset.material;
                const materialsTextarea = document.getElementById('materials_used');
                
                if (materialsTextarea.value === '') {
                    materialsTextarea.value = materialName;
                } else {
                    materialsTextarea.value += '\n' + materialName;
                }
            });
        });
        
        // Solicitud de repuestos
        const addRequestBtn = document.getElementById('add-request');
        const requestList = document.getElementById('request-list');
        
        addRequestBtn.addEventListener('click', function() {
            const item = document.getElementById('request_item').value.trim();
            const quantity = document.getElementById('request_quantity').value;
            const urgency = document.getElementById('request_urgency');
            const urgencyText = urgency.options[urgency.selectedIndex].text;
            
            if (item === '') {
                alert('Por favor ingrese el nombre del repuesto');
                return;
            }
            
            const requestItem = document.createElement('div');
            requestItem.className = 'alert alert-secondary d-flex justify-content-between align-items-center';
            requestItem.innerHTML = `
                <div>
                    <strong>${item}</strong> - Cant: ${quantity} 
                    <span class="badge bg-${urgency.value === 'high' ? 'danger' : (urgency.value === 'medium' ? 'warning' : 'info')}">
                        ${urgencyText}
                    </span>
                </div>
                <button type="button" class="btn-close" aria-label="Close"></button>
            `;
            
            // Botón para eliminar solicitud
            requestItem.querySelector('.btn-close').addEventListener('click', function() {
                requestItem.remove();
            });
            
            requestList.appendChild(requestItem);
            
            // Limpiar campos
            document.getElementById('request_item').value = '';
            document.getElementById('request_quantity').value = 1;
        });
        
        // Paso 5: Finalización
        // Opción de completar ahora
        const completeNowCheckbox = document.getElementById('complete_now');
        const generateCertificateOption = document.getElementById('generate-certificate-option');
        
        completeNowCheckbox.addEventListener('change', function() {
            generateCertificateOption.style.display = this.checked ? 'block' : 'none';
        });
        
        // ========= Navegación entre pasos =========
        function updateStep(step) {
            currentStep = step;
            
            // Actualizar pasos de progreso
            progressSteps.forEach(progressStep => {
                const stepNumber = parseInt(progressStep.dataset.step);
                
                progressStep.classList.remove('active', 'completed');
                
                if (stepNumber === currentStep) {
                    progressStep.classList.add('active');
                } else if (stepNumber < currentStep) {
                    progressStep.classList.add('completed');
                }
            });
            
            // Actualizar contenido visible
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
            });
            document.getElementById(`step${currentStep}`).classList.add('active');
            
            // Actualizar botones
            prevBtn.disabled = currentStep === 1;
            
            if (currentStep === 4) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-block';
            } else {
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            }
        }
        
        // Botón Anterior
        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                updateStep(currentStep - 1);
            }
        });
        
        // Botón Siguiente
        nextBtn.addEventListener('click', function() {
            // Validación según el paso actual
            let canContinue = true;
            
            if (currentStep === 1) {
                // Verificar diagnóstico
                if ($('#diagnosis').summernote('isEmpty')) {
                    alert('Por favor, ingrese el diagnóstico del problema');
                    canContinue = false;
                }
                
                // Verificar prioridad
                if (prioritySelect.value === '') {
                    alert('Por favor, seleccione la prioridad del mantenimiento');
                    canContinue = false;
                }
            }
            else if (currentStep === 2) {
                // Verificar procedimiento
                if ($('#procedure').summernote('isEmpty')) {
                    alert('Por favor, ingrese el procedimiento realizado');
                    canContinue = false;
                }
            }
            
            if (canContinue && currentStep < 4) {
                updateStep(currentStep + 1);
            }
        });
        
        // Envío del formulario
        const maintenanceForm = document.getElementById('maintenance-form');
        maintenanceForm.addEventListener('submit', function(e) {
            loadingIndicator.style.display = 'flex';
            
            // Verificar campos requeridos
            if ($('#diagnosis').summernote('isEmpty')) {
                e.preventDefault();
                alert('Por favor, ingrese el diagnóstico del problema');
                updateStep(1);
                loadingIndicator.style.display = 'none';
                return false;
            }
            
            if ($('#procedure').summernote('isEmpty')) {
                e.preventDefault();
                alert('Por favor, ingrese el procedimiento realizado');
                updateStep(2);
                loadingIndicator.style.display = 'none';
                return false;
            }
            
            if (prioritySelect.value === '') {
                e.preventDefault();
                alert('Por favor, seleccione la prioridad del mantenimiento');
                updateStep(1);
                loadingIndicator.style.display = 'none';
                return false;
            }
            
            return true;
        });
        
        // Configurar descripción de prioridad al cargar la página
        if (prioritySelect.value) {
            let description = '';
            let textClass = '';
            
            switch(prioritySelect.value) {
                case 'low':
                    description = 'Puede ser atendido sin urgencia, no afecta el funcionamiento normal.';
                    textClass = 'text-success';
                    break;
                case 'medium':
                    description = 'Requiere atención dentro de un tiempo razonable.';
                    textClass = 'text-primary';
                    break;
                case 'high':
                    description = 'Requiere atención pronta, afecta parcialmente la operación.';
                    textClass = 'text-warning';
                    break;
                case 'critical':
                    description = 'Requiere atención inmediata, impide totalmente la operación.';
                    textClass = 'text-danger';
                    break;
            }
            
            priorityDescription.className = textClass;
            priorityDescription.textContent = description;
        }
    });
</script>
@endsection
