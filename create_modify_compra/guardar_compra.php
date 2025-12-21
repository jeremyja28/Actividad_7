<?php
require_once '../security.php';
require_once __DIR__ . '/../connect.php';

$variante_id = (int)($_POST['variante_id'] ?? 0);
$proveedor_id = (int)($_POST['proveedor_id'] ?? 0);
$precio_unitario = $_POST['precio_unitario'] ?? '';
$cantidad = (int)($_POST['cantidad'] ?? 0);
$fecha_compra = $_POST['fecha_compra'] ?? '';

$success = false;
$message = "";

if ($variante_id <= 0 || $proveedor_id <= 0 || $precio_unitario === '' || $cantidad <= 0) {
    $message = "Datos incompletos.";
} else {
    if ($fecha_compra === '') {
        $stmt = $conn->prepare("INSERT INTO compras (variante_id, proveedor_id, precio_unitario, cantidad) VALUES (?,?,?,?)");
        $stmt->bind_param('iidi', $variante_id, $proveedor_id, $precio_unitario, $cantidad);
    } else {
        $fecha_compra = str_replace('T', ' ', $fecha_compra) . ':00';
        $stmt = $conn->prepare("INSERT INTO compras (variante_id, proveedor_id, precio_unitario, cantidad, fecha_compra) VALUES (?,?,?,?,?)");
        $stmt->bind_param('iidis', $variante_id, $proveedor_id, $precio_unitario, $cantidad, $fecha_compra);
    }

    if ($stmt->execute()) {
        $success = true;
        $message = "Compra registrada correctamente.";
    } else {
        $message = "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
<?php include '../navbar.php'; ?>
    <div class="card shadow p-4 text-center" style="max-width: 500px; width: 100%;">
        <div class="card-body">
            <?php if ($success): ?>
                <h1 class="display-1 text-success mb-3"><i class="bi bi-check-circle"></i></h1>
                <h3 class="card-title text-success">¡Éxito!</h3>
                <p class="card-text lead"><?php echo $message; ?></p>
                <div class="d-grid gap-2">
                    <a href="agregar_compra.php" class="btn btn-primary">Registrar otra</a>
                    <a href="listar_compra.php" class="btn btn-outline-primary">Ver Compras</a>
                    <a href="../principal.php" class="btn btn-outline-secondary">Ir al Inicio</a>
                </div>
            <?php else: ?>
                <h1 class="display-1 text-danger mb-3"><i class="bi bi-x-circle"></i></h1>
                <h3 class="card-title text-danger">Error</h3>
                <p class="card-text"><?php echo $message; ?></p>
                <div class="d-grid gap-2">
                    <a href="javascript:history.back()" class="btn btn-secondary">Intentar de nuevo</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
