<?php 

function generarSlugConHash($nombre, $id) {
    $slugBase = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre)));
    $hash = substr(sha1($id), 0, 6); // hash corto basado en ID
    return $slugBase . '-' . $hash;
}