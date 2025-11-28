{{-- resources/views/reportes/tramites_por_estado.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte por Estados</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Reporte de Solicitudes por Estados</h1>
    <p>Generado: {{ $fechaActual }}</p>

    <p><strong>Filtros:</strong></p>
    <ul>
        <li>Desde: {{ $filters['fecha_desde'] ?? 'No definido' }}</li>
        <li>Hasta: {{ $filters['fecha_hasta'] ?? 'No definido' }}</li>
        <li>Estado: {{ $filters['estado'] ?? 'Todos' }}</li>
        <li>Encargado: {{ $filters['encargado_id'] ?? 'Todos' }}</li>
        <li>Trámite: {{ $filters['tramite_id'] ?? 'Todos' }}</li>
    </ul>

    @foreach ($reporte as $tramite => $estados)
    <h3>{{ $tramite }}</h3>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estados as $estado => $cantidad)
            <tr>
                <td>{{ $estado ?? 'Sin estado' }}</td>
                <td>{{ $cantidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
</body>

</html>
