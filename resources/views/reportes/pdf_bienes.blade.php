<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Bienes - {{ date('Y') }}</title>
    <style>
        @page { margin: 0px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #334155; 
            margin: 0; 
            padding: 0; 
            background-color: #ffffff;
        }
        
        /* HEADER PRINCIPAL (Estilo Corporativo) */
        .header-bar { 
            background-color: #ffffff; 
            border-top: 6px solid #0284c7;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 40px; 
            margin: 0;
            overflow: hidden;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-box { 
            width: 40%; 
            vertical-align: middle;
            color: #0f172a;
            font-weight: 900;
        }
        .logo-img { max-height: 50px; }
        .header-title-box { 
            width: 60%; 
            text-align: right; 
            vertical-align: middle;
        }
        .header-title-box p:first-child { 
            margin: 0 0 3px; 
            font-size: 9px; 
            font-weight: bold; 
            color: #64748b; 
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .header-title-box h1 { 
            margin: 0; 
            font-size: 18px; 
            font-weight: 800; 
            color: #0284c7; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        .header-title-box p:last-child { 
            margin: 4px 0 0; 
            font-size: 8px; 
            color: #94a3b8; 
            letter-spacing: 1px;
        }

        /* INFORMACIÓN DE SECCIÓN */
        .info-container { padding: 30px 40px 10px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-column { width: 50%; vertical-align: top; padding-right: 20px; }
        .info-label { 
            font-size: 9px; 
            font-weight: 900; 
            color: #000; 
            text-transform: uppercase; 
            margin-bottom: 5px;
            display: block;
        }
        .info-value { 
            font-size: 10px; 
            color: #475569; 
            margin-bottom: 15px; 
            line-height: 1.4;
        }

        /* TABLA DE DATOS */
        .table-container { padding: 10px 40px; }
        .category-header {
            background-color: #f8fafc;
            padding: 10px 15px;
            border-left: 4px solid #0284c7;
            margin-bottom: 10px;
            font-weight: 900;
            font-size: 11px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px;
        }
        .data-table thead th { 
            background-color: #0284c7; 
            color: white; 
            text-align: left; 
            padding: 10px 12px; 
            font-size: 9px; 
            text-transform: uppercase;
            font-weight: bold;
        }
        .data-table tbody td { 
            padding: 10px 12px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 9px;
            vertical-align: middle;
        }
        
        .equipo-row { background-color: #f8fafc; font-weight: bold; }
        .component-row td { 
            color: #64748b; 
            font-size: 8px; 
            background-color: #ffffff;
            padding-left: 25px !important;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-bueno { background-color: #dcfce7; color: #166534; }
        .status-regular { background-color: #fef9c3; color: #854d0e; }
        .status-malo { background-color: #fee2e2; color: #991b1b; }

        /* SIGNATURES */
        .signature-container { padding: 40px; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-box { width: 50%; text-align: center; }
        .signature-line { 
            border-top: 1px solid #cbd5e1; 
            width: 180px; 
            margin: 0 auto 5px; 
            padding-top: 10px;
            font-weight: bold;
            font-size: 9px;
            color: #1e293b;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .page-footer { 
            position: fixed; 
            bottom: 20px; 
            left: 40px; 
            right: 40px; 
            font-size: 8px; 
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header-bar">
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    @if($config->logo_base64)
                        <img src="{{ $config->logo_base64 }}" class="logo-img">
                    @else
                        <span style="font-size: 20px; font-weight: 900;">SISTEMA AIP</span>
                    @endif
                </td>
                <td class="header-title-box">
                    <p>CONTROL PATRIMONIAL {{ date('Y') }}</p>
                    <h1>INVENTARIO DE BIENES</h1>
                    <p>REF: INV-{{ date('Ymd') }}-AIP</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="info-container">
        <table class="info-table">
            <tr>
                <td class="info-column">
                    <span class="info-label">Sede / Institución</span>
                    <div class="info-value">
                        <strong>{{ strtoupper($config->nombre_institucion) }}</strong><br>
                        Aula de Innovación Pedagógica (AIP)<br>
                        Responsable: {{ $config->director_nombre }}<br>
                        Dirección: {{ $config->direccion ?? 'No especificada' }}<br>
                        RUC: {{ $config->ruc ?? 'No especificado' }}
                    </div>
                </td>
                <td class="info-column">
                    <span class="info-label">Detalles del Documento</span>
                    <div class="info-value">
                        Fecha de Corte: {{ date('d/m/Y') }}<br>
                        Estado del Inventario: Consolidado Anual<br>
                        Tipo de Auditoría: Técnica / Hardware
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- LISTADO POR CATEGORÍAS -->
    <div class="table-container">
        @foreach($equiposAgrupados as $tipo => $equipos)
            <div class="category-header">SECCIÓN: {{ strtoupper($tipo) }}</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">CÓDIGO</th>
                        <th style="width: 30%;">BIEN / COMPONENTE</th>
                        <th style="width: 15%;">MARCA / MODELO</th>
                        <th style="width: 10%;" class="text-center">ESTADO</th>
                        <th style="width: 5%;" class="text-center">B</th>
                        <th style="width: 5%;" class="text-center">R</th>
                        <th style="width: 5%;" class="text-center">M</th>
                        <th class="text-right">OBSERVACIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipos as $equipo)
                        @php
                            $rowStyle = '';
                            if ($equipo->estado == 'con_falla') {
                                $rowStyle = 'background-color: #fee2e2; color: #991b1b;';
                            } elseif ($equipo->estado == 'en_mantenimiento') {
                                $rowStyle = 'background-color: #fef9c3; color: #854d0e;';
                            }
                        @endphp
                        <tr class="equipo-row" style="{{ $rowStyle }}">
                            <td class="text-center">{{ $equipo->codigo }}</td>
                            <td>{{ strtoupper($equipo->nombre) }}</td>
                            <td>{{ $equipo->marca ?? '-' }} / {{ $equipo->modelo ?? '-' }}</td>
                            <td class="text-center" style="font-size: 7px; font-weight: bold;">{{ strtoupper(str_replace('_', ' ', $equipo->estado)) }}</td>
                            <td class="text-center font-bold">{{ $equipo->estado == 'operativo' ? 'X' : '' }}</td>
                            <td class="text-center font-bold">{{ $equipo->estado == 'en_mantenimiento' ? 'X' : '' }}</td>
                            <td class="text-center font-bold">{{ ($equipo->estado == 'con_falla' || $equipo->estado == 'de_baja' || $equipo->estado == 'dado_de_baja') ? 'X' : '' }}</td>
                            <td class="text-right" style="font-size: 7px;">Principal</td>
                        </tr>
                        
                        {{-- Componentes del equipo --}}
                        @foreach($equipo->componentes as $comp)
                            @php $estComp = strtolower($comp->pivot->estado ?? 'bueno'); @endphp
                            <tr class="component-row">
                                <td class="text-center" style="color: #cbd5e1;">&bull;</td>
                                <td>{{ strtoupper($comp->nombre) }}</td>
                                <td>{{ $comp->categoria->nombre ?? 'HARDWARE' }}</td>
                                <td class="text-center" style="font-size: 7px; opacity: 0.7;">{{ strtoupper($estComp) }}</td>
                                <td class="text-center">{{ $estComp == 'bueno' ? 'x' : '' }}</td>
                                <td class="text-center">{{ $estComp == 'regular' ? 'x' : '' }}</td>
                                <td class="text-center">{{ $estComp == 'malo' ? 'x' : '' }}</td>
                                <td class="text-right" style="font-size: 7px;">Accesorio</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    <!-- FIRMAS -->
    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <div class="signature-line">RESPONSABLE AIP</div>
                    <span style="font-size: 7px; color: #94a3b8;">Control de Activos Digitales</span>
                </td>
                <td class="signature-box">
                    <div class="signature-line">{{ strtoupper($config->director_nombre) }}</div>
                    <span style="font-size: 7px; color: #94a3b8;">Director / Firma y Sello</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footer">
        Inventario Patrimonial AIP | Documento Generado por el Sistema de Gestión de Mantenimiento | Página 1 de 1
    </div>

</body>
</html>