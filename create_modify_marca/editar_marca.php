<?php
include("../connect.php");
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$m = null;
if ($id>0){
  $r = $conn->query("SELECT * FROM marcas WHERE id=$id");
  if ($r && $r->num_rows===1){ $m = $r->fetch_assoc(); }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Marca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 500px; margin-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #0d6efd; color: white; border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h3>Editar Marca</h3>
        </div>
        <div class="card-body">
            <?php if ($m): ?>
            <form action="actualizar_marca.php" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Marca:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($m['nombre']); ?>" required maxlength="100">
                    <div class="invalid-feedback">Ingrese el nombre de la marca.</div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar_marca.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Marca</button>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-danger" role="alert">
                No se encontró la marca.
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-center mt-3">
        <a href="../index.php" class="text-decoration-none">Volver al índice</a>
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