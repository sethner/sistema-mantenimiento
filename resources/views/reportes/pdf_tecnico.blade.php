<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Trabajo - {{ date('Y') }}</title>
    <style>
        @page {
            margin: 25px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #000;
            page-break-inside: avoid;
        }
        .header-table {
            width: 100%;
            border-bottom: 1px solid #000;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 8px;
            vertical-align: middle;
        }
        .logo-box {
            width: 90px;
            text-align: center;
        }
        .logo-img {
            max-width: 80px;
            max-height: 80px;
        }
        .header-info {
            text-align: center;
        }
        .header-info h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header-info p {
            margin: 1px 0;
            font-size: 11px;
            font-weight: bold;
        }
        .red-bar {
            background-color: #e11d48; /* Red rose */
            color: #ffffff;
            text-align: center;
            padding: 4px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .meta-table, .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td, .data-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
        }
        .label {
            font-weight: bold;
            background-color: #f3f4f6;
            width: 15%;
        }
        .section-title {
            background-color: #e11d48;
            color: #fff;
            padding: 3px 10px;
            font-weight: bold;
            border-top: 1px solid #000;
            font-size: 11px;
        }
        .content-box {
            border: 1px solid #000;
            padding: 12px;
            min-height: 45px;
            margin: 8px;
            background-color: #fff;
            font-size: 11px;
            line-height: 1.4;
        }
        /* SIGNATURE DESIGN */
        .signature-container {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #000;
        }
        .signature-container td {
            width: 50%;
            border: 1px solid #000;
            height: 100px;
            vertical-align: bottom;
            text-align: center;
            padding: 0;
        }
        .signature-space {
            height: 75px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 0 20px;
            padding-top: 3px;
        }
        .signature-name {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
        }
        .signature-label-bar {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            border-top: 1px solid #000;
            text-transform: uppercase;
        }
        .footer-info {
            text-align: right;
            font-size: 8px;
            margin-top: 4px;
            color: #444;
            font-style: italic;
        }
    </style>
</head>
<body>

    @forelse($mantenimientos as $m)
    <div class="container">
        {{-- Encabezado con Logo --}}
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    @if($config->logo_base64)
                        <img src="{{ $config->logo_base64 }}" class="logo-img">
                    @else
                        <div style="font-size: 8px; color: #999; border: 1px dashed #ccc; padding: 10px;">LOGO NO DISPONIBLE</div>
                    @endif
                </td>
                <td class="header-info">
                    <h1>{{ strtoupper($config->nombre_institucion) }}</h1>
                    <p>MANTENIMIENTO DE INFRAESTRUCTURA Y EQUIPAMIENTO AIP</p>
                    <p style="font-size: 8px;">RUC: {{ $config->ruc }} | Dirección: {{ $config->direccion }}</p>
                    <p>{{ date('Y') }} - "Año de la Consolidación del Mar de Grau"</p>
                </td>
                <td style="width: 80px; text-align: right; font-weight: bold; font-size: 10px;">
                    OT-{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        </table>

        <div class="red-bar">Orden de Trabajo</div>

        {{-- Metadatos de la O.T --}}
        <table class="meta-table">
            <tr>
                <td class="label">Fecha O.T:</td>
                <td style="width: 25%">{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
                <td style="text-align: center; font-weight: bold; font-size: 13px; color: #e11d48;">
                    MANTENIMIENTO {{ strtoupper($m->tipo) }}
                </td>
                <td class="label">Emisión:</td>
                <td style="width: 20%">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        {{-- Datos del Equipo --}}
        <table class="data-table">
            <tr>
                <td class="label">Código:</td>
                <td style="width: 35%">{{ $m->equipo->codigo ?? 'N/A' }}</td>
                <td class="label">Marca:</td>
                <td>{{ $m->equipo->marca ?? 'S/M' }}</td>
            </tr>
            <tr>
                <td class="label">Equipo:</td>
                <td>{{ strtoupper($m->equipo->nombre ?? 'N/A') }}</td>
                <td class="label">Modelo:</td>
                <td>{{ $m->equipo->modelo ?? 'S/M' }}</td>
            </tr>
        </table>

        <div class="section-title">DESCRIPCIÓN DE FALLA / DIAGNÓSTICO TÉCNICO:</div>
        <div class="content-box">
            {{ $m->diagnostico ?: 'Sin diagnóstico registrado.' }}
        </div>

        <div class="section-title">ACTIVIDADES Y REPARACIONES REALIZADAS:</div>
        <div class="content-box">
            {{ $m->accion ?: 'Sin acciones registradas.' }}
        </div>

        {{-- Estado y Responsable --}}
        <table class="meta-table">
            <tr>
                <td class="label">Estado Final:</td>
                <td style="width: 25%; font-weight: bold;">{{ strtoupper(str_replace('_', ' ', $m->estado)) }}</td>
                <td class="label">Costo:</td>
                <td style="width: 20%; font-weight: bold;">S/ {{ number_format($m->costo, 2) }}</td>
                <td class="label">Técnico:</td>
                <td>{{ $m->usuario->name ?? 'N/A' }}</td>
            </tr>
        </table>

        {{-- BLOQUE DE FIRMAS --}}
        <div class="section-title">CERTIFICACIÓN DE CONFORMIDAD (Firma y Sello):</div>
        <table class="signature-container">
            <tr>
                <td>
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ strtoupper($config->director_nombre) }}</div>
                    <div class="signature-label-bar">DIRECTOR / SUPERVISOR</div>
                </td>
                <td>
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ strtoupper($m->usuario->name ?? 'TÉCNICO') }}</div>
                    <div class="signature-label-bar">TÉCNICO ENCARGADO</div>
                </td>
            </tr>
        </table>
    </div>
    
    @if(!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif

    @empty
    <div style="text-align: center; padding: 50px;">
        <h3>No hay mantenimientos registrados en este reporte.</h3>
    </div>
    @endforelse

    <div class="footer-info">
        Documento generado electrónicamente el {{ now()->format('d/m/Y \a \l\a\s H:i:s') }}
    </div>

</body>
</html>
