<?php
require_once '../connect.php';

echo "<h1>Diagnóstico y Reparación de Usuario Admin</h1>";

// 1. Verificar conexión
if ($conn->connect_error) {
    die("<p style='color:red'>Error de conexión: " . $conn->connect_error . "</p>");
} else {
    echo "<p style='color:green'>Conexión a BD exitosa.</p>";
}

// 2. Verificar si la tabla existe y tiene las columnas correctas
$checkTable = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'cedula'");
if ($checkTable->num_rows == 0) {
    die("<p style='color:red'>ERROR CRÍTICO: La tabla 'usuarios' no tiene la estructura nueva (falta columna 'cedula'). Por favor importa el archivo 'actividad7_FULL_FIXED.sql' en phpMyAdmin.</p>");
}

// 3. Intentar crear/actualizar el usuario admin
$cedula = '1234567890';
$correo = 'admin@sistema.com';
$clave = 'admin123';
$clave_md5 = md5($clave);
$nombre = 'Admin';
$apellido = 'Sistema';
$telefono = '0999999999';
$estado = 'activo';

// Verificar si ya existe
$sql = "SELECT id FROM usuarios WHERE cedula = '$cedula' OR correo = '$correo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Actualizar
    $sql_update = "UPDATE usuarios SET 
        clave = '$clave_md5', 
        estado = 'activo',
        nombre = '$nombre',
        apellido = '$apellido'
        WHERE cedula = '$cedula' OR correo = '$correo'";
    
    if ($conn->query($sql_update)) {
        echo "<p style='color:blue'>Usuario Admin existente actualizado correctamente.</p>";
    } else {
        echo "<p style='color:red'>Error actualizando usuario: " . $conn->error . "</p>";
    }
} else {
    // Insertar
    // Necesitamos valores para los campos NOT NULL
    $dummy_md5 = md5('respuesta');
    $sql_insert = "INSERT INTO usuarios 
    (cedula, nombre, apellido, correo, telefono, clave, pregunta_1_id, respuesta_1, pregunta_2_id, respuesta_2, pregunta_3_id, respuesta_3, estado) 
    VALUES 
    ('$cedula', '$nombre', '$apellido', '$correo', '$telefono', '$clave_md5', 1, '$dummy_md5', 6, '$dummy_md5', 11, '$dummy_md5', '$estado')";

    if ($conn->query($sql_insert)) {
        echo "<p style='color:green'>Usuario Admin creado correctamente.</p>";
    } else {
        echo "<p style='color:red'>Error creando usuario: " . $conn->error . "</p>";
    }
}

echo "<h3>Datos de Acceso Válidos:</h3>";
echo "<ul>";
echo "<li><strong>Usuario (Cédula):</strong> $cedula</li>";
echo "<li><strong>Usuario (Correo):</strong> $correo</li>";
echo "<li><strong>Contraseña:</strong> $clave</li>";
echo "</ul>";
echo "<p><a href='login.php'>Ir al Login</a></p>";

// 4. Listar usuarios existentes para depuración
echo "<hr><h3>Usuarios en la Base de Datos:</h3>";
$res = $conn->query("SELECT id, cedula, correo, estado, clave FROM usuarios");
if ($res->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Cédula</th><th>Correo</th><th>Estado</th><th>Hash Clave</th></tr>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['cedula'] . "</td>";
        echo "<td>" . $row['correo'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . $row['clave'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No hay usuarios registrados.";
}
?>
