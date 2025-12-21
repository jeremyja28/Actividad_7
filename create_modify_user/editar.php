<?php
require_once '../security.php';
include '../connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$fila = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $fila = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-top: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(45deg, #4e54c8, #8f94fb);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header text-center">
            <h3 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h3>
        </div>
        <div class="card-body p-4">
            <?php if ($fila): ?>
            <form action="actualizar.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo htmlspecialchars($fila['cedula']); ?>" required pattern="[0-9]+" title="Solo números">
                    <div class="invalid-feedback">Ingrese la cédula (solo números).</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras">
                        <div class="invalid-feedback">Ingrese el nombre.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($fila['apellido']); ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras">
                        <div class="invalid-feedback">Ingrese el apellido.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($fila['correo']); ?>" required>
                    <div class="invalid-feedback">Ingrese un correo válido.</div>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($fila['telefono']); ?>" required pattern="[0-9]+" title="Solo números">
                    <div class="invalid-feedback">Ingrese el teléfono.</div>
                </div>

                <div class="mb-3">
                    <label for="clave" class="form-label">Contraseña (Dejar en blanco para no cambiar):</label>
                    <input type="password" class="form-control" id="clave" name="clave">
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo" <?php if ($fila['estado'] == 'activo') echo 'selected'; ?>>Activo</option>
                        <option value="inactivo" <?php if ($fila['estado'] == 'inactivo') echo 'selected'; ?>>Inactivo</option>
                    </select>
                    <div class="invalid-feedback">Seleccione el estado.</div>
                </div>

                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol:</label>
                    <select class="form-select" id="rol_id" name="rol_id" required>
                        <option value="">-- Seleccione --</option>
                        <?php
                        $roles = $conn->query("SELECT cod_rol, descripcion FROM cod_rol ORDER BY descripcion");
                        while ($r = $roles->fetch_assoc()) {
                            $sel = ($r['cod_rol'] == $fila['rol_id']) ? 'selected' : '';
                            echo '<option value="'.$r['cod_rol'].'" '.$sel.'>'.htmlspecialchars($r['descripcion']).'</option>';
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Seleccione un rol.</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-danger" role="alert">
                No se encontró el usuario.
            </div>
            <div class="text-center">
                <a href="listar.php" class="btn btn-primary">Volver al listado</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
</body>
</html>
<?php $conn->close(); ?>