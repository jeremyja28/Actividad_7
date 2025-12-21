<?php
// Determinar rutas para los enlaces del navbar
if (file_exists('connect.php')) {
    // Estamos en la raíz
    $home_link = 'principal.php';
    $logout_link = 'create_modify_user/logout.php';
} else {
    // Estamos en una subcarpeta
    $home_link = '../principal.php';
    $logout_link = '../create_modify_user/logout.php';
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