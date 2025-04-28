@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Actas</h1>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i> Filtros de búsqueda
        </div>
        <div class="card-body">
            <form action="{{ route('certificates.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="code" class="form-label">Código</label>
                    <input type="text" class="form-control" id="code" name="code" value="{{ request('code') }}" placeholder="Buscar por código...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente de Firmas</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-file-signature me-1"></i> Listado de Actas
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Activo</th>
                            <th>Departamento</th>
                            <th>Técnico</th>
                            <th>Fecha Generación</th>
                            <th>Estado</th>
                            <th>Firmas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->code }}</td>
                                <td>
                                    <a href="{{ route('assets.show', $certificate->maintenance->asset->id) }}">
                                        {{ $certificate->maintenance->asset->name }}
                                    </a>
                                </td>
                                <td>{{ $certificate->maintenance->asset->department->name }}</td>
                                <td>{{ $certificate->maintenance->technician->name }}</td>
                                <td>{{ $certificate->generation_date->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($certificate->status == 'pending')
                                        <span class="badge bg-warning">Pendiente de Firmas</span>
                                    @elseif($certificate->status == 'completed')
                                        <span class="badge bg-success">Completada</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalSignatures = $certificate->signatures->count();
                                        $requiredSignatures = 2;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($totalSignatures / $requiredSignatures) * 100 }}%;" 
                                            aria-valuenow="{{ $totalSignatures }}" aria-valuemin="0" aria-valuemax="{{ $requiredSignatures }}">
                                            {{ $totalSignatures }}/{{ $requiredSignatures }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('certificates.show', $certificate->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($certificate->status == 'pending')
                                            <a href="{{ route('certificates.sign', $certificate->id) }}" class="btn btn-sm btn-success" title="Firmar">
                                                <i class="fas fa-signature"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">No hay actas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $certificates->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection