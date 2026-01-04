<?php
require_once '../connect.php';

header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = "%" . $_GET['q'] . "%";
$sql = "SELECT id, cedula, nombres, apellidos, direccion, correo, telefono 
        FROM clientes 
        WHERE cedula LIKE ? OR nombres LIKE ? OR apellidos LIKE ? 
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $q, $q, $q);
$stmt->execute();
$result = $stmt->get_result();

$clientes = [];
while ($row = $result->fetch_assoc()) {
    $clientes[] = [
        'id' => $row['id'],
        'text' => $row['cedula'] . " - " . $row['nombres'] . " " . $row['apellidos'],
        'cedula' => $row['cedula'],
        'nombres' => $row['nombres'],
        'apellidos' => $row['apellidos'],
        'direccion' => $row['direccion'],
        'correo' => $row['correo'],
        'telefono' => $row['telefono']
    ];
}

echo json_encode($clientes);
?>