@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Generar Acta de Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-signature me-1"></i> Información para el Acta
                </div>
                <div class="card-body">
                    <form action="{{ route('certificates.store', $maintenance->id) }}" method="POST">
                        @csrf

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Se generará un acta para el mantenimiento seleccionado con la siguiente información:
                        </div>

                        <table class="table">
                            <tr>
                                <th style="width: 30%">Mantenimiento ID:</th>
                                <td>{{ $maintenance->id }}</td>
                            </tr>
                            <tr>
                                <th>Tipo de Mantenimiento:</th>
                                <td>
                                    @if($maintenance->maintenance_type == 'preventive')
                                        <span class="badge bg-success">Preventivo</span>
                                    @elseif($maintenance->maintenance_type == 'corrective')
                                        <span class="badge bg-danger">Correctivo</span>
                                    @else
                                        <span class="badge bg-info">Predictivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Estado del Mantenimiento:</th>
                                <td>
                                    @if($maintenance->status == 'in_progress')
                                        <span class="badge bg-warning">En Proceso</span>
                                    @elseif($maintenance->status == 'completed')
                                        <span class="badge bg-success">Completado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Activo:</th>
                                <td>{{ $maintenance->asset->name }} ({{ $maintenance->asset->type }})</td>
                            </tr>
                            <tr>
                                <th>Departamento:</th>
                                <td>{{ $maintenance->asset->department->name }}</td>
                            </tr>
                            <tr>
                                <th>Técnico Responsable:</th>
                                <td>{{ $maintenance->technician->name }}</td>
                            </tr>
                            <tr>
                                <th>Jefe de Departamento:</th>
                                <td>{{ $maintenance->asset->department->manager->name ?? 'No asignado' }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Inicio:</th>
                                <td>{{ $maintenance->start_date->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Fin:</th>
                                <td>{{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') : 'En proceso' }}</td>
                            </tr>
                        </table>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Una vez generada el acta, se requerirá la firma digital del técnico responsable y del jefe de departamento para completar el proceso.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-pdf"></i> Generar Acta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-question-circle me-1"></i> Información
                </div>
                <div class="card-body">
                    <h5>¿Qué es un acta de mantenimiento?</h5>
                    <p>
                        El acta de mantenimiento es un documento oficial que certifica la realización del servicio
                        de mantenimiento en un activo tecnológico. Este documento requiere la firma tanto del técnico
                        que realizó el mantenimiento como del jefe del departamento al que pertenece el activo.
                    </p>

                    <h5>Proceso de firma</h5>
                    <ol>
                        <li>El técnico que realizó el mantenimiento debe firmar primero el acta.</li>
                        <li>El jefe del departamento debe revisar y firmar el acta para dar su conformidad.</li>
                        <li>Una vez firmada por ambas partes, el acta se considera completada.</li>
                    </ol>

                    <h5>Consideraciones</h5>
                    <ul>
                        <li>Solo se pueden generar actas para mantenimientos completados.</li>
                        <li>El acta generada estará disponible para su descarga en formato PDF.</li>
                        <li>Las firmas digitales quedan registradas con fecha y hora en el sistema.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection