<?php include("../connect.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #0d6efd; color: white; border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h3>Agregar Nuevo Proveedor</h3>
        </div>
        <div class="card-body">
            <form action="guardar_proveedor.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate onsubmit="return validateForm()">
                
                <div class="mb-3">
                    <label for="nombres" class="form-label">Nombre Contacto</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" maxlength="100">
                    <div class="invalid-feedback">Por favor ingrese un nombre válido (solo letras).</div>
                </div>

                <div class="mb-3">
                    <label for="nombre_empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" required maxlength="100">
                </div>

                <div class="mb-3">
                    <label for="logo" class="form-label">Logotipo</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                    <div class="invalid-feedback">Por favor seleccione una imagen.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ciudad" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras" maxlength="50">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pais" class="form-label">País</label>
                        <input type="text" class="form-control" id="pais" name="pais" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras" maxlength="50">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" maxlength="200">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" pattern="\d{10}" maxlength="10" title="Debe tener 10 dígitos numéricos" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                        <div class="invalid-feedback">El teléfono debe tener exactamente 10 números.</div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar_proveedor.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Bootstrap validation script
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
        // Custom validations if needed beyond HTML5
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