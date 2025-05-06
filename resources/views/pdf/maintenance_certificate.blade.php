<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Mantenimiento - {{ $certificate->code }}</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        
        .container {
            width: 100%;
            padding: 10px 15px;
        }
        
        /* Encabezado con logo */
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 10px;
        }
        
        .logo-container {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }
        
        .header-text {
            flex: 1;
        }
        
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .certificate-metadata {
            font-size: 10px;
            color: #555;
        }
        
        .code {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            padding: 3px 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background-color: #f9f9f9;
            display: inline-block;
        }
        
        /* Secciones */
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #fff;
            background-color: #2c3e50;
            padding: 5px 10px;
            border-radius: 3px 3px 0 0;
            margin: 0;
        }
        
        .section-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 8px 10px;
            background-color: #fff;
            border-radius: 0 0 3px 3px;
        }
        
        /* Grid layout para información */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }
        
        .grid-item {
            display: flex;
            border-bottom: 1px dotted #f0f0f0;
            padding: 3px 0;
        }
        
        .grid-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 110px;
            color: #2c3e50;
            font-size: 9pt;
        }
        
        .info-value {
            flex: 1;
            font-size: 9pt;
        }
        
        /* Campos de texto */
        .text-field {
            border: 1px solid #eee;
            padding: 8px;
            background-color: #fafafa;
            min-height: 40px;
            border-radius: 3px;
            text-align: justify;
            font-size: 9pt;
        }
        
        /* Indicadores y badges */
        .priority-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 3px;
            vertical-align: middle;
        }
        
        .priority-low { background-color: #28a745; }
        .priority-medium { background-color: #007bff; }
        .priority-high { background-color: #ffc107; }
        .priority-critical { background-color: #dc3545; }
        
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 2px;
        }
        
        .badge-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-secondary { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        
        /* Firmas */
        .signatures {
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            margin-top: 20px;
        }
        
        .signature-block {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            margin: 8px 0 5px 0;
            border-top: 1px solid #000;
            width: 100%;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 10pt;
        }
        
        .signature-title {
            font-size: 8pt;
            color: #555;
        }
        
        .signature-date {
            font-size: 7pt;
            color: #777;
        }
        
        .signature-image {
            max-width: 100px;
            max-height: 40px;
            min-height: 30px;
        }
        
        /* Tabla de firmas */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        
        .signatures-table th {
            background-color: #f2f2f2;
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .signatures-table td {
            padding: 4px;
            border: 1px solid #ddd;
        }
        
        /* Recomendaciones */
        .recommendations-note {
            background-color: #fffacd;
            border-left: 3px solid #ffd700;
            padding: 6px;
            font-style: italic;
            font-size: 9pt;
        }
        
        /* Imágenes */
        .images-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 5px;
            margin-top: 8px;
        }
        
        .maintenance-image {
            width: 32%;
            border: 1px solid #ddd;
            padding: 2px;
        }
        
        /* Pie de página */
        .footer {
            position: fixed;
            bottom: 10px;
            left: 15px;
            right: 15px;
            font-size: 7pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado optimizado -->
        <div class="header">
            <div class="logo-container">
                <div style="width: 50px; height: 50px; background-color: #2c3e50; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; font-weight: bold; font-size: 12px;">
                    MSJ
                </div>
            </div>
            
            <div class="header-text">
                <div class="title">ACTA DE MANTENIMIENTO DE EQUIPOS TECNOLÓGICOS</div>
                <div class="certificate-metadata">
                    MUNICIPALIDAD DISTRITAL DE SAN JERÓNIMO | OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN
                </div>
                <div class="code">{{ $certificate->code }}</div>
            </div>
        </div>
        
        <!-- Sección 1 y 2 combinadas: Información General y del Equipo -->
        <div class="section">
            <div class="section-title">1. INFORMACIÓN GENERAL Y DEL EQUIPO</div>
            <div class="section-content">
                <div class="grid-container">
                    <div class="grid-item">
                        <div class="info-label">Fecha Inicio:</div>
                        <div class="info-value">{{ $maintenance->start_date->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Tipo:</div>
                        <div class="info-value">
                            @if($maintenance->maintenance_type == 'preventive')
                                <span class="badge badge-success">Preventivo</span>
                            @elseif($maintenance->maintenance_type == 'corrective')
                                <span class="badge badge-danger">Correctivo</span>
                            @elseif($maintenance->maintenance_type == 'predictive')
                                <span class="badge badge-info">Predictivo</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Fecha Fin:</div>
                        <div class="info-value">{{ $maintenance->end_date ? $maintenance->end_date->format('d/m/Y H:i') : 'En proceso' }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Técnico:</div>
                        <div class="info-value">{{ $technician->name }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Departamento:</div>
                        <div class="info-value">{{ $department->name }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Cargo:</div>
                        <div class="info-value">{{ $technician->position }}</div>
                    </div>
                    
                    @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
                        @php
                            $additionalInfo = json_decode($maintenance->additional_info, true);
                        @endphp
                        
                        @if(isset($additionalInfo['priority']))
                            <div class="grid-item">
                                <div class="info-label">Prioridad:</div>
                                <div class="info-value">
                                    @php
                                        $priorityLabel = 'No especificada';
                                        $priorityClass = '';
                                        
                                        switch($additionalInfo['priority']) {
                                            case 'low':
                                                $priorityLabel = 'Baja';
                                                $priorityClass = 'low';
                                                break;
                                            case 'medium':
                                                $priorityLabel = 'Media';
                                                $priorityClass = 'medium';
                                                break;
                                            case 'high':
                                                $priorityLabel = 'Alta';
                                                $priorityClass = 'high';
                                                break;
                                            case 'critical':
                                                $priorityLabel = 'Crítica';
                                                $priorityClass = 'critical';
                                                break;
                                        }
                                    @endphp
                                    <span class="priority-indicator priority-{{ $priorityClass }}"></span>
                                    {{ $priorityLabel }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                
                <hr style="border: none; border-top: 1px dashed #ddd; margin: 5px 0;">
                
                <div class="grid-container">
                    <div class="grid-item">
                        <div class="info-label">Equipo:</div>
                        <div class="info-value">{{ $asset->name }} (ID: {{ $asset->id }})</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Marca/Modelo:</div>
                        <div class="info-value">{{ $asset->brand ?? 'N/E' }} / {{ $asset->model ?? 'N/E' }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Tipo de Equipo:</div>
                        <div class="info-value">{{ $asset->type }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Serial:</div>
                        <div class="info-value">{{ $asset->serial ?? 'No especificado' }}</div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">
                            @if($asset->status == 'active')
                                <span class="badge badge-success">Activo</span>
                            @elseif($asset->status == 'in_maintenance')
                                <span class="badge badge-warning">En Mantenimiento</span>
                            @elseif($asset->status == 'inactive')
                                <span class="badge badge-secondary">Inactivo</span>
                            @else
                                <span class="badge badge-danger">De Baja</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid-item">
                        <div class="info-label">Cód. Patrimonial:</div>
                        <div class="info-value">{{ $asset->patrimony_code ?? 'No asignado' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección Diagnóstico y Procedimiento (combinados) -->
        <div class="section">
            <div class="section-title">2. DIAGNÓSTICO Y PROCEDIMIENTO</div>
            <div class="section-content">
                <div style="font-weight:bold; color:#2c3e50; margin-bottom:3px; font-size:9pt;">Diagnóstico:</div>
                <div class="text-field" style="margin-bottom:8px;">{!! $maintenance->diagnosis !!}</div>
                
                <div style="font-weight:bold; color:#2c3e50; margin-bottom:3px; font-size:9pt;">Procedimiento Realizado:</div>
                <div class="text-field">{!! $maintenance->procedure !!}</div>
            </div>
        </div>
        
        <!-- Sección Materiales y Recomendaciones -->
        @php
            $showMaterials = false;
            $showRecommendations = false;
            
            if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true)) {
                $additionalInfo = json_decode($maintenance->additional_info, true);
                $showMaterials = isset($additionalInfo['materials_used']) && !empty($additionalInfo['materials_used']);
                $showRecommendations = isset($additionalInfo['recommendations']) && !empty($additionalInfo['recommendations']);
            }
        @endphp
        
        @if($showMaterials || $showRecommendations)
            <div class="section">
                <div class="section-title">3. MATERIALES Y RECOMENDACIONES</div>
                <div class="section-content">
                    @if($showMaterials)
                        <div style="font-weight:bold; color:#2c3e50; margin-bottom:3px; font-size:9pt;">Materiales y Repuestos:</div>
                        <div class="text-field" style="margin-bottom:8px;">{{ $additionalInfo['materials_used'] }}</div>
                    @endif
                    
                    @if($showRecommendations)
                        <div style="font-weight:bold; color:#2c3e50; margin-bottom:3px; font-size:9pt;">Recomendaciones:</div>
                        <div class="recommendations-note">{{ $additionalInfo['recommendations'] }}</div>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Sección Observaciones y Próximo Mantenimiento -->
        <div class="section">
            <div class="section-title">{{ ($showMaterials || $showRecommendations) ? '4' : '3' }}. OBSERVACIONES Y SEGUIMIENTO</div>
            <div class="section-content">
                <div class="text-field">
                    <strong>Registro:</strong> {{ $maintenance->start_date->format('d/m/Y H:i') }} - Inicio de mantenimiento
                    @if($maintenance->end_date)
                        | {{ $maintenance->end_date->format('d/m/Y H:i') }} - Finalización
                    @else
                        | <span style="color:#ff8c00;">En proceso - No finalizado</span>
                    @endif
                    
                    @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
                        @php
                            $additionalInfo = json_decode($maintenance->additional_info, true);
                        @endphp
                        
                        @if(isset($additionalInfo['next_maintenance_date']) && !empty($additionalInfo['next_maintenance_date']))
                            <div style="margin-top:5px;"><strong>Próximo mantenimiento:</strong> {{ \Carbon\Carbon::parse($additionalInfo['next_maintenance_date'])->format('d/m/Y') }}
                                @if(isset($additionalInfo['next_maintenance_type']))
                                    - 
                                    @if($additionalInfo['next_maintenance_type'] == 'preventive')
                                        <span class="badge badge-success">Preventivo</span>
                                    @elseif($additionalInfo['next_maintenance_type'] == 'corrective')
                                        <span class="badge badge-danger">Correctivo</span>
                                    @elseif($additionalInfo['next_maintenance_type'] == 'predictive')
                                        <span class="badge badge-info">Predictivo</span>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Imágenes del mantenimiento (si existen) -->
        @if(isset($maintenance->additional_info) && json_decode($maintenance->additional_info, true))
            @php
                $additionalInfo = json_decode($maintenance->additional_info, true);
            @endphp
            
            @if(isset($additionalInfo['images']) && !empty($additionalInfo['images']))
                <div class="section">
                    <div class="section-title">{{ ($showMaterials || $showRecommendations) ? '5' : '4' }}. EVIDENCIA FOTOGRÁFICA</div>
                    <div class="section-content">
                        <div class="images-container">
                            @foreach($additionalInfo['images'] as $image)
                                <img src="{{ public_path('storage/' . $image) }}" class="maintenance-image" alt="Imagen de mantenimiento">
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Firmas -->
        @php
            $sectionNumber = ($showMaterials || $showRecommendations) ? 
                (isset($additionalInfo['images']) ? '6' : '5') : 
                (isset($additionalInfo['images']) ? '5' : '4');
        @endphp
        
        <div class="section">
            <div class="section-title">{{ $sectionNumber }}. FIRMAS DE CONFORMIDAD</div>
            <div class="section-content">
                <div class="signatures">
                    <div class="signature-block">
                        @if($signatures && isset($technicianSignature))
                            <img src="data:image/png;base64,{{ $technicianSignature->digital_signature }}" class="signature-image">
                        @else
                            <div style="height: 40px;"></div>
                        @endif
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $technician->name }}</div>
                        <div class="signature-title">Técnico Responsable</div>
                        @if($signatures && isset($technicianSignature))
                            <div class="signature-date">{{ $technicianSignature->signature_date->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                    
                    <div class="signature-block">
                        @if($signatures && isset($managerSignature))
                            <img src="data:image/png;base64,{{ $managerSignature->digital_signature }}" class="signature-image">
                        @else
                            <div style="height: 40px;"></div>
                        @endif
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $manager->name }}</div>
                        <div class="signature-title">Jefe de Departamento</div>
                        @if($signatures && isset($managerSignature))
                            <div class="signature-date">{{ $managerSignature->signature_date->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>
                
                @if($signatures)
                    <table class="signatures-table">
                        <thead>
                            <tr>
                                <th>Firmante</th>
                                <th>Cargo</th>
                                <th>Tipo</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificate->signatures as $signature)
                                <tr>
                                    <td>{{ $signature->user->name }}</td>
                                    <td>{{ $signature->user->position }}</td>
                                    <td>{{ $signature->signature_type == 'technician' ? 'Técnico' : 'Jefe' }}</td>
                                    <td>{{ $signature->signature_date->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    
    <div class="footer">
        Documento oficial generado por el Sistema de Gestión de Mantenimiento de Activos Tecnológicos | {{ $date }} {{ $time }} | Código: {{ $certificate->code }}<br>
        Municipalidad Distrital de San Jerónimo - Oficina de Tecnologías de la Información
    </div>
</body>
</html>