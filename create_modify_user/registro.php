<?php
session_start();
require_once '../connect.php';
require_once 'funciones_auth.php';

$mensaje = "";
$tipo_mensaje = "";

// Obtener roles disponibles
$roles_disponibles = [];
$sql_roles = "SELECT cod_rol, descripcion FROM cod_rol";
$res_roles = $conn->query($sql_roles);
if ($res_roles && $res_roles->num_rows > 0) {
    while ($r = $res_roles->fetch_assoc()) {
        $roles_disponibles[] = $r;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $clave = $_POST['clave'];
    $rol_id = isset($_POST['rol_id']) ? $_POST['rol_id'] : null; // Capturar Rol
    
    $pregunta1 = $_POST['pregunta1'];
    $respuesta1 = $_POST['respuesta1'];
    $pregunta2 = $_POST['pregunta2'];
    $respuesta2 = $_POST['respuesta2'];
    $pregunta3 = $_POST['pregunta3'];
    $respuesta3 = $_POST['respuesta3'];

    // Validaciones
    if (!validarCedulaEcuatoriana($cedula)) {
        $mensaje = "La cédula ingresada no es válida.";
        $tipo_mensaje = "danger";
    } elseif (!validarTelefono($telefono)) {
        $mensaje = "El teléfono debe tener 10 dígitos y empezar con 09.";
        $tipo_mensaje = "danger";
    } else {
        // Verificar duplicados
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE cedula = ? OR correo = ?");
        $stmt->bind_param("ss", $cedula, $correo);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $mensaje = "El usuario ya existe (cédula o correo duplicado).";
            $tipo_mensaje = "danger";
        } else {
            // Hashes (Normalizamos a minúsculas y sin espacios extra)
            $clave_md5 = md5($clave);
            $resp1_md5 = md5(mb_strtolower(trim($respuesta1), 'UTF-8'));
            $resp2_md5 = md5(mb_strtolower(trim($respuesta2), 'UTF-8'));
            $resp3_md5 = md5(mb_strtolower(trim($respuesta3), 'UTF-8'));
            
            // Verificar configuración de WhatsApp
            $sql_conf = "SELECT valor FROM configuracion WHERE clave = 'usar_whatsapp'";
            $res_conf = $conn->query($sql_conf);
            $usar_whatsapp = '0'; // Default off if not found
            if ($res_conf && $res_conf->num_rows > 0) {
                $row_conf = $res_conf->fetch_assoc();
                $usar_whatsapp = $row_conf['valor'];
            }

            if ($usar_whatsapp === '1') {
                // OTP
                $otp = rand(100000, 999999);
                $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
                
                // Guardar datos en sesión temporalmente
                $_SESSION['temp_user'] = [
                    'cedula' => $cedula,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'clave' => $clave_md5,
                    'rol_id' => $rol_id,
                    'pregunta1' => $pregunta1,
                    'respuesta1' => $resp1_md5,
                    'pregunta2' => $pregunta2,
                    'respuesta2' => $resp2_md5,
                    'pregunta3' => $pregunta3,
                    'respuesta3' => $resp3_md5,
                    'otp' => $otp,
                    'expiracion' => $expiracion
                ];

                // Enviar WhatsApp
                $mensaje_wsp = "Tu codigo de activacion es: $otp";
                enviarWhatsApp($telefono, $mensaje_wsp);
                
                header("Location: activar_cuenta.php");
                exit();
            } else {
                // Registro directo (usar_whatsapp = 0)
                $sql_insert = "INSERT INTO usuarios (cedula, nombre, apellido, correo, telefono, clave, rol_id, pregunta_1_id, respuesta_1, pregunta_2_id, respuesta_2, pregunta_3_id, respuesta_3, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
                
                $stmt_insert = $conn->prepare($sql_insert);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("ssssssiisisss", 
                        $cedula, 
                        $nombre, 
                        $apellido, 
                        $correo, 
                        $telefono, 
                        $clave_md5,
                        $rol_id, 
                        $pregunta1, 
                        $resp1_md5, 
                        $pregunta2, 
                        $resp2_md5, 
                        $pregunta3, 
                        $resp3_md5
                    );
                    
                    if ($stmt_insert->execute()) {
                        $mensaje = "¡Registro exitoso! Redirigiendo al login...";
                        $tipo_mensaje = "success";
                        header("refresh:2;url=login.php");
                    } else {
                        $mensaje = "Error al guardar el usuario: " . $conn->error;
                        $tipo_mensaje = "danger";
                    }
                    $stmt_insert->close();
                } else {
                    $mensaje = "Error en la base de datos: " . $conn->error;
                    $tipo_mensaje = "danger";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
        }
        body {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0 !important;
        }
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .btn-success {
            background: #28a745;
            border: none;
            padding: 0.8rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
            border-color: var(--primary-color);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        /* Fix for input group borders */
        .input-group .form-control:focus {
            z-index: 3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0 fw-bold" style="color: var(--primary-color);">Crear Nueva Cuenta</h3>
                        <p class="text-muted mb-0">Completa el formulario para registrarte</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                                <div><?php echo $mensaje; ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <h5 class="section-title"><i class="bi bi-person-vcard me-2"></i>Datos Personales</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small text-muted">Cédula</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-card-heading"></i></span>
                                        <input type="text" name="cedula" class="form-control border-start-0 ps-0" required maxlength="10" value="<?php echo isset($_POST['cedula']) ? $_POST['cedula'] : ''; ?>" placeholder="Ej: 1712345678">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Teléfono (WhatsApp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                        <input type="text" name="telefono" class="form-control border-start-0 ps-0" required maxlength="10" placeholder="09XXXXXXXX" value="<?php echo isset($_POST['telefono']) ? $_POST['telefono'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small text-muted">Nombre</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                                        <input type="text" name="nombre" class="form-control border-start-0 ps-0" required value="<?php echo isset($_POST['nombre']) ? $_POST['nombre'] : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Apellido</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                                        <input type="text" name="apellido" class="form-control border-start-0 ps-0" required value="<?php echo isset($_POST['apellido']) ? $_POST['apellido'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label small text-muted">Correo Electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="correo" class="form-control border-start-0 ps-0" required value="<?php echo isset($_POST['correo']) ? $_POST['correo'] : ''; ?>" placeholder="ejemplo@correo.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Rol de Usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-person-gear"></i></span>
                                        <select name="rol_id" class="form-select border-start-0 ps-0" required>
                                            <option value="">Seleccione un rol...</option>
                                            <?php foreach ($roles_disponibles as $rol): ?>
                                                <option value="<?php echo $rol['cod_rol']; ?>" <?php echo (isset($_POST['rol_id']) && $_POST['rol_id'] == $rol['cod_rol']) ? 'selected' : ''; ?>>
                                                    <?php echo $rol['descripcion']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label small text-muted">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="clave" class="form-control border-start-0 ps-0" required placeholder="********">
                                </div>
                            </div>
                            
                            <h5 class="section-title mt-5"><i class="bi bi-shield-lock me-2"></i>Preguntas de Seguridad</h5>
                            <p class="text-muted small mb-4">Estas preguntas nos ayudarán a verificar tu identidad si olvidas tu contraseña.</p>
                            
                            <!-- Grupo 1 -->
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <label class="form-label fw-bold text-secondary">Pregunta 1</label>
                                    <select name="pregunta1" class="form-select mb-2" required>
                                        <option value="">Seleccione una pregunta...</option>
                                        <?php foreach ($bancoPreguntas[1] as $id => $preg): ?>
                                            <option value="<?php echo $id; ?>"><?php echo $preg; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="respuesta1" class="form-control" placeholder="Tu respuesta" required>
                                </div>
                            </div>
                            
                            <!-- Grupo 2 -->
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <label class="form-label fw-bold text-secondary">Pregunta 2</label>
                                    <select name="pregunta2" class="form-select mb-2" required>
                                        <option value="">Seleccione una pregunta...</option>
                                        <?php foreach ($bancoPreguntas[2] as $id => $preg): ?>
                                            <option value="<?php echo $id; ?>"><?php echo $preg; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="respuesta2" class="form-control" placeholder="Tu respuesta" required>
                                </div>
                            </div>
                            
                            <!-- Grupo 3 -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <label class="form-label fw-bold text-secondary">Pregunta 3</label>
                                    <select name="pregunta3" class="form-select mb-2" required>
                                        <option value="">Seleccione una pregunta...</option>
                                        <?php foreach ($bancoPreguntas[3] as $id => $preg): ?>
                                            <option value="<?php echo $id; ?>"><?php echo $preg; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="respuesta3" class="form-control" placeholder="Tu respuesta" required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                    REGISTRARSE <i class="bi bi-check-circle-fill ms-2"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <p class="text-muted">¿Ya tienes una cuenta? <a href="login.php" class="fw-bold text-decoration-none" style="color: var(--primary-color);">Inicia Sesión aquí</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
