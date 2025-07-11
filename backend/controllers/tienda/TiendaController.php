<?php    
 
class TiendaController { 

    public function login() {

        $input = json_decode(file_get_contents("php://input"), true);

        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $slug = $input['slug'] ?? '';


        $pdo = getPDO();

        // ✅ Verificar si la tienda existe
        $stmt = $pdo->prepare("SELECT * FROM tiendas  WHERE slug = ?");
        $stmt->execute([$slug]);
        $tienda = $stmt->fetch();

        if (!$tienda) {
            http_response_code(404);
            echo json_encode(["error" => "La tienda no existe."]);
            exit();
        }


        // ✅ Verificar si el usuario pertenece a la tienda
        $stmt = $pdo->prepare("SELECT id, email, password_hash, rol FROM usuarios  WHERE email = ? AND tienda_id = ?");
        $stmt->execute([$email, $tienda['id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {

            $token = generarToken([
                "usuario_id" => $user['id'],
                "email" => $user['email'],
                "tienda_id" => $tienda['id'],
                "rol" => $user['rol'],
                "slug" => $slug
            ]);


            echo json_encode(["token" => $token]);

        } else {
            http_response_code(401);
            echo json_encode(["error" => "Credenciales incorrectas"]);
        }
    }

    public function index() {
        $usuario = authRequired(); // Esto detiene si no hay token válido

        echo json_encode([
            "mensaje" => $usuario['email'],
            "propietario_id" => $usuario['propietario_id']
        ]);
    }

    public function panel() {

        $datos = authRequired(); // Esto detiene si no hay token válido
        
        $tienda_id = $datos['tienda_id'];

        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare("SELECT *  FROM ventas WHERE tienda_id = ?");
            $stmt->execute([$tienda_id]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("SELECT dv.* FROM detalle_ventas dv
                JOIN ventas v ON dv.venta_id = v.id
                WHERE v.tienda_id = ?
            ");
            $stmt->execute([$tienda_id]);
            $detventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("SELECT * FROM productos  WHERE tienda_id = ? AND activo='1'");
            $stmt->execute([$tienda_id]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("SELECT * FROM productos  WHERE tienda_id = ?");
            $stmt->execute([$tienda_id]);
            $productos_totales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "ventas" => $ventas, 
                "detventas" => $detventas,
                "productos" => $productos,
                "productos_totales" => $productos_totales
            ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar datos: " . $e->getMessage()]);
        }
       
    }

    public function reportes() {

        $datos = authRequired(); // ✅ Extrae los datos del token 

        $tienda_id = $datos['tienda_id']; 

        /*$input = json_decode(file_get_contents("php://input"), true);

        $slug = trim($input['slug'] ?? '');

        if (!$slug) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos"]);
            return;
        }*/

        try {

            $pdo = getPDO();

            // ✅ Consulta real desde la base de datos
            $stmt = $pdo->prepare("SELECT *  FROM ventas WHERE tienda_id = ?");
            $stmt->execute([$tienda_id]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

           echo json_encode(["ventas" => $ventas, "tienda_id" => $tienda_id ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar productos: " . $e->getMessage()]);
        }
       
    }

    public function detventa(){

        $input = json_decode(file_get_contents('php://input'), true);

        $venta_id = validarCampoRequerido($input, 'venta_id');

        try {

            $pdo = getPDO();

            // ✅ Consulta real desde la base de datos
            $stmt = $pdo->prepare("SELECT * FROM detalle_ventas WHERE venta_id = ?");
            $stmt->execute([$venta_id]);
            $detventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

           echo json_encode(["detventas" => $detventas]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar productos: " . $e->getMessage()]);
        }

    }

    public function api(){

        $usuario = authRequired();

        echo json_encode(["rol" => $usuario['rol'], "slug" => $usuario['slug']]);
    }

     public function gastos() {

        $datos = authRequired(); // Esto detiene si no hay token válido
        
        $tienda_id = $datos['tienda_id'];

        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare("SELECT * FROM productos  WHERE tienda_id = ? AND activo='1'");
            $stmt->execute([$tienda_id]);
            $activos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("SELECT * FROM productos  WHERE tienda_id = ? AND activo='0'");
            $stmt->execute([$tienda_id]);
            $inactivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "activos" => $activos,
                "inactivos" => $inactivos
            ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar datos: " . $e->getMessage()]);
        }
       
    }

}

 

