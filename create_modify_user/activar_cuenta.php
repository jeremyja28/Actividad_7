<?php
session_start();
require_once '../connect.php';

$mensaje = "";
$tipo_mensaje = "";

// Verificar si hay datos en sesión
if (!isset($_SESSION['temp_user'])) {
    header("Location: registro.php");
    exit();
}

$temp_user = $_SESSION['temp_user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_ingresado = $_POST['otp'];

    // Verificar OTP y expiración
    if ($otp_ingresado == $temp_user['otp']) {
        if (strtotime($temp_user['expiracion']) > time()) {
            // OTP válido y no expirado -> Insertar en BD
            
            $sql = "INSERT INTO usuarios (cedula, nombre, apellido, correo, telefono, clave, rol_id, pregunta_1_id, respuesta_1, pregunta_2_id, respuesta_2, pregunta_3_id, respuesta_3, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssssssiisisss", 
                    $temp_user['cedula'], 
                    $temp_user['nombre'], 
                    $temp_user['apellido'], 
                    $temp_user['correo'], 
                    $temp_user['telefono'], 
                    $temp_user['clave'], 
                    $temp_user['rol_id'], 
                    $temp_user['pregunta1'], 
                    $temp_user['respuesta1'], 
                    $temp_user['pregunta2'], 
                    $temp_user['respuesta2'], 
                    $temp_user['pregunta3'], 
                    $temp_user['respuesta3']
                );
                
                if ($stmt->execute()) {
                    // Limpiar sesión
                    unset($_SESSION['temp_user']);
                    
                    $mensaje = "¡Cuenta activada con éxito! Redirigiendo al login...";
                    $tipo_mensaje = "success";
                    header("refresh:2;url=login.php");
                } else {
                    $mensaje = "Error al guardar el usuario: " . $conn->error;
                    $tipo_mensaje = "danger";
                }
                $stmt->close();
            } else {
                $mensaje = "Error en la base de datos: " . $conn->error;
                $tipo_mensaje = "danger";
            }
        } else {
            $mensaje = "El código OTP ha expirado. Por favor regístrese nuevamente.";
            $tipo_mensaje = "warning";
        }
    } else {
        $mensaje = "Código OTP incorrecto.";
        $tipo_mensaje = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activar Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background: transparent;
            border-bottom: none;
            padding-top: 2rem;
            text-align: center;
        }
        .otp-input {
            letter-spacing: 0.5rem;
            font-size: 1.5rem;
            text-align: center;
            font-weight: bold;
        }
        .btn-primary {
            background: #fda085;
            border: none;
            padding: 0.8rem;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #f6d365;
            color: #333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header">
                        <div class="mb-3">
                            <i class="bi bi-shield-check display-1 text-warning"></i>
                        </div>
                        <h3 class="mb-0 fw-bold text-secondary">Activar Cuenta</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> d-flex align-items-center" role="alert">
                                <?php if($tipo_mensaje == 'success'): ?>
                                    <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                                <?php else: ?>
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                                <?php endif; ?>
                                <div><?php echo $mensaje; ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($tipo_mensaje != 'success'): ?>
                        <p class="text-center text-muted mb-4">
                            Hemos enviado un código de 6 dígitos a tu WhatsApp.<br>
                            <small>Ingrésalo abajo para verificar tu identidad.</small>
                        </p>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">CÉDULA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-vcard"></i></span>
                                    <input type="text" name="cedula" class="form-control bg-light border-start-0" value="<?php echo htmlspecialchars($temp_user['cedula']); ?>" readonly required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small text-muted fw-bold">CÓDIGO OTP</label>
                                <input type="text" name="otp" class="form-control otp-input" placeholder="000000" required maxlength="6" autocomplete="off">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    ACTIVAR AHORA <i class="bi bi-arrow-right-circle ms-2"></i>
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
