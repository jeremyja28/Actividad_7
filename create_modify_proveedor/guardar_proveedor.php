<?php
require_once '../security.php';
include("../connect.php");

$nombres = $_POST['nombres'] ?? '';
$nombre_empresa = $_POST['nombre_empresa'] ?? '';
$direccion = $_POST['direccion'] ?? null;
$ciudad = $_POST['ciudad'] ?? null;
$pais = $_POST['pais'] ?? null;
$email = $_POST['email'] ?? null;
$telefono = $_POST['telefono'] ?? null;

$success = false;
$message = "";

if ($nombres === '' || $nombre_empresa === '') {
    $message = "Datos obligatorios faltantes.";
} elseif ($telefono && !preg_match('/^\d{10}$/', $telefono)) {
    $message = "El teléfono debe tener 10 dígitos numéricos.";
} else {
    $ruta_imagen = null;
    $error_imagen = false;

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES['logo']['name']);
        $directorio_destino = "../img/proveedores/";
        
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        $ruta_final = $directorio_destino . $nombre_archivo;
        $tipo_archivo = strtolower(pathinfo($ruta_final, PATHINFO_EXTENSION));
        
        if (in_array($tipo_archivo, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_final)) {
                $ruta_imagen = "img/proveedores/" . $nombre_archivo;
            } else {
                $message = "Error al subir la imagen.";
                $error_imagen = true;
            }
        } else {
            $message = "Solo se permiten archivos de imagen.";
            $error_imagen = true;
        }
    }

    if (!$error_imagen) {
        $stmt = $conn->prepare("INSERT INTO proveedores (nombres, nombre_empresa, direccion, ciudad, pais, email, telefono, ruta) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssssssss', $nombres, $nombre_empresa, $direccion, $ciudad, $pais, $email, $telefono, $ruta_imagen);

        if ($stmt->execute()) {
            $success = true;
            $message = "Proveedor guardado correctamente.";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
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
                    <a href="agregar_proveedor.php" class="btn btn-primary">Agregar otro</a>
                    <a href="listar_proveedor.php" class="btn btn-outline-primary">Ver Lista</a>
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
