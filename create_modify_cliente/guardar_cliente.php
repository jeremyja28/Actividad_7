<?php
require_once '../security.php';
include("../connect.php");

$cedula = $_POST['cedula'] ?? '';
$nombres = $_POST['nombres'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';

if (empty($cedula) || empty($nombres) || empty($apellidos)) {
    header("Location: agregar_cliente.php?error=Datos obligatorios faltantes");
    exit;
}

// Verificar si la cédula ya existe
$check = $conn->prepare("SELECT id FROM clientes WHERE cedula = ?");
$check->bind_param("s", $cedula);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    header("Location: agregar_cliente.php?error=La cédula ya está registrada");
    exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO clientes (cedula, nombres, apellidos, direccion, telefono, correo) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $cedula, $nombres, $apellidos, $direccion, $telefono, $correo);

if ($stmt->execute()) {
    header("Location: listar_cliente.php?msg=Cliente registrado exitosamente");
} else {
    header("Location: agregar_cliente.php?error=Error al registrar el cliente: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
