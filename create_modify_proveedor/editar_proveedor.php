<?php
require_once '../security.php';
include("../connect.php");
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prov = null;
if ($id > 0) {
    $r = $conn->query("SELECT * FROM proveedores WHERE id=$id");
    if ($r && $r->num_rows === 1) { $prov = $r->fetch_assoc(); }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
        }
        body { background-color: #f8f9fa; }
        .navbar-brand { color: var(--primary-color) !important; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: var(--primary-color); color: white; border-radius: 15px 15px 0 0 !important; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: #4338ca; border-color: #4338ca; }
        .img-preview { max-width: 150px; margin-top: 10px; border-radius: 10px; border: 1px solid #ddd; padding: 5px; }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h3>Editar Proveedor</h3>
        </div>
        <div class="card-body">
            <?php if ($prov): ?>
            <form action="actualizar_proveedor.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate onsubmit="return validateForm()">
                <input type="hidden" name="id" value="<?php echo $prov['id']; ?>">
                
                <div class="mb-3">
                    <label for="nombres" class="form-label">Nombre Contacto</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($prov['nombres']); ?>" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="nombre_empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" value="<?php echo htmlspecialchars($prov['nombre_empresa']); ?>" required maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label">Logotipo (Dejar vacío para mantener el actual)</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                    <?php if (!empty($prov['ruta'])): ?>
                        <div class="mt-2">
                            <p>Logotipo actual:</p>
                            <img src="../<?php echo htmlspecialchars($prov['ruta']); ?>" alt="Logo actual" class="img-preview">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($prov['ciudad']); ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras" maxlength="50">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pais" class="form-label">País</label>
                        <input type="text" class="form-control" id="pais" name="pais" value="<?php echo htmlspecialchars($prov['pais']); ?>" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras" maxlength="50">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($prov['direccion']); ?>" maxlength="200">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($prov['email']); ?>" maxlength="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($prov['telefono']); ?>" pattern="\d{10}" maxlength="10" title="Debe tener 10 dígitos numéricos" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                        <div class="invalid-feedback">El teléfono debe tener exactamente 10 números.</div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar_proveedor.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Proveedor</button>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-danger">No se encontró el proveedor.</div>
                <a href="listar_proveedor.php" class="btn btn-primary">Volver a la lista</a>
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

    function validateForm() {
        const telefono = document.getElementById('telefono').value;
        if (telefono && !/^\d{10}$/.test(telefono)) {
            alert("El teléfono debe tener 10 dígitos numéricos.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>
<?php $conn->close(); ?>