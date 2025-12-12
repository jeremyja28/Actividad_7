<?php
include("../connect.php");

$nombre = $_POST['nombre'] ?? '';
$marca_id = (int)($_POST['marca_id'] ?? 0);
$descripcion = $_POST['descripcion'] ?? null;

$success = false;
$message = "";

if ($nombre === '' || $marca_id === 0) {
    $message = "Datos incompletos.";
} else {
    $sql = "INSERT INTO productos (nombre, marca_id, descripcion) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sis', $nombre, $marca_id, $descripcion);

    if ($stmt->execute()) {
        $success = true;
        $message = "Teléfono (producto) creado correctamente.";
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
    <div class="card shadow p-4 text-center" style="max-width: 500px; width: 100%;">
        <div class="card-body">
            <?php if ($success): ?>
                <h1 class="display-1 text-success mb-3"><i class="bi bi-check-circle"></i></h1>
                <h3 class="card-title text-success">¡Éxito!</h3>
                <p class="card-text lead"><?php echo $message; ?></p>
                <div class="d-grid gap-2">
                    <a href="agregar_telefono.php" class="btn btn-primary">Agregar otro</a>
                    <a href="listar_telefono.php" class="btn btn-outline-primary">Ver Lista</a>
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
