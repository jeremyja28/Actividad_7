<?php
session_start();

// Definir rutas relativas basadas en la ubicación del script actual
if (file_exists('connect.php')) {
    // Estamos en la raíz
    $login_path = 'create_modify_user/login.php';
} else {
    // Estamos en una subcarpeta
    $login_path = '../create_modify_user/login.php';
}

// 1. Protección de Rutas (Middleware)
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $login_path);
    exit();
}

// 2. Control de Inactividad
$timeout_duration = 600; // 10 minutos en segundos

if (isset($_SESSION['last_activity'])) {
    $duration = time() - $_SESSION['last_activity'];
    if ($duration > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: " . $login_path . "?msg=timeout");
        exit();
    }
}

// Actualizar timestamp de última actividad
$_SESSION['last_activity'] = time();
?>