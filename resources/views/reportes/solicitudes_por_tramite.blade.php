{{-- resources/views/reportes/solicitudes_por_tramite.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Solicitudes</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 0;
        }

        .small {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>Reporte de Solicitudes por Trámite</h1>
    <p class="small">Generado: {{ $fechaActual }}</p>

    <p><strong>Rango:</strong> {{ $fechaDesde }} a {{ $fechaHasta }}</p>
    <p><strong>Estado:</strong> {{ $estado ?? 'Todos' }}</p>
    <p><strong>Encargado:</strong> {{ $encargadoId ?? 'Todos' }}</p>
    <p><strong>Trámite:</strong> {{ $tramiteId ?? 'Todos' }}</p>

    <h3>Total de Solicitudes: {{ $totalSolicitudes }}</h3>

    <h4>Resumen por Trámite:</h4>
    <ul>
        @foreach ($resumenPorTramite as $nombre => $cantidad)
        <li>{{ $nombre }}: {{ $cantidad }}</li>
        @endforeach
    </ul>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Trámite</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitudes as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->tramite->nombre }}</td>
                <td>{{ $s->usuario->nombre ?? 'N/A' }}</td>
                <td>{{ $s->created_at->format('d/m/Y') }}</td>
                <td>{{ $s->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
