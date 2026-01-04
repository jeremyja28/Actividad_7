<?php
require_once 'security.php';
include("connect.php");

// Consulta para obtener productos con stock bajo o nulo
$sql = "SELECT v.id, v.sku, v.stock_minimo, p.nombre AS producto,
        vc.valor AS color, vcap.valor AS capacidad, vmod.valor AS modelo,
        COALESCE((SELECT SUM(cantidad) FROM compras WHERE variante_id = v.id), 0) as stock_actual
        FROM variantes v
        JOIN productos p ON p.id=v.producto_id
        LEFT JOIN valores_atributo vc ON vc.id=v.color_id
        LEFT JOIN valores_atributo vcap ON vcap.id=v.capacidad_id
        LEFT JOIN valores_atributo vmod ON vmod.id=v.modelo_id
        HAVING stock_actual <= stock_minimo
        ORDER BY stock_actual ASC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas de Stock | InventoryOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #dc3545; /* Rojo para alertas */
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
        }
        body {
            background-color: var(--bg-color);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
        }
        .navbar {
            background-color: var(--card-bg);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            margin-top: 2rem;
        }
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }
        .card-custom {
            background-color: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .table thead th {
            background-color: #fef2f2; /* Rojo muy claro */
            color: #991b1b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #fee2e2;
            padding: 1rem;
        }
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            color: var(--text-main);
            font-size: 0.875rem;
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background-color: #b91c1c;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Alertas de Stock</h1>
            <a href="create_modify_compra/agregar_compra.php" class="btn btn-primary-custom">
                <i class="bi bi-cart-plus"></i> Reponer Stock
            </a>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th>Atributos</th>
                            <th>Stock Mínimo</th>
                            <th>Stock Actual</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res && $res->num_rows > 0): ?>
                            <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['producto']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['sku']); ?></span></td>
                                <td>
                                    <?php 
                                    $attrs = [];
                                    if($row['color']) $attrs[] = $row['color'];
                                    if($row['capacidad']) $attrs[] = $row['capacidad'];
                                    if($row['modelo']) $attrs[] = $row['modelo'];
                                    echo htmlspecialchars(implode(' / ', $attrs));
                                    ?>
                                </td>
                                <td><?php echo $row['stock_minimo']; ?></td>
                                <td>
                                    <span class="badge bg-danger rounded-pill fs-6">
                                        <?php echo $row['stock_actual']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['stock_actual'] == 0): ?>
                                        <span class="text-danger fw-bold">Agotado</span>
                                    <?php else: ?>
                                        <span class="text-warning fw-bold">Bajo Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-check-circle text-success fs-1 d-block mb-3"></i>
                                    ¡Todo en orden! No hay productos con stock bajo.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
