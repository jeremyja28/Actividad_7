<?php
require_once __DIR__ . '/../connect.php';

$cod_rol = isset($_POST['cod_rol']) ? (int)$_POST['cod_rol'] : 0;
$descripcion = $_POST['descripcion'] ?? '';

$success = false;
$message = "";

if ($cod_rol <= 0 || $descripcion === '') {
    $message = "Datos inválidos.";
} else {
    $stmt = $conn->prepare("UPDATE cod_rol SET descripcion=? WHERE cod_rol=?");
    $stmt->bind_param('si', $descripcion, $cod_rol);

    if ($stmt->execute()) {
        $success = true;
        $message = "Rol actualizado correctamente.";
    } else {
        $message = "Error al actualizar: " . $conn->error;
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
    <div class="card shadow p-4 text-center" style="max-width: 500px; width: 100%;">
        <div class="card-body">
            <?php if ($success): ?>
                <h1 class="display-1 text-success mb-3"><i class="bi bi-check-circle"></i></h1>
                <h3 class="card-title text-success">¡Éxito!</h3>
                <p class="card-text lead"><?php echo $message; ?></p>
                <div class="d-grid gap-2">
                    <a href="listar_rol.php" class="btn btn-primary">Volver a la lista</a>
                    <a href="../index.php" class="btn btn-outline-secondary">Ir al Inicio</a>
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
