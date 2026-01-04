<?php
session_start();
require_once '../connect.php';
require_once 'funciones_auth.php';

$step = 1;
$mensaje = "";
$tipo_mensaje = "";
$cedula_recuperacion = "";
$metodo_recuperacion = ""; // 'whatsapp' o 'preguntas'
$preguntas_usuario = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // PASO 1: Buscar usuario
    if (isset($_POST['buscar_usuario'])) {
        $usuario = $_POST['usuario'];
        
        $stmt = $conn->prepare("SELECT id, cedula, telefono, pregunta_1_id, pregunta_2_id, pregunta_3_id FROM usuarios WHERE cedula = ? OR correo = ?");
        $stmt->bind_param("ss", $usuario, $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cedula_recuperacion = $row['cedula'];
            $_SESSION['recuperacion_usuario'] = $row; // Guardar datos en sesión
            $step = 2; // Ir a selección de método
        } else {
            $mensaje = "Usuario no encontrado.";
            $tipo_mensaje = "danger";
        }
    } 
    
    // PASO 2: Selección de método
    elseif (isset($_POST['seleccionar_metodo'])) {
        $metodo = $_POST['metodo'];
        $row = $_SESSION['recuperacion_usuario'];
        $cedula_recuperacion = $row['cedula'];
        
        if ($metodo == 'whatsapp') {
            // Generar OTP y enviar
            $otp = rand(100000, 999999);
            $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            
            $update = $conn->prepare("UPDATE usuarios SET token_otp = ?, token_expiracion = ? WHERE id = ?");
            $update->bind_param("ssi", $otp, $expiracion, $row['id']);
            $update->execute();
            
            $mensaje_wsp = "Su codigo de recuperacion es: $otp";
            if (enviarWhatsApp($row['telefono'], $mensaje_wsp)) {
                $mensaje = "Código enviado a su WhatsApp.";
                $tipo_mensaje = "success";
                $step = 3;
                $metodo_recuperacion = 'whatsapp';
            } else {
                $mensaje = "Error al enviar WhatsApp. Intente con preguntas de seguridad.";
                $tipo_mensaje = "danger";
                $step = 2;
            }
        } elseif ($metodo == 'preguntas') {
            $step = 3;
            $metodo_recuperacion = 'preguntas';
            // Preparar preguntas para mostrar
            $preguntas_usuario = [
                1 => $bancoPreguntas[1][$row['pregunta_1_id']],
                2 => $bancoPreguntas[2][$row['pregunta_2_id']],
                3 => $bancoPreguntas[3][$row['pregunta_3_id']]
            ];
        }
    }
    
    // PASO 3: Verificar y Cambiar Clave
    elseif (isset($_POST['cambiar_clave'])) {
        $metodo_recuperacion = $_POST['metodo_recuperacion'];
        $row = $_SESSION['recuperacion_usuario'];
        $cedula_recuperacion = $row['cedula'];
        
        $nueva_clave = $_POST['nueva_clave'];
        $confirmar_clave = $_POST['confirmar_clave'];
        
        if ($nueva_clave !== $confirmar_clave) {
            $mensaje = "Las contraseñas no coinciden.";
            $tipo_mensaje = "danger";
            $step = 3;
            // Restaurar estado para volver a mostrar formulario
            if ($metodo_recuperacion == 'preguntas') {
                $preguntas_usuario = [
                    1 => $bancoPreguntas[1][$row['pregunta_1_id']],
                    2 => $bancoPreguntas[2][$row['pregunta_2_id']],
                    3 => $bancoPreguntas[3][$row['pregunta_3_id']]
                ];
            }
        } else {
            $verificado = false;
            
            if ($metodo_recuperacion == 'whatsapp') {
                $otp = $_POST['otp'];
                $stmt = $conn->prepare("SELECT id, token_expiracion FROM usuarios WHERE id = ? AND token_otp = ?");
                $stmt->bind_param("is", $row['id'], $otp);
                $stmt->execute();
                $res = $stmt->get_result();
                
                if ($res->num_rows > 0) {
                    $u = $res->fetch_assoc();
                    if (date("Y-m-d H:i:s") <= $u['token_expiracion']) {
                        $verificado = true;
                    } else {
                        $mensaje = "El código ha expirado.";
                    }
                } else {
                    $mensaje = "Código incorrecto.";
                }
            } elseif ($metodo_recuperacion == 'preguntas') {
                // Normalizamos a minúsculas y sin espacios para comparar
                $resp1 = md5(mb_strtolower(trim($_POST['resp1']), 'UTF-8'));
                $resp2 = md5(mb_strtolower(trim($_POST['resp2']), 'UTF-8'));
                $resp3 = md5(mb_strtolower(trim($_POST['resp3']), 'UTF-8'));
                
                $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND respuesta_1 = ? AND respuesta_2 = ? AND respuesta_3 = ?");
                $stmt->bind_param("isss", $row['id'], $resp1, $resp2, $resp3);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $verificado = true;
                } else {
                    $mensaje = "Una o más respuestas son incorrectas.";
                    $preguntas_usuario = [
                        1 => $bancoPreguntas[1][$row['pregunta_1_id']],
                        2 => $bancoPreguntas[2][$row['pregunta_2_id']],
                        3 => $bancoPreguntas[3][$row['pregunta_3_id']]
                    ];
                }
            }
            
            if ($verificado) {
                $clave_md5 = md5($nueva_clave);
                $update = $conn->prepare("UPDATE usuarios SET clave = ?, token_otp = NULL, token_expiracion = NULL WHERE id = ?");
                $update->bind_param("si", $clave_md5, $row['id']);
                
                if ($update->execute()) {
                    $mensaje = "Contraseña actualizada correctamente. <a href='login.php'>Iniciar Sesión</a>";
                    $tipo_mensaje = "success";
                    $step = 4; // Finalizado
                    unset($_SESSION['recuperacion_usuario']);
                } else {
                    $mensaje = "Error al actualizar la contraseña.";
                    $tipo_mensaje = "danger";
                }
            } else {
                $tipo_mensaje = "danger";
                $step = 3;
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
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
        }
        body {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 1rem;
            position: relative;
        }
        .step.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 0.3rem rgba(79, 70, 229, 0.25);
        }
        .step.completed {
            background: #10b981;
            color: white;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 2rem;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
        }
        .step:last-child::after {
            display: none;
        }
        .btn-warning {
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header">
                        <h3 class="mb-0 fw-bold text-secondary">Recuperar Acceso</h3>
                        <p class="text-muted small">Sigue los pasos para restablecer tu contraseña</p>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : ''; ?>">1</div>
                            <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : ''; ?>">2</div>
                            <div class="step <?php echo $step >= 3 ? ($step > 3 ? 'completed' : 'active') : ''; ?>">3</div>
                            <div class="step <?php echo $step == 4 ? 'completed' : ''; ?>"><i class="bi bi-check"></i></div>
                        </div>

                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div><?php echo $mensaje; ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step == 1): ?>
                            <!-- PASO 1: Identificación -->
                            <form method="POST">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary">Identifícate</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" name="usuario" class="form-control" placeholder="Ingresa tu Cédula o Correo" required>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="buscar_usuario" class="btn btn-info text-white fw-bold shadow-sm">
                                        CONTINUAR <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>

                        <?php elseif ($step == 2): ?>
                            <!-- PASO 2: Selección de Método -->
                            <h5 class="text-center mb-4">Selecciona un método de recuperación</h5>
                            <div class="d-grid gap-3">
                                <form method="POST">
                                    <input type="hidden" name="metodo" value="whatsapp">
                                    <button type="submit" name="seleccionar_metodo" class="btn btn-outline-success p-3 text-start w-100">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-whatsapp fs-2 me-3"></i>
                                            <div>
                                                <div class="fw-bold">Enviar código por WhatsApp</div>
                                                <small class="text-muted">Recibirás un código de 6 dígitos</small>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                                
                                <form method="POST">
                                    <input type="hidden" name="metodo" value="preguntas">
                                    <button type="submit" name="seleccionar_metodo" class="btn btn-outline-primary p-3 text-start w-100">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-question-circle fs-2 me-3"></i>
                                            <div>
                                                <div class="fw-bold">Responder Preguntas de Seguridad</div>
                                                <small class="text-muted">Responde las 3 preguntas que elegiste</small>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            </div>

                        <?php elseif ($step == 3): ?>
                            <!-- PASO 3: Verificación y Cambio de Clave -->
                            <form method="POST">
                                <input type="hidden" name="metodo_recuperacion" value="<?php echo $metodo_recuperacion; ?>">
                                
                                <?php if ($metodo_recuperacion == 'whatsapp'): ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-secondary">Código de Verificación (WhatsApp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
                                            <input type="text" name="otp" class="form-control" required placeholder="6 dígitos" maxlength="6">
                                        </div>
                                    </div>
                                <?php elseif ($metodo_recuperacion == 'preguntas'): ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-secondary">1. <?php echo $preguntas_usuario[1]; ?></label>
                                        <input type="text" name="resp1" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-secondary">2. <?php echo $preguntas_usuario[2]; ?></label>
                                        <input type="text" name="resp2" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-secondary">3. <?php echo $preguntas_usuario[3]; ?></label>
                                        <input type="text" name="resp3" class="form-control" required>
                                    </div>
                                <?php endif; ?>

                                <hr class="my-4">
                                <h5 class="mb-3">Establecer Nueva Contraseña</h5>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                        <input type="password" name="nueva_clave" class="form-control" required placeholder="********">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                                        <input type="password" name="confirmar_clave" class="form-control" required placeholder="********">
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="cambiar_clave" class="btn btn-success fw-bold shadow-sm">
                                        CAMBIAR CONTRASEÑA <i class="bi bi-check-lg ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="mt-4 text-center border-top pt-3">
                            <a href="login.php" class="text-decoration-none text-secondary">
                                <i class="bi bi-arrow-left"></i> Volver al Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
