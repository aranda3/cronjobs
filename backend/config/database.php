<?php 

function getPDO() { 

    static $pdo = null;

    if ($pdo === null) {
        
        try {

            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            // Crear una instancia de PDO
            $pdo = new PDO($dsn, DB_USER, DB_PASS);

            // Configurar PDO para que lance excepciones en caso de error
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //echo "Conexión exitosa a la base de datos.";
        } catch (PDOException $e) {
            // Detener ejecución y mostrar un mensaje claro
            http_response_code(500);
            echo json_encode(['error' => 'Error al conectar con la base de datos: ' . $e->getMessage()]);
            exit; // Muy importante para que no siga el script
        }

    }
    
    return $pdo;

}