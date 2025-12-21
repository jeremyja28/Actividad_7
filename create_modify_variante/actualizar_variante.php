<?php
require_once '../security.php';
include("../connect.php");

$id = (int)($_POST['id'] ?? 0);
$producto_id = (int)($_POST['producto_id'] ?? 0);
$sku = $_POST['sku'] ?? '';
$precio = $_POST['precio'] ?? '';
$color_id = $_POST['color_id'] !== '' ? (int)$_POST['color_id'] : null;
$capacidad_id = $_POST['capacidad_id'] !== '' ? (int)$_POST['capacidad_id'] : null;
$modelo_id = $_POST['modelo_id'] !== '' ? (int)$_POST['modelo_id'] : null;

$success = false;
$message = "";

if ($id <= 0 || $producto_id <= 0 || $sku === '' || $precio === '') {
    $message = "Datos inválidos.";
} else {
    $stmt = $conn->prepare("UPDATE variantes SET producto_id=?, sku=?, precio=?, color_id=?, capacidad_id=?, modelo_id=? WHERE id=?");
    $stmt->bind_param('isdiiii', $producto_id, $sku, $precio, $color_id, $capacidad_id, $modelo_id, $id);
    if ($stmt->execute()) {
        $success = true;
        $message = "Variante actualizada correctamente.";
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
                    <a href="listar_variante.php" class="btn btn-primary">Volver a la lista</a>
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
