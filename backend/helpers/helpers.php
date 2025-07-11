<?php   

function contarTiendas(int $propietarioId): int {
    $pdo = getPDO(); 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tiendas WHERE propietario_id = ?");
    $stmt->execute([$propietarioId]);
    return (int) $stmt->fetchColumn();
}

function verificarEstructuraTabla($tabla, $columnasEsperadas) {   
    try {
        $pdo = getPDO(); 
        
        // Verifica que la tabla exista
        $tablaExiste = $pdo->query("SHOW TABLES LIKE '$tabla'")->rowCount() > 0;
        if (!$tablaExiste) {
            return "La tabla '$tabla' no existe.";
        }

        // Verifica columnas
        $cols = $pdo->query("SHOW COLUMNS FROM `$tabla`")->fetchAll(PDO::FETCH_COLUMN);
        $faltantes = array_diff($columnasEsperadas, $cols);

        if (!empty($faltantes)) {
            return "La tabla '$tabla' no tiene estas columnas: " . implode(', ', $faltantes);
        }

        return true;

    } catch (PDOException $e) {
        return "Error al verificar la estructura: " . $e->getMessage();
    }
}

function validarCampoRequerido($input, $text) {

    $valor = trim($input[$text] ?? '');
    
    if ($valor === "") {
        http_response_code(400);
        echo json_encode(["error" => "El campo '$text' es obligatorio."]);
        exit;
    }

    return $valor;
}