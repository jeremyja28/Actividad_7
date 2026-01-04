<?php
require_once '../security.php';
include("../connect.php");

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$cedula = $_POST['cedula'] ?? '';
$nombres = $_POST['nombres'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';

if ($id <= 0 || empty($cedula) || empty($nombres) || empty($apellidos)) {
    header("Location: editar_cliente.php?id=$id&error=Datos obligatorios faltantes");
    exit;
}

// Verificar si la cédula ya existe en otro cliente
$check = $conn->prepare("SELECT id FROM clientes WHERE cedula = ? AND id != ?");
$check->bind_param("si", $cedula, $id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    header("Location: editar_cliente.php?id=$id&error=La cédula ya está registrada por otro cliente");
    exit;
}
$check->close();

$stmt = $conn->prepare("UPDATE clientes SET cedula=?, nombres=?, apellidos=?, direccion=?, telefono=?, correo=? WHERE id=?");
$stmt->bind_param("ssssssi", $cedula, $nombres, $apellidos, $direccion, $telefono, $correo, $id);

if ($stmt->execute()) {
    header("Location: listar_cliente.php?msg=Cliente actualizado exitosamente");
} else {
    header("Location: editar_cliente.php?id=$id&error=Error al actualizar el cliente: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
