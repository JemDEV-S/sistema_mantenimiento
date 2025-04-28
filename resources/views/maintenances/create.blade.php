@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nuevo Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-tools me-1"></i> Formulario de Registro
        </div>
        <div class="card-body">
            <form action="{{ route('maintenances.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="asset_id" class="form-label">Activo</label>
                        <select class="form-select @error('asset_id') is-invalid @enderror" id="asset_id" name="asset_id" required {{ $asset ? 'disabled' : '' }}>
                            <option value="">Seleccionar activo</option>
                            @foreach($assets as $assetItem)
                                <option value="{{ $assetItem->id }}" {{ (old('asset_id') == $assetItem->id || ($asset && $asset->id == $assetItem->id)) ? 'selected' : '' }}>
                                    {{ $assetItem->name }} ({{ $assetItem->type }}) - {{ $assetItem->department->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($asset)
                            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                        @endif
                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="maintenance_type" class="form-label">Tipo de Mantenimiento</label>
                        <select class="form-select @error('maintenance_type') is-invalid @enderror" id="maintenance_type" name="maintenance_type" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="preventive" {{ old('maintenance_type') == 'preventive' ? 'selected' : '' }}>Preventivo</option>
                            <option value="corrective" {{ old('maintenance_type') == 'corrective' ? 'selected' : '' }}>Correctivo</option>
                            <option value="predictive" {{ old('maintenance_type') == 'predictive' ? 'selected' : '' }}>Predictivo</option>
                        </select>
                        @error('maintenance_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnóstico</label>
                    <textarea class="form-control @error('diagnosis') is-invalid @enderror" id="diagnosis" name="diagnosis" rows="4" required>{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="procedure" class="form-label">Procedimiento a Realizar</label>
                    <textarea class="form-control @error('procedure') is-invalid @enderror" id="procedure" name="procedure" rows="4" required>{{ old('procedure') }}</textarea>
                    @error('procedure')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> El mantenimiento será registrado como "En Proceso" y el técnico responsable será el usuario actual.
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Iniciar Mantenimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection