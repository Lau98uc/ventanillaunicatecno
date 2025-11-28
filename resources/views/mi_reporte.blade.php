<!-- resources/views/mi_reporte.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Mi Reporte</title>
    <style>
        body {
            font-family: sans-serif;
        }

        h1 {
            color: #333;
        }
    </style>
</head>

<body>
    <h1>Hola, este es tu PDF generado</h1>
    <p>Generado en: {{ now() }}</p>
</body>

</html>
