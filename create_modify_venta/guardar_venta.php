<?php
require_once '../connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$cliente_id = $input['cliente_id'];
$productos = $input['productos']; // Array de {id, cantidad, precio}
$subtotal = $input['subtotal'];
$impuesto = $input['impuesto'];
$descuento_tipo = $input['descuento_tipo'];
$descuento_valor = $input['descuento_valor'];
$total = $input['total'];

if (empty($cliente_id) || empty($productos)) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Insertar Venta
    $stmt = $conn->prepare("INSERT INTO ventas (cliente_id, subtotal, impuesto_15, descuento_tipo, descuento_valor, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssdd", $cliente_id, $subtotal, $impuesto, $descuento_tipo, $descuento_valor, $total);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al crear venta: " . $stmt->error);
    }
    
    $venta_id = $stmt->insert_id;
    
    // 2. Insertar Detalles y Validar Stock
    $stmt_detalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, variante_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    
    foreach ($productos as $prod) {
        // Validar stock nuevamente (seguridad)
        $sql_stock = "SELECT 
            (COALESCE((SELECT SUM(cantidad) FROM compras WHERE variante_id = ?), 0) - 
             COALESCE((SELECT SUM(cantidad) FROM detalle_ventas WHERE variante_id = ?), 0)) as stock";
        $stmt_check = $conn->prepare($sql_stock);
        $stmt_check->bind_param("ii", $prod['id'], $prod['id']);
        $stmt_check->execute();
        $res_stock = $stmt_check->get_result()->fetch_assoc();
        
        if ($res_stock['stock'] < $prod['cantidad']) {
            throw new Exception("Stock insuficiente para el producto ID: " . $prod['id']);
        }
        
        $stmt_detalle->bind_param("iiid", $venta_id, $prod['id'], $prod['cantidad'], $prod['precio']);
        if (!$stmt_detalle->execute()) {
            throw new Exception("Error al guardar detalle: " . $stmt_detalle->error);
        }
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'venta_id' => $venta_id, 'message' => 'Venta registrada correctamente']);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>