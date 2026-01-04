<?php
require_once '../connect.php';

header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = "%" . $_GET['q'] . "%";

// Consulta compleja para calcular stock real
$sql = "SELECT 
            v.id, 
            v.sku, 
            v.precio, 
            p.nombre, 
            m.nombre as marca,
            vc.valor as color,
            vcap.valor as capacidad,
            vmod.valor as modelo,
            (
                COALESCE((SELECT SUM(cantidad) FROM compras WHERE variante_id = v.id), 0) - 
                COALESCE((SELECT SUM(cantidad) FROM detalle_ventas WHERE variante_id = v.id), 0)
            ) as stock
        FROM variantes v
        JOIN productos p ON v.producto_id = p.id
        LEFT JOIN marcas m ON p.marca_id = m.id
        LEFT JOIN valores_atributo vc ON v.color_id = vc.id
        LEFT JOIN valores_atributo vcap ON v.capacidad_id = vcap.id
        LEFT JOIN valores_atributo vmod ON v.modelo_id = vmod.id
        WHERE p.nombre LIKE ? OR v.sku LIKE ?
        HAVING stock > 0
        LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $q, $q);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    // Construir nombre completo del producto
    $desc = $row['nombre'] . " " . $row['marca'];
    if ($row['color']) $desc .= " - " . $row['color'];
    if ($row['capacidad']) $desc .= " - " . $row['capacidad'];
    if ($row['modelo']) $desc .= " - " . $row['modelo'];

    $productos[] = [
        'id' => $row['id'],
        'text' => $desc . " (Stock: " . $row['stock'] . ")",
        'sku' => $row['sku'],
        'precio' => $row['precio'],
        'stock' => $row['stock'],
        'nombre_completo' => $desc
    ];
}

echo json_encode($productos);
?>