<?php
require_once 'connect.php';

echo "<h2>Actualizando Estructura de Roles...</h2>";

// 1. Crear tabla cod_rol si no existe
$sql = "CREATE TABLE IF NOT EXISTS cod_rol (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "✅ Tabla 'cod_rol' verificada.<br>";
} else {
    die("❌ Error creando tabla 'cod_rol': " . $conn->error);
}

// 2. Insertar roles por defecto
$roles = ['Administrador', 'Vendedor', 'Bodeguero', 'Cliente'];
foreach ($roles as $rol) {
    $stmt = $conn->prepare("INSERT IGNORE INTO cod_rol (descripcion) VALUES (?)");
    $stmt->bind_param("s", $rol);
    $stmt->execute();
}
echo "✅ Roles por defecto insertados (Administrador, Vendedor, Bodeguero, Cliente).<br>";

// 3. Agregar columna rol_id a usuarios
$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'rol_id'");
if ($check->num_rows == 0) {
    // Agregar columna
    $sql = "ALTER TABLE usuarios ADD COLUMN rol_id INT DEFAULT NULL AFTER clave";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Columna 'rol_id' agregada a 'usuarios'.<br>";
        
        // Asignar rol 'Cliente' (id 4) a usuarios existentes por defecto
        // Primero buscamos el ID del rol Cliente
        $res = $conn->query("SELECT id FROM cod_rol WHERE descripcion = 'Cliente'");
        if ($row = $res->fetch_assoc()) {
            $cliente_id = $row['id'];
            $conn->query("UPDATE usuarios SET rol_id = $cliente_id WHERE rol_id IS NULL");
            echo "✅ Usuarios existentes actualizados con rol 'Cliente'.<br>";
        }
        
        // Agregar Foreign Key
        $sql = "ALTER TABLE usuarios ADD CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES cod_rol(id)";
        if ($conn->query($sql) === TRUE) {
            echo "✅ Relación (Foreign Key) creada correctamente.<br>";
        } else {
            echo "⚠️ No se pudo crear la FK (puede que haya datos inconsistentes): " . $conn->error . "<br>";
        }
    } else {
        echo "❌ Error agregando columna 'rol_id': " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ La columna 'rol_id' ya existe en 'usuarios'.<br>";
}

echo "<h3>¡Listo! La base de datos ha sido actualizada.</h3>";
echo "<a href='create_modify_user/registro.php'>Ir al Registro</a>";
?>