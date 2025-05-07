@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Registrar Nuevo Activo</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('maintenance-flow.select-asset') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Selección
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i> Formulario de Registro de Activo
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> 
                <strong>Importante:</strong> Complete todos los datos del activo. Este registro se integrará con el inventario general.
            </div>
            
            <form action="{{ route('maintenance-flow.store-asset') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nombre del Activo *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Tipo de Activo *</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Desktop" {{ old('type') == 'Desktop' ? 'selected' : '' }}>Computadora de Escritorio</option>
                            <option value="Laptop" {{ old('type') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                            <option value="Printer" {{ old('type') == 'Printer' ? 'selected' : '' }}>Impresora</option>
                            <option value="Server" {{ old('type') == 'Server' ? 'selected' : '' }}>Servidor</option>
                            <option value="Monitor" {{ old('type') == 'Monitor' ? 'selected' : '' }}>Monitor</option>
                            <option value="Network" {{ old('type') == 'Network' ? 'selected' : '' }}>Equipo de Red</option>
                            <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="brand" class="form-label">Marca</label>
                        <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand') }}">
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="model" class="form-label">Modelo</label>
                        <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model') }}">
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="serial" class="form-label">Número de Serie</label>
                        <input type="text" class="form-control @error('serial') is-invalid @enderror" id="serial" name="serial" value="{{ old('serial') }}" required>
                        @error('serial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="patrimony_code" class="form-label">Código Patrimonial</label>
                        <input type="text" class="form-control @error('patrimony_code') is-invalid @enderror" id="patrimony_code" name="patrimony_code" value="{{ old('patrimony_code') }}">
                        @error('patrimony_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Departamento *</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                            <option value="">Seleccionar departamento</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
@endsection