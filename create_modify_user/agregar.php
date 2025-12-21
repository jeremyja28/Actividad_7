<?php 
require_once '../security.php';
require_once __DIR__ . '/../connect.php'; 

// Obtener roles
$roles = [];
$res = $conn->query("SELECT * FROM cod_rol");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $roles[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #0d6efd; color: white; border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h3>Agregar Usuario</h3>
        </div>
        <div class="card-body">
            <form action="guardar.php" method="post" class="needs-validation" novalidate>
                
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" required pattern="\d{10}" maxlength="10" title="Debe tener 10 dígitos numéricos" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    <div class="invalid-feedback">Ingrese una cédula válida (10 dígitos).</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="50">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required maxlength="50">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo:</label>
                    <input type="email" class="form-control" id="correo" name="correo" required maxlength="100">
                    <div class="invalid-feedback">Ingrese un correo válido.</div>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" required maxlength="20">
                </div>

                <div class="mb-3">
                    <label for="clave" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="clave" name="clave" required maxlength="32">
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="activo">Activo</option>
                        <option value="pendiente_activacion">Pendiente Activación</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="rol_id" class="form-label">Rol:</label>
                    <select class="form-select" id="rol_id" name="rol_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['cod_rol']; ?>"><?php echo $rol['descripcion']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
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