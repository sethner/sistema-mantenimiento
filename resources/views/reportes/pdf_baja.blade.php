<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Equipos de Baja - {{ date('Y') }}</title>
    <style>
        @page {
            margin: 0px;
        }

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
            border-top: 6px solid #475569;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 40px;
            margin: 0;
            overflow: hidden;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-box {
            width: 40%;
            vertical-align: middle;
            color: #0f172a;
            font-weight: 900;
        }

        .logo-img {
            max-height: 50px;
        }

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
            color: #475569;
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
        .info-container {
            padding: 30px 40px 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-column {
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

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

        /* MÉTRICAS RÁPIDAS (KPIs) */
        .kpi-container {
            padding: 0 40px 20px;
        }

        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kpi-box {
            width: 100%;
            padding: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
            border-radius: 8px;
        }

        .kpi-value {
            font-size: 14px;
            font-weight: 900;
            color: #475569;
            display: block;
        }

        .kpi-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 4px;
        }

        /* LISTADO DE BIENES DADOS DE BAJA */
        .table-container {
            padding: 10px 40px;
        }

        .category-header {
            background-color: #f8fafc;
            padding: 10px 15px;
            border-left: 4px solid #475569;
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
            background-color: #475569;
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

        .equipo-row {
            background-color: #f8fafc;
            font-weight: bold;
        }

        .component-row td {
            color: #64748b;
            font-size: 8px;
            background-color: #ffffff;
            padding-left: 25px !important;
            border-bottom: 1px solid #f8fafc;
        }

        /* FIRMAS */
        .signature-container {
            padding: 40px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-box {
            width: 50%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #cbd5e1;
            width: 180px;
            margin: 0 auto 5px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 9px;
            color: #1e293b;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

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
                    <p>RETIRO DE ACTIVOS E INVENTARIO</p>
                    <h1>EQUIPOS DADOS DE BAJA</h1>
                    <p>REF: BAJA-{{ date('Ymd') }}-AIP</p>
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
                        Director: {{ $config->director_nombre }}<br>
                        Dirección: {{ $config->direccion ?? 'No especificada' }}<br>
                        RUC: {{ $config->ruc ?? 'No especificado' }}
                    </div>
                </td>
                <td class="info-column">
                    <span class="info-label">Detalles del Documento</span>
                    <div class="info-value">
                        Fecha de Emisión: {{ date('d/m/Y') }}<br>
                        Estado del Reporte: Consolidado de Baja Patrimonial<br>
                        Tipo de Auditoría: Técnica / Retiro de Hardware
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- MÉTRICAS RÁPIDAS -->
    <div class="kpi-container">
        <table class="kpi-table">
            <tr>
                <td class="kpi-box">
                    <span class="kpi-value">{{ $equipos->count() }}</span>
                    <span class="kpi-label">TOTAL DE EQUIPOS RETIRADOS Y DADOS DE BAJA</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- LISTADO DE BAJAS -->
    <div class="table-container">
        <div class="category-header">Relación de Bienes Retirados de Servicio</div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">CÓDIGO</th>
                    <th style="width: 30%;">EQUIPO / COMPONENTE</th>
                    <th style="width: 20%;">MARCA / MODELO</th>
                    <th style="width: 15%;">TIPO</th>
                    <th class="text-right">MOTIVO ESTIMADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $e)
                <tr class="equipo-row">
                    <td class="text-center font-bold">{{ $e->codigo }}</td>
                    <td>{{ strtoupper($e->nombre) }}</td>
                    <td>{{ $e->marca ?? 'S/M' }} / {{ $e->modelo ?? 'S/M' }}</td>
                    <td>{{ $e->tipo->nombre ?? 'N/A' }}</td>
                    <td class="text-right" style="font-size: 7px; color: #991b1b;">Obsoleto / Daño Crítico</td>
                </tr>

                {{-- Componentes del equipo --}}
                @foreach($e->componentes as $comp)
                <tr class="component-row">
                    <td class="text-center" style="color: #cbd5e1;">&bull;</td>
                    <td>{{ strtoupper($comp->nombre) }}</td>
                    <td>{{ $comp->categoria->nombre ?? 'HARDWARE' }}</td>
                    <td>{{ $comp->num_serie ?? '-' }}</td>
                    <td class="text-right" style="font-size: 7.5px; font-style: italic;">Componente Obsoleto</td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
                        No hay equipos registrados en estado de baja.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 15px; background-color: #f8fafc; border-left: 4px solid #64748b; padding: 12px; font-size: 8.5px; line-height: 1.4; color: #475569;">
            <strong>Nota de Auditoría Patrimonial:</strong> Los equipos y componentes listados en este reporte han sido declarados inoperativos de forma definitiva. Se recomienda proceder a su almacenamiento físico para su posterior disposición de residuo tecnológico según la normativa vigente.
        </div>
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
        Reporte de Bajas AIP | Generado automáticamente por el Sistema de Gestión de Mantenimiento | Página 1 de 1
    </div>

</body>

</html>