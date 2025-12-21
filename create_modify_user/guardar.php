<?php
require_once '../security.php';
require_once __DIR__ . '/../connect.php';

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
$link_back = "agregar.php";

if ($cedula === '' || $nombre === '' || $apellido === '' || $correo === '' || $clave === '' || $estado === '' || $rol_id === '') {
    $message = "Datos incompletos.";
} else {
    $clave_md5 = md5($clave);
    $stmt = $conn->prepare("INSERT INTO usuarios (cedula, nombre, apellido, correo, telefono, clave, estado, rol_id) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssi', $cedula, $nombre, $apellido, $correo, $telefono, $clave_md5, $estado, $rol_id);

    if ($stmt->execute()) {
        $success = true;
        $message = "Usuario guardado correctamente.";
    } else {
        $message = "Error al guardar: " . $conn->error;
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
                    <a href="<?php echo $link_back; ?>" class="btn btn-primary">Agregar otro</a>
                    <a href="listar.php" class="btn btn-outline-primary">Ver Lista</a>
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
