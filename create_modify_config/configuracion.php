<?php
session_start();
require_once '../connect.php';

$mensaje = "";
$tipo_mensaje = "";

// Verificar si el usuario es administrador (opcional, pero recomendado)
// if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
//     header("Location: ../index.php");
//     exit();
// }

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usar_whatsapp = $_POST['usar_whatsapp'];
    
    // Validar entrada (0 o 1)
    if ($usar_whatsapp === '0' || $usar_whatsapp === '1') {
        $stmt = $conn->prepare("UPDATE configuracion SET valor = ? WHERE clave = 'usar_whatsapp'");
        $stmt->bind_param("s", $usar_whatsapp);
        
        if ($stmt->execute()) {
            $mensaje = "Configuración actualizada correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al actualizar: " . $conn->error;
            $tipo_mensaje = "danger";
        }
        $stmt->close();
    }
}

// Obtener configuración actual
$sql = "SELECT valor FROM configuracion WHERE clave = 'usar_whatsapp'";
$result = $conn->query($sql);
$config_actual = '0'; // Valor por defecto

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $config_actual = $row['valor'];
} else {
    // Si no existe, insertar por defecto
    $conn->query("INSERT INTO configuracion (clave, valor) VALUES ('usar_whatsapp', '0')");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-gear-fill"></i> Configuración de WhatsApp</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Uso de API de WhatsApp para Registro</label>
                                <div class="card p-3 bg-light">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="usar_whatsapp" id="whatsapp_on" value="1" <?php echo ($config_actual === '1') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="whatsapp_on">
                                            <i class="bi bi-whatsapp text-success"></i> Activado (Enviar OTP por WhatsApp)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usar_whatsapp" id="whatsapp_off" value="0" <?php echo ($config_actual === '0') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="whatsapp_off">
                                            <i class="bi bi-slash-circle text-danger"></i> Desactivado (Registro directo)
                                        </label>
                                    </div>
                                </div>
                                <div class="form-text text-muted">
                                    Si está desactivado, los usuarios se registrarán directamente sin verificación por WhatsApp.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Guardar Cambios
                                </button>
                                <a href="../index.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver al Inicio
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
