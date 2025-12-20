<?php
include("../connect.php");
// Filtrosss
$f_producto = isset($_GET['producto_id']) ? (int)$_GET['producto_id'] : 0;
$f_proveedor = isset($_GET['proveedor_id']) ? (int)$_GET['proveedor_id'] : 0;
$f_variante = isset($_GET['variante_id']) ? (int)$_GET['variante_id'] : 0;
$f_desde = $_GET['desde'] ?? '';
$f_hasta = $_GET['hasta'] ?? '';

$where = [];
$params = [];
$types = '';

if ($f_producto > 0) { $where[] = 'p.id = ?'; $params[]=$f_producto; $types.='i'; }
if ($f_proveedor > 0) { $where[] = 'pr.id = ?'; $params[]=$f_proveedor; $types.='i'; }
if ($f_variante > 0) { $where[] = 'c.variante_id = ?'; $params[]=$f_variante; $types.='i'; }
if ($f_desde !== '') { $where[] = 'c.fecha_compra >= ?'; $params[]=$f_desde.' 00:00:00'; $types.='s'; }
if ($f_hasta !== '') { $where[] = 'c.fecha_compra <= ?'; $params[]=$f_hasta.' 23:59:59'; $types.='s'; }

$sql = "SELECT c.id, c.fecha_compra, c.precio_unitario, c.cantidad, (c.precio_unitario * c.cantidad) AS total,
        v.sku, p.nombre AS producto, pr.nombre_empresa AS proveedor
        FROM compras c
        JOIN variantes v ON v.id = c.variante_id
        JOIN productos p ON p.id = v.producto_id
        JOIN proveedores pr ON pr.id = c.proveedor_id";
if ($where) { $sql .= ' WHERE '.implode(' AND ', $where); }
$sql .= ' ORDER BY c.fecha_compra DESC';

$stmt = $conn->prepare($sql);
if ($types !== '') { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras | InventoryOS</title>
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
            margin-bottom: 1.5rem;
        }
        .card-header-custom {
            padding: 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            font-weight: 600;
            font-size: 1.1rem;
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
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-main);
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">
            <i class="bi bi-grid-1x2-fill"></i> InventoryOS
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="../index.php" class="btn btn-outline-secondary btn-sm border-0">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</nav>

<div class="container px-4">
    <div class="page-header">
        <h1 class="page-title">Compras</h1>
        <a href="agregar_compra.php" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg"></i> Registrar Compra
        </a>
    </div>

    <!-- Filtros -->
    <div class="card-custom mb-4">
        <div class="card-header-custom">
            <i class="bi bi-funnel"></i> Filtrar Compras
        </div>
        <div class="p-4">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Producto</label>
                    <select name="producto_id" class="form-select">
                        <option value="">-- Todos --</option>
                        <?php $prod=$conn->query("SELECT id,nombre FROM productos ORDER BY nombre"); while($p=$prod->fetch_assoc()){ $sel=($f_producto==$p['id'])?'selected':''; echo '<option value="'.$p['id'].'" '.$sel.'>'.htmlspecialchars($p['nombre']).'</option>'; } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">-- Todos --</option>
                        <?php $prov=$conn->query("SELECT id,nombre_empresa FROM proveedores ORDER BY nombre_empresa"); while($pr=$prov->fetch_assoc()){ $sel=($f_proveedor==$pr['id'])?'selected':''; echo '<option value="'.$pr['id'].'" '.$sel.'>'.htmlspecialchars($pr['nombre_empresa']).'</option>'; } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Variante</label>
                    <select name="variante_id" class="form-select">
                        <option value="">-- Todas --</option>
                        <?php $vars=$conn->query("SELECT v.id, v.sku FROM variantes v ORDER BY v.sku"); while($va=$vars->fetch_assoc()){ $sel=($f_variante==$va['id'])?'selected':''; echo '<option value="'.$va['id'].'" '.$sel.'>'.htmlspecialchars($va['sku']).'</option>'; } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($f_desde); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($f_hasta); ?>">
                </div>
                <div class="col-12 text-end">
                    <a href="listar_compra.php" class="btn btn-outline-secondary btn-sm me-2">Limpiar</a>
                    <button type="submit" class="btn btn-primary-custom btn-sm">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado -->
    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Proveedor</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && $res->num_rows>0): while($c=$res->fetch_assoc()): ?>
                    <tr>
                        <td class="text-muted"><?php echo date('d/m/Y', strtotime($c['fecha_compra'])); ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($c['producto']); ?></td>
                        <td class="text-muted font-monospace small"><?php echo htmlspecialchars($c['sku']); ?></td>
                        <td><?php echo htmlspecialchars($c['proveedor']); ?></td>
                        <td class="text-end">$<?php echo number_format($c['precio_unitario'], 2); ?></td>
                        <td class="text-center"><span class="badge bg-light text-dark border"><?php echo $c['cantidad']; ?></span></td>
                        <td class="text-end fw-bold text-success">$<?php echo number_format($c['total'], 2); ?></td>
                        <td class="text-end">
                            <a href="editar_compra.php?id=<?php echo $c['id']; ?>" class="action-btn" title="Editar">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Sin compras registradas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>
