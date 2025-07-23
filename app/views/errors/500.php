<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del servidor - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="mb-4">
                    <i class="bi bi-exclamation-octagon-fill text-danger" style="font-size: 5rem;"></i>
                </div>
                <h1 class="display-1 fw-bold text-muted">500</h1>
                <h2 class="mb-4">Error del servidor</h2>
                <p class="lead mb-4">
                    Ha ocurrido un error interno en el servidor. 
                    Por favor, inténtelo de nuevo más tarde.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="/" class="btn btn-primary">
                        <i class="bi bi-house"></i>
                        Ir al Inicio
                    </a>
                    <button onclick="location.reload()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reintentar
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>