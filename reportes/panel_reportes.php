<?php
require_once '../security.php';
require_once '../connect.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | InventoryOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #4f46e5; }
        .card-icon { font-size: 2rem; color: var(--primary-color); }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container mt-5">
        <h2 class="mb-4 fw-bold text-primary"><i class="bi bi-bar-chart-line"></i> Panel de Reportes</h2>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-receipt card-icon mb-3"></i>
                        <h5 class="card-title">Reporte de Ventas</h5>
                        <p class="card-text text-muted">Visualiza el historial de ventas realizadas.</p>
                        <a href="#" class="btn btn-outline-primary stretched-link">Ver Reporte</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam card-icon mb-3"></i>
                        <h5 class="card-title">Movimientos de Stock</h5>
                        <p class="card-text text-muted">Entradas y salidas de inventario.</p>
                        <a href="#" class="btn btn-outline-primary stretched-link">Ver Reporte</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-people card-icon mb-3"></i>
                        <h5 class="card-title">Mejores Clientes</h5>
                        <p class="card-text text-muted">Análisis de compras por cliente.</p>
                        <a href="#" class="btn btn-outline-primary stretched-link">Ver Reporte</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle-fill"></i> Módulo de reportes en construcción. Próximamente más funcionalidades.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>