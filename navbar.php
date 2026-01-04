<?php
// Determinar rutas para los enlaces del navbar
if (file_exists('connect.php')) {
    // Estamos en la raíz
    $home_link = 'principal.php';
    $alertas_link = 'ver_alertas.php';
    $logout_link = 'create_modify_user/logout.php';
} else {
    // Estamos en una subcarpeta
    $home_link = '../principal.php';
    $alertas_link = '../ver_alertas.php';
    $logout_link = '../create_modify_user/logout.php';
}

// Contar alertas si hay conexión
$alert_count = 0;
if(isset($conn)) {
    $sql_alert = "SELECT COUNT(*) as total FROM (
        SELECT v.id, v.stock_minimo, 
        COALESCE((SELECT SUM(cantidad) FROM compras WHERE variante_id = v.id), 0) as stock_actual
        FROM variantes v
        HAVING stock_actual <= stock_minimo
    ) as alertas";
    $res_a = $conn->query($sql_alert);
    if($res_a && $row_a = $res_a->fetch_assoc()) {
        $alert_count = $row_a['total'];
    }

    // Obtener vista previa de alertas (Top 3 con menos stock)
    $preview_alerts = [];
    if($alert_count > 0) {
        $sql_preview = "SELECT v.id, p.nombre, v.sku, v.stock_minimo, 
            COALESCE((SELECT SUM(cantidad) FROM compras WHERE variante_id = v.id), 0) as stock_actual
            FROM variantes v
            JOIN productos p ON p.id = v.producto_id
            HAVING stock_actual <= stock_minimo
            ORDER BY stock_actual ASC
            LIMIT 3";
        $res_p = $conn->query($sql_preview);
        if($res_p) {
            while($row_p = $res_p->fetch_assoc()) {
                $preview_alerts[] = $row_p;
            }
        }
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary" href="<?php echo $home_link; ?>">
            <i class="bi bi-grid-1x2-fill"></i> InventoryOS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Espacio para menú futuro -->
            </ul>
            <div class="d-flex align-items-center gap-3">
                
                <!-- Notificación de Alertas (Dropdown) -->
                <div class="dropdown">
                    <a href="#" class="position-relative text-dark me-2" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if($alert_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $alert_count; ?>
                            <span class="visually-hidden">alertas</span>
                        </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="alertsDropdown" style="width: 320px;">
                        <li><h6 class="dropdown-header text-uppercase fw-bold text-primary">Alertas de Stock</h6></li>
                        <?php if(!empty($preview_alerts)): ?>
                            <?php foreach($preview_alerts as $alert): ?>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="<?php echo $alertas_link; ?>">
                                        <div class="me-2 overflow-hidden">
                                            <div class="fw-bold text-truncate"><?php echo htmlspecialchars($alert['nombre']); ?></div>
                                            <small class="text-muted" style="font-size: 0.75rem;">SKU: <?php echo $alert['sku']; ?></small>
                                        </div>
                                        <div class="text-end">
                                            <?php if($alert['stock_actual'] == 0): ?>
                                                <span class="badge bg-danger">Agotado</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo $alert['stock_actual']; ?> / <?php echo $alert['stock_minimo']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center text-primary fw-bold py-2" href="<?php echo $alertas_link; ?>">
                                    Ver todas las alertas <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li><div class="dropdown-item text-center text-muted py-3">No hay alertas pendientes</div></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="text-end d-none d-md-block">
                    <div class="fw-bold small"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;">En línea</div>
                </div>
                <a href="<?php echo $logout_link; ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</nav>