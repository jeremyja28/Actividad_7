<?php
require_once __DIR__ . '/../connect.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$c = null;
if ($id>0){
  $stmt=$conn->prepare("SELECT * FROM compras WHERE id=?");
  $stmt->bind_param('i',$id); $stmt->execute(); $res=$stmt->get_result();
  if($res && $res->num_rows===1){ $c=$res->fetch_assoc(); }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Compra</title>
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
            <h3>Editar Compra</h3>
        </div>
        <div class="card-body">
            <?php if($c): ?>
            <form action="actualizar_compra.php" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">

                <div class="mb-3">
                    <label for="variante_id" class="form-label">Variante:</label>
                    <select class="form-select" id="variante_id" name="variante_id" required>
                        <option value="">-- Seleccione --</option>
                        <?php
                        $vars=$conn->query("SELECT v.id, v.sku, p.nombre AS producto FROM variantes v JOIN productos p ON p.id=v.producto_id ORDER BY p.nombre, v.sku");
                        while($va=$vars->fetch_assoc()){
                            $sel = ($va['id'] == $c['variante_id']) ? 'selected' : '';
                            echo '<option value="'.$va['id'].'" '.$sel.'>'.htmlspecialchars($va['producto']).' - '.htmlspecialchars($va['sku']).'</option>';
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Seleccione una variante.</div>
                </div>

                <div class="mb-3">
                    <label for="proveedor_id" class="form-label">Proveedor:</label>
                    <select class="form-select" id="proveedor_id" name="proveedor_id" required>
                        <option value="">-- Seleccione --</option>
                        <?php
                        $prov=$conn->query("SELECT id, nombre_empresa FROM proveedores ORDER BY nombre_empresa");
                        while($pr=$prov->fetch_assoc()){
                            $sel = ($pr['id'] == $c['proveedor_id']) ? 'selected' : '';
                            echo '<option value="'.$pr['id'].'" '.$sel.'>'.htmlspecialchars($pr['nombre_empresa']).'</option>';
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Seleccione un proveedor.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="precio_unitario" class="form-label">Precio Unitario:</label>
                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" step="0.01" min="0" required value="<?php echo $c['precio_unitario']; ?>" onkeydown="return event.keyCode !== 69">
                        <div class="invalid-feedback">Ingrese un precio válido.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" step="1" required value="<?php echo $c['cantidad']; ?>" onkeydown="return event.keyCode !== 69">
                        <div class="invalid-feedback">La cantidad debe ser al menos 1.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fecha_compra" class="form-label">Fecha Compra:</label>
                    <?php $dt = str_replace(' ', 'T', substr($c['fecha_compra'],0,16)); ?>
                    <input type="datetime-local" class="form-control" id="fecha_compra" name="fecha_compra" value="<?php echo $dt; ?>" max="<?php echo date('Y-m-d\TH:i'); ?>">
                    <div class="invalid-feedback">La fecha no puede ser futura.</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="listar_compra.php" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Compra</button>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-danger" role="alert">
                No se encontró la compra.
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