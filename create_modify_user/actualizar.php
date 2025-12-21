<?php
require_once '../security.php';
require_once __DIR__ . '/../connect.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$cedula = $_POST['cedula'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo = $_POST['correo'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$clave = $_POST['clave'] ?? '';
$estado = $_POST['estado'] ?? '';
$rol_id = $_POST['rol_id'] ?? '';

$success = false;
$message = "";

if ($id <= 0) {
    $message = "ID de usuario no válido.";
} else {
    // Check if password needs to be updated
    if (!empty($clave)) {
        $clave_hash = md5($clave);
        $sql = "UPDATE usuarios SET cedula=?, nombre=?, apellido=?, correo=?, telefono=?, clave=?, estado=?, rol_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $cedula, $nombre, $apellido, $correo, $telefono, $clave_hash, $estado, $rol_id, $id);
    } else {
        $sql = "UPDATE usuarios SET cedula=?, nombre=?, apellido=?, correo=?, telefono=?, estado=?, rol_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssii", $cedula, $nombre, $apellido, $correo, $telefono, $estado, $rol_id, $id);
    }

    try {
        if ($stmt->execute()) {
            $success = true;
            $message = "Usuario actualizado correctamente.";
        } else {
            $message = "Error al actualizar: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $message = "Error al actualizar: " . $e->getMessage();
    }
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
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
<?php include '../navbar.php'; ?>
    <div class="card shadow p-4 text-center" style="max-width: 500px; width: 100%; border-radius: 15px;">
        <div class="card-body">
            <?php if ($success): ?>
                <h1 class="display-1 text-success mb-3"><i class="bi bi-check-circle"></i></h1>
                <h3 class="card-title text-success">¡Éxito!</h3>
                <p class="card-text lead"><?php echo $message; ?></p>
                <div class="d-grid gap-2">
                    <a href="listar.php" class="btn btn-primary">Volver a la lista</a>
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
