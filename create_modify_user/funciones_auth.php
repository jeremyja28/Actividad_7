<?php
// create_modify_user/funciones_auth.php

// Banco de preguntas global
$bancoPreguntas = [
    1 => [
        1 => "¿Cuál es el nombre de tu primera mascota?",
        2 => "¿En qué ciudad naciste?",
        3 => "¿Cuál es tu color favorito?",
        4 => "¿Cuál es el nombre de tu abuelo paterno?",
        5 => "¿Cuál es tu comida favorita?"
    ],
    2 => [
        6 => "¿Cuál es el segundo nombre de tu madre?",
        7 => "¿En qué año te graduaste de la escuela?",
        8 => "¿Cuál es tu película favorita?",
        9 => "¿Cómo se llamaba tu primer colegio?",
        10 => "¿Cuál es tu deporte favorito?"
    ],
    3 => [
        11 => "¿Cuál es el nombre de tu mejor amigo de la infancia?",
        12 => "¿Cuál es tu destino de vacaciones soñado?",
        13 => "¿Cuál es la marca de tu primer auto?",
        14 => "¿Cuál es tu libro favorito?",
        15 => "¿Cuál es tu canción favorita?"
    ]
];

function validarCedulaEcuatoriana($cedula) {
    if (strlen($cedula) !== 10) return false;
    if (!is_numeric($cedula)) return false;

    $provincia = substr($cedula, 0, 2);
    if ($provincia < 1 || $provincia > 24) return false;

    $tercerDigito = $cedula[2];
    if ($tercerDigito >= 6) return false; // Personas naturales

    $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    $suma = 0;

    for ($i = 0; $i < 9; $i++) {
        $valor = $cedula[$i] * $coeficientes[$i];
        if ($valor >= 10) {
            $valor -= 9;
        }
        $suma += $valor;
    }

    $digitoVerificador = ($suma % 10 == 0) ? 0 : 10 - ($suma % 10);
    
    return $digitoVerificador == $cedula[9];
}

function validarTelefono($telefono) {
    // Debe empezar con 09 y tener 10 dígitos
    return preg_match('/^09\d{8}$/', $telefono);
}

function enviarWhatsApp($telefono, $mensaje) {
    // ---------------------------------------------------------
    // DATOS DE ULTRAMSG (Sacados de tu captura)
    // ---------------------------------------------------------
    $instanceId = "instance155632"; // YA LO PUSE POR TI (según tu foto)
    $token = "1xllvx9e9yey0myd"; // <--- ESTO LO COPIAS DE LA WEB AL ESCANEAR EL QR

    // ---------------------------------------------------------
    // LÓGICA DE ENVÍO
    // ---------------------------------------------------------
    $telefono = trim($telefono);
    
    // Formatear para Ecuador (agregar 593 si falta)
    if (substr($telefono, 0, 1) == '0') {
        // Convierte 099... a 59399...
        $telefonoEnvio = '593' . substr($telefono, 1);
    } elseif (substr($telefono, 0, 3) == '593') {
        $telefonoEnvio = $telefono;
    } else {
        $telefonoEnvio = '593' . $telefono;
    }

    $params = array(
        'token' => $token,
        'to' => $telefonoEnvio,
        'body' => $mensaje
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.ultramsg.com/$instanceId/messages/chat",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => http_build_query($params),
      CURLOPT_HTTPHEADER => array(
        "content-type: application/x-www-form-urlencoded"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) { return false; } 
    return true;
}
?>
