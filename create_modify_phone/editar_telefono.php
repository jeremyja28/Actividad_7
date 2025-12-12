<?php
include("../connect.php");
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$telefono = null;
if ($id > 0) {
    $sql = "SELECT * FROM productos WHERE id=$id";
    $r = $conn->query($sql);
    if ($r && $r->num_rows === 1) {
        $telefono = $r->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Teléfono</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color: #0d6efd; color: white; border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h3>Editar Teléfono</h3>
        </div>
        <div class="card-body">
            <?php if ($telefono): ?>
            <form action="actualizar_telefono.php" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $telefono['id']; ?>">
                
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre / Modelo:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($telefono['nombre']); ?>" required maxlength="150">
                    <div class="invalid-feedback">Por favor ingrese el nombre.</div>
                </div>

                <div class="mb-3">
                    <label for="marca_id" class="form-label">Marca:</label>
                    <select class="form-select" id="marca_id" name="marca_id" required>
                        <?php
                        $marcas = $conn->query("SELECT id, nombre FROM marcas ORDER BY nombre");
                        while ($m = $marcas->fetch_assoc()) {
                            $sel = $m['id'] == $telefono['marca_id'] ? 'selected' : '';
                            echo '<option value="'.$m['id'].'" '.$sel.'>'.htmlspecialchars($m['nombre']).'</option>';
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Por favor seleccione una marca.</div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="1000"><?php echo htmlspecialchars($telefono['descripcion']); ?></textarea>
                    <div class="form-text">Máximo 1000 caracteres.</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar_telefono.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-danger">No se encontró el teléfono.</div>
                <a href="listar_telefono.php" class="btn btn-primary">Volver a la lista</a>
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