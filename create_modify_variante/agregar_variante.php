<?php include("../connect.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Variante</title>
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
            <h3>Agregar Variante</h3>
        </div>
        <div class="card-body">
            <form action="guardar_variante.php" method="post" class="needs-validation" novalidate>
                
                <div class="mb-3">
                    <label for="producto_id" class="form-label">Producto:</label>
                    <select class="form-select" id="producto_id" name="producto_id" required>
                        <option value="">-- Seleccione --</option>
                        <?php $prod=$conn->query("SELECT id,nombre FROM productos ORDER BY nombre"); while($p=$prod->fetch_assoc()){ echo '<option value="'.$p['id'].'">'.htmlspecialchars($p['nombre']).'</option>'; } ?>
                    </select>
                    <div class="invalid-feedback">Seleccione un producto.</div>
                </div>

                <div class="mb-3">
                    <label for="sku" class="form-label">SKU:</label>
                    <input type="text" class="form-control" id="sku" name="sku" required maxlength="100">
                    <div class="invalid-feedback">Ingrese el SKU.</div>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio:</label>
                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required placeholder="0.00" onkeydown="return event.keyCode !== 69">
                    <div class="invalid-feedback">Ingrese un precio válido.</div>
                </div>

                <?php
                function selectValores($conn,$attr){
                    $q = $conn->prepare("SELECT va.id, va.valor FROM valores_atributo va JOIN atributos a ON a.id=va.atributo_id WHERE a.nombre=? ORDER BY va.valor");
                    $q->bind_param('s',$attr); $q->execute(); $res=$q->get_result();
                    echo '<div class="mb-3">';
                    echo '<label for="'.strtolower($attr).'_id" class="form-label">'.$attr.':</label>';
                    echo '<select class="form-select" id="'.strtolower($attr).'_id" name="'.strtolower($attr).'_id">';
                    echo '<option value="">-- Seleccione --</option>';
                    while($row=$res->fetch_assoc()){ echo '<option value="'.$row['id'].'">'.htmlspecialchars($row['valor']).'</option>'; }
                    echo '</select>';
                    echo '</div>';
                    $q->close();
                }
                ?>
                
                <?php selectValores($conn,'Color'); ?>
                <?php selectValores($conn,'Capacidad'); ?>
                <?php selectValores($conn,'Modelo'); ?>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar_variante.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Variante</button>
                </div>
            </form>
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