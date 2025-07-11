<?php

class UserModel extends Model{
 
  
    public function __construct($pdo) { 
        parent::__construct($pdo);
    }

 // Obtener todos los registros
    public function obtenerTodo() {

        $sql = "SELECT * FROM gmail";

        $stmt = $this->pdo->query($sql);

        try {
            return ["status" => "success", "message" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ["status" => "error", 'message' => 'Error al consultar: ' . $e->getMessage()];
        }
 
    }

    
    // Obtener datos por ID
    public function obtenerDatosPorId($sql, $params) {
        
        try {

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);   

            return $stmt->fetch(PDO::FETCH_ASSOC); 

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    // Actualizar datos
    public function actualizarDatos($sql, $params) {
                
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            return $e->getMessage();
        }

    }

    public function correos(){

        $sql = "SELECT * FROM gmail";

        $stmt = $this->pdo->query($sql);

        try {
            return ["status" => "success", "message" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ["status" => "error", 'message' => 'Error al consultar: ' . $e->getMessage()];
        }
 
    }
    
}
