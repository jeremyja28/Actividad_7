<?php
require_once '../security.php';
include("../connect.php");
$sql = "SELECT v.id, v.sku, v.precio, p.nombre AS producto,
        vc.valor AS color, vcap.valor AS capacidad, vmod.valor AS modelo
        FROM variantes v
        JOIN productos p ON p.id=v.producto_id
        LEFT JOIN valores_atributo vc ON vc.id=v.color_id
        LEFT JOIN valores_atributo vcap ON vcap.id=v.capacidad_id
        LEFT JOIN valores_atributo vmod ON vmod.id=v.modelo_id
        ORDER BY v.id DESC";
$res=$conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variantes | InventoryOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
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
            background-color: #f9fafb;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
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
            background-color: #4338ca;
            color: white;
        }
        .action-btn {
            color: var(--text-muted);
            transition: color 0.2s;
        }
        .action-btn:hover { color: var(--primary-color); }
        .badge-attr {
            background-color: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-right: 4px;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container px-4">
    <div class="page-header">
        <h1 class="page-title">Variantes</h1>
        <a href="agregar_variante.php" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg"></i> Nueva Variante
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
                        <th class="text-end">Precio</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && $res->num_rows>0): while($v=$res->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($v['producto']); ?></td>
                        <td class="text-muted font-monospace"><?php echo htmlspecialchars($v['sku']); ?></td>
                        <td>
                            <?php if($v['color']): ?><span class="badge-attr">Color: <?php echo htmlspecialchars($v['color']); ?></span><?php endif; ?>
                            <?php if($v['capacidad']): ?><span class="badge-attr">Cap: <?php echo htmlspecialchars($v['capacidad']); ?></span><?php endif; ?>
                            <?php if($v['modelo']): ?><span class="badge-attr">Mod: <?php echo htmlspecialchars($v['modelo']); ?></span><?php endif; ?>
                        </td>
                        <td class="text-end fw-bold text-success">$<?php echo number_format($v['precio'], 2); ?></td>
                        <td class="text-end">
                            <a href="editar_variante.php?id=<?php echo $v['id']; ?>" class="action-btn" title="Editar">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Sin variantes registradas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
