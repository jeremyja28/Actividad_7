
<?php
// Datos de conexión
$servername = "localhost";

// NOTA PARA EL PROFE:
// Si utiliza XAMPP, por favor cambie el puerto a 3306 o elimine la variable $port.
// Este proyecto fue desarrollado en Laragon usando el puerto 3307.
$port = 3306;
$username = "pucesa";         
$password = "pucesa";              
$database = "actividad7";       // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
?>