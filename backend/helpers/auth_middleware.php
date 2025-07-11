<?php

function authRequired() {
    
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';

    if (!str_starts_with($auth, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(["error" => "Token no enviado"]);
        exit;
    }

    $token = trim(str_replace("Bearer", "", $auth));
    $datos = validarToken($token);

    if (!$datos) {
        http_response_code(401);
        echo json_encode(["error" => "Token inválido o expirado"]);
        exit;
    }

    return $datos;
}
