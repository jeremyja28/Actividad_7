<?php
require_once '../security.php';
include("../connect.php");
$sql = "SELECT * FROM proveedores ORDER BY id DESC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores | InventoryOS</title>
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
        .provider-card {
            background-color: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.02);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .provider-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-color);
        }
        .provider-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        .provider-card:hover .provider-logo {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.05);
        }
        .provider-name {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.9);
            padding: 0.5rem;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        .provider-card:hover .provider-name {
            transform: translateY(0);
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container px-4">
    <div class="page-header">
        <h1 class="page-title">Proveedores</h1>
        <a href="agregar_proveedor.php" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg"></i> Nuevo Proveedor
        </a>
    </div>

    <div class="row g-4">
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($p = $res->fetch_assoc()): ?>
                <?php
                    $final_img = '';
                    
                    // 1. Prioridad: Ruta guardada en BD
                    if (!empty($p['ruta']) && file_exists(__DIR__ . '/../' . $p['ruta'])) {
                        $final_img = '../' . $p['ruta'];
                    }
                    
                    // 2. Fallback: Buscar por nombre (Legacy)
                    if (empty($final_img)) {
                        $name_clean = strtolower(trim($p['nombre_empresa']));
                        $name_clean_underscore = str_replace(' ', '_', $name_clean);
                        
                        $candidates = [
                            "img/proveedores/" . $name_clean_underscore . ".png",
                            "img/proveedores/" . $name_clean_underscore . ".jpg",
                            "img/proveedores/" . $name_clean . ".png",
                        ];

                        if (stripos($p['nombre_empresa'], 'Tech Global') !== false) {
                            array_unshift($candidates, "img/proveedores/TechGlobal_Supplies.png");
                        }
                        
                        foreach ($candidates as $cand) {
                            if (file_exists(__DIR__ . '/../' . $cand)) {
                                $final_img = '../' . $cand;
                                break;
                            }
                        }
                    }
                    
                    // 3. Placeholder
                    if (empty($final_img)) {
                        $final_img = 'https://via.placeholder.com/300x150?text=' . urlencode($p['nombre_empresa']);
                    }
                ?>
                <div class="col-md-4 col-lg-3">
                    <a href="editar_proveedor.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                        <div class="provider-card" title="<?php echo htmlspecialchars($p['nombre_empresa']); ?>">
                            <img src="<?php echo $final_img; ?>" alt="<?php echo htmlspecialchars($p['nombre_empresa']); ?>" class="provider-logo">
                            <div class="provider-name"><?php echo htmlspecialchars($p['nombre_empresa']); ?></div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">No hay proveedores registrados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
