<?php
session_start();
require_once '../connect.php';
require_once 'funciones_auth.php';

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario']; // Puede ser cédula o correo
    $clave = $_POST['clave'];
    $clave_md5 = md5($clave);

    // Buscar usuario
    $stmt = $conn->prepare("SELECT id, cedula, nombre, apellido, correo, telefono, estado FROM usuarios WHERE (cedula = ? OR correo = ?) AND clave = ?");
    $stmt->bind_param("sss", $usuario, $usuario, $clave_md5);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if ($row['estado'] == 'activo') {
            // Iniciar sesión
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'] . " " . $row['apellido'];
            $_SESSION['correo'] = $row['correo'];
            $_SESSION['last_activity'] = time(); // Inicializar tiempo de actividad
            
            header("Location: ../principal.php");
            exit();
        } elseif ($row['estado'] == 'pendiente_activacion') {
            // Cuenta pendiente: Generar nuevo OTP y guardar en sesión
            $otp = rand(100000, 999999);
            $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            
            // Guardar en sesión para activación
            $_SESSION['temp_user'] = [
                'cedula' => $row['cedula'],
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'correo' => $row['correo'],
                'telefono' => $row['telefono'],
                'otp' => $otp,
                'expiracion' => $expiracion,
                'from_login' => true,
                'user_id' => $row['id']
            ];
            
            // Enviar WhatsApp
            $mensaje_wsp = "Tu nuevo codigo de activacion es: $otp";
            enviarWhatsApp($row['telefono'], $mensaje_wsp);
            
            header("Location: activar_cuenta.php");
            exit();
        } else {
            $mensaje = "Su cuenta está desactivada. Contacte al administrador.";
            $tipo_mensaje = "warning";
        }
    } else {
        $mensaje = "Credenciales incorrectas.";
        $tipo_mensaje = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background: transparent;
            border-bottom: none;
            padding-top: 2rem;
            padding-bottom: 1rem;
        }
        .btn-primary {
            background: #764ba2;
            border: none;
            padding: 0.8rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #667eea;
            transform: translateY(-2px);
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
            border-color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle display-1 text-primary" style="color: #764ba2 !important;"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-secondary">Bienvenido</h4>
                        <p class="text-muted small">Ingresa tus credenciales para continuar</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> d-flex align-items-center" role="alert">
                                <?php if($tipo_mensaje == 'danger'): ?>
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php else: ?>
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                <?php endif; ?>
                                <div><?php echo $mensaje; ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="usuario" class="form-control" id="floatingInput" placeholder="Cédula o Correo" required>
                                <label for="floatingInput"><i class="bi bi-envelope me-1"></i> Usuario (Cédula o Correo)</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" name="clave" class="form-control" id="floatingPassword" placeholder="Contraseña" required>
                                <label for="floatingPassword"><i class="bi bi-lock me-1"></i> Contraseña</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-bold">
                                    INGRESAR <i class="bi bi-box-arrow-in-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center border-top pt-3">
                            <p class="mb-1"><a href="recuperar.php" class="text-decoration-none text-secondary small"><i class="bi bi-key"></i> ¿Olvidaste tu contraseña?</a></p>
                            <p class="mb-0"><a href="registro.php" class="text-decoration-none fw-bold" style="color: #764ba2;">Crear una cuenta nueva</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
