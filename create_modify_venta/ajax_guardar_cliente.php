<?php
require_once '../connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$cedula = $_POST['cedula'] ?? '';
$nombres = $_POST['nombres'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';

if (empty($cedula) || empty($nombres) || empty($apellidos)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Verificar duplicados
$check = $conn->prepare("SELECT id FROM clientes WHERE cedula = ?");
$check->bind_param("s", $cedula);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'La cédula ya existe']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO clientes (cedula, nombres, apellidos, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $cedula, $nombres, $apellidos, $direccion, $telefono, $correo);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'id' => $stmt->insert_id,
        'text' => $cedula . " - " . $nombres . " " . $apellidos,
        'data' => [
            'cedula' => $cedula,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'direccion' => $direccion,
            'correo' => $correo,
            'telefono' => $telefono
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $conn->error]);
}
?>