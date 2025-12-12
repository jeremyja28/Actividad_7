<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: create_modify_user/login.php");
    exit();
}

require_once 'connect.php';

// Helper function to get count
function getCount($conn, $table) {
    $sql = "SELECT COUNT(*) as total FROM $table";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        return $row['total'];
    }
    return 0;
}

$count_productos = getCount($conn, 'productos');
$count_marcas = getCount($conn, 'marcas');
$count_variantes = getCount($conn, 'variantes');
$count_proveedores = getCount($conn, 'proveedores');
$count_compras = getCount($conn, 'compras');
$count_usuarios = getCount($conn, 'usuarios');
$count_roles = getCount($conn, 'cod_rol');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5; /* Indigo */
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
        
        /* Navbar Minimalista */
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
        
        /* Cards Elegantes */
        .stat-card {
            background-color: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.02);
            height: 100%;
            text-decoration: none;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-color);
        }

        .stat-card .content {
            position: relative;
            z-index: 2;
        }

        .card-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        
        .card-value {
            color: var(--text-main);
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .card-link {
            font-size: 0.875rem;
            color: var(--primary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Icono flotante con fondo suave */
        .icon-box {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            opacity: 0.9;
        }

        /* Variaciones de color suaves */
        .theme-blue .icon-box { background-color: #eff6ff; color: #3b82f6; }
        .theme-orange .icon-box { background-color: #fff7ed; color: #f97316; }
        .theme-purple .icon-box { background-color: #f5f3ff; color: #8b5cf6; }
        .theme-cyan .icon-box { background-color: #ecfeff; color: #06b6d4; }
        .theme-indigo .icon-box { background-color: #eef2ff; color: #6366f1; }
        .theme-pink .icon-box { background-color: #fdf2f8; color: #ec4899; }
        .theme-teal .icon-box { background-color: #f0fdfa; color: #14b8a6; }

        .page-header {
            margin-bottom: 2rem;
            margin-top: 2rem;
        }
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-main);
        }
        .page-subtitle {
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="bi bi-grid-1x2-fill"></i> InventoryOS
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <div class="fw-bold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></div>
                <div class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($_SESSION['correo']); ?></div>
            </div>
            <div class="dropdown">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; cursor: pointer;" data-bs-toggle="dropdown">
                    <i class="bi bi-person text-secondary"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger" href="create_modify_user/logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container px-4">
    <div class="page-header">
        <h1 class="page-title">Resumen General</h1>
        <p class="page-subtitle">Bienvenido al panel de control de inventario.</p>
    </div>
    
    <div class="row g-4">
        
        <!-- Compras -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_compra/listar_compra.php" class="stat-card theme-blue">
                <div class="content">
                    <div class="card-label">Transacciones</div>
                    <div class="card-value"><?php echo $count_compras; ?></div>
                    <div class="card-link">Ver Compras <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-cart3"></i>
                </div>
            </a>
        </div>

        <!-- Proveedores -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_proveedor/listar_proveedor.php" class="stat-card theme-orange">
                <div class="content">
                    <div class="card-label">Socios Comerciales</div>
                    <div class="card-value"><?php echo $count_proveedores; ?></div>
                    <div class="card-link">Ver Proveedores <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-truck"></i>
                </div>
            </a>
        </div>

        <!-- Productos -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_phone/listar_telefono.php" class="stat-card theme-purple">
                <div class="content">
                    <div class="card-label">Catálogo</div>
                    <div class="card-value"><?php echo $count_productos; ?></div>
                    <div class="card-link">Ver Teléfonos <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-phone"></i>
                </div>
            </a>
        </div>

        <!-- Variantes -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_variante/listar_variante.php" class="stat-card theme-cyan">
                <div class="content">
                    <div class="card-label">Stock / SKUs</div>
                    <div class="card-value"><?php echo $count_variantes; ?></div>
                    <div class="card-link">Ver Variantes <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-tags"></i>
                </div>
            </a>
        </div>

        <!-- Marcas -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_marca/listar_marca.php" class="stat-card theme-indigo">
                <div class="content">
                    <div class="card-label">Fabricantes</div>
                    <div class="card-value"><?php echo $count_marcas; ?></div>
                    <div class="card-link">Ver Marcas <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-award"></i>
                </div>
            </a>
        </div>

        <!-- Usuarios -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_user/listar.php" class="stat-card theme-pink">
                <div class="content">
                    <div class="card-label">Acceso</div>
                    <div class="card-value"><?php echo $count_usuarios; ?></div>
                    <div class="card-link">Gestionar Usuarios <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-people"></i>
                </div>
            </a>
        </div>

        <!-- Roles -->
        <div class="col-md-6 col-lg-3">
            <a href="create_modify_rol/listar_rol.php" class="stat-card theme-teal">
                <div class="content">
                    <div class="card-label">Permisos</div>
                    <div class="card-value"><?php echo $count_roles; ?></div>
                    <div class="card-link">Configurar Roles <i class="bi bi-arrow-right"></i></div>
                </div>
                <div class="icon-box">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
