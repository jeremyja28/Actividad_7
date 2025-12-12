<?php
require_once __DIR__ . '/../connect.php';

// Consulta para obtener todos los registros (sin password)
$sql = "SELECT u.id, u.cedula, u.nombre, u.apellido, u.correo, u.estado, u.rol_id, r.descripcion AS rol
    FROM usuarios u LEFT JOIN cod_rol r ON r.cod_rol = u.rol_id";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | InventoryOS</title>
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
        .badge-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-activo { background-color: #dcfce7; color: #166534; }
        .status-inactivo { background-color: #fee2e2; color: #991b1b; }
        
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
        <h1 class="page-title">Usuarios</h1>
        <a href="agregar.php" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg"></i> Nuevo Usuario
        </a>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($fila = $resultado->fetch_assoc()) {
                            $statusClass = ($fila['estado'] == 'activo') ? 'status-activo' : 'status-inactivo';
                            echo "<tr>";
                            echo "<td class='fw-medium'>" . htmlspecialchars($fila['cedula']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['correo']) . "</td>";
                            echo "<td><span class='badge-status " . $statusClass . "'>" . ucfirst(htmlspecialchars($fila['estado'])) . "</span></td>";
                            echo "<td>" . htmlspecialchars($fila['rol'] ?? 'Sin Rol') . "</td>";
                            echo "<td class='text-end'>
                                    <a href='editar.php?id=" . $fila['id'] . "' class='action-btn' title='Editar'>
                                        <i class='bi bi-pencil-square fs-5'></i>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No hay usuarios registrados</td></tr>";
                    }
                    ?>  
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
