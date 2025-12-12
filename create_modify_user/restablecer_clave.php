<?php
include("../connect.php");

$mensaje = "";
$tipo_mensaje = "";
$usuario_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $otp = $_POST['otp'];
    $nueva_clave = $_POST['nueva_clave'];
    
    $stmt = $conn->prepare("SELECT id, token_expiracion FROM usuarios WHERE id = ? AND token_otp = ?");
    $stmt->bind_param("is", $usuario_id, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (strtotime($row['token_expiracion']) > time()) {
            $clave_md5 = md5($nueva_clave);
            
            $update = $conn->prepare("UPDATE usuarios SET clave = ?, token_otp = NULL, token_expiracion = NULL WHERE id = ?");
            $update->bind_param("si", $clave_md5, $usuario_id);
            
            if ($update->execute()) {
                $mensaje = "Contraseña actualizada correctamente. Redirigiendo al login...";
                $tipo_mensaje = "success";
                header("refresh:2;url=login.php");
            } else {
                $mensaje = "Error al actualizar contraseña.";
                $tipo_mensaje = "danger";
            }
        } else {
            $mensaje = "El código OTP ha expirado.";
            $tipo_mensaje = "danger";
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
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Restablecer Contraseña</div>
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($tipo_mensaje != 'success'): ?>
                    <form method="POST">
                        <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
                        <div class="mb-3">
                            <label>Código OTP (Enviado a WhatsApp)</label>
                            <input type="text" name="otp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nueva Contraseña</label>
                            <input type="password" name="nueva_clave" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Cambiar Contraseña</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
