<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//const JWT_SECRET = "tu_clave_segura_larga"; // cámbiala a algo privado
const JWT_SECRET = "3N*s~5gZ!kX9@aP1R#vBtD6zWqLmCz2^";

function generarToken($datos) {
    $payload = [
        "iat" => time(),                      // fecha de emisión
        //"exp" => time() + (60 * 60),          // expira en 1 hora
        "data" => $datos                      // tus datos: id, email, etc.
    ];

    return JWT::encode($payload, JWT_SECRET, 'HS256');
}

function validarToken($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return (array) $decoded->data;
    } catch (Exception $e) {
        return null;
    }
}
