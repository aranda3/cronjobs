<?php  


class AplicacionController {

    public function login() {

        $input = json_decode(file_get_contents("php://input"), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        $pdo = getPDO();

        $stmt = $pdo->prepare(
            "SELECT p.id, p.email, p.stripe_customer_id, p.password_hash, s.stripe_price_id, s.nivel_plan, s.stripe_subscription_id
            FROM usuarios p
            INNER JOIN suscripciones s
            ON s.propietario_id = p.id
            WHERE p.email = ? AND rol = 'propietario'"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $token = generarToken([
                "propietario_id" => $user['id'],
                "email" => $user['email'],
                "stripe_customer_id" => $user['stripe_customer_id'],
                "stripe_price_id" => $user['stripe_price_id'],
                "nivel_plan" => $user['nivel_plan'],
                "stripe_subscription_id" => $user['stripe_subscription_id']
            ]);
            

            echo json_encode(["token" => $token]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Credenciales incorrectas"]);
        }
    }

    public function registro() {    
        
        $input = json_decode(file_get_contents("php://input"), true);
        $email = trim($input['email'] ?? '');
        $password = trim($input['password'] ?? '');

        if (!$email || !$password) { 
            http_response_code(400);
            echo json_encode(["error" => "Correo y contraseña requeridos."]);
            return;
        }

        $pdo = getPDO();

        // ¿Ya existe?
        $stmt = $pdo->prepare("SELECT id FROM propietarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["error" => "Este correo ya está registrado."]);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar
        $stmt = $pdo->prepare("INSERT INTO propietarios (email, password_hash) VALUES (?, ?)");
        $stmt->execute([$email, $hash]);

        $id = $pdo->lastInsertId();

        echo json_encode(["success" => true]);
    } 

    public function planPorId(){
        
        $datos = authRequired(); // ✅ Extrae los datos del token 

        $propietario_id = $datos['propietario_id']; 
        $stripe_subscription_id = $datos['stripe_subscription_id'];

        try {
            $pdo = getPDO();

            // ✅ Consulta real desde la base de datos
            $stmt = $pdo->prepare("SELECT stripe_price_id, stripe_subscription_id, nivel_plan FROM suscripciones WHERE propietario_id = ?");
            $stmt->execute([$propietario_id]);
            $sub = $stmt->fetch(PDO::FETCH_ASSOC);

            $stripe_price_id = $sub['stripe_price_id'];
            $nivel_plan = $sub['nivel_plan'];

            echo json_encode([
                "nivelPlanActual" => $nivel_plan,
                "planes" => PLANES_DISPONIBLES,
                'propietario_id' => $propietario_id,
                'stripe_subscription_id' => $stripe_subscription_id
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar tiendas: " . $e->getMessage()]);
        }

    }
    
    public function actualizar_suscripcion(){

        \Stripe\Stripe::setApiKey(SECRET_KEY);

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['new_price_id'], $input ['propietario_id'], $input['stripe_subscription_id'])) {
            echo json_encode(['error' => "Faltan parámetros obligatorios."]);
            exit();
        }

        $newPriceId = $input['new_price_id'];
        $propietarioId = $input['propietario_id'];
        $stripe_subscription_id = $input['stripe_subscription_id'];

        try { 

            // 🔁 Primero obtenemos la suscripción actual
            $subscription = \Stripe\Subscription::retrieve($stripe_subscription_id);
          
            if ($subscription->status !== 'active') {
                echo json_encode(['error' => "La suscripción no está activa y no puede modificarse."]);
                exit();
            }
            
            $itemId = $subscription->items->data[0]->id;

            // ⚙️ Luego la actualizamos
            $updated = \Stripe\Subscription::update($stripe_subscription_id, [
                'items' => [[
                    'id' => $itemId,
                    'price' => $newPriceId
                ]],
                'cancel_at_period_end' => false,
                'proration_behavior' => 'create_prorations'
            ]);
                
            if ($updated->items->data[0]->price->id !== $newPriceId) { 
                echo json_encode(['error' => "La suscripción no se actualizó correctamente."]); 
                exit();
            }

            echo json_encode(['subscription_id' => $updated->id]); 

        } catch (\Stripe\Exception\ApiErrorException $e) { 
            http_response_code(402); // Stripe API error
            echo json_encode(['error' => 'Stripe: ' . $e->getMessage()]);
        }catch (Exception $e) {
            http_response_code(400); // General error
            echo json_encode(['error' => $e->getMessage()]);
        }

    }

    public function sincronizar_bd_upgrade(){  

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['new_price_id'], $input ['propietario_id'], $input ['nivel_plan'])) {
            echo json_encode(['error' => "Faltan parámetros obligatorios."]);
            exit();
        }

        $newPriceId = $input['new_price_id'];
        $propietarioId = $input['propietario_id'];
        $nivel_plan = $input['nivel_plan'];

        try {

            $pdo = getPDO();
            $stmt = $pdo->prepare("UPDATE suscripciones SET stripe_price_id = ?, nivel_plan = ? WHERE propietario_id = ?"); 
            $stmt->execute([$newPriceId, $propietarioId, $nivel_plan]);

            echo json_encode(['success' => true]);

        } catch (PDOException $e) {
            http_response_code(500); // DB error
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(400); // General error
            echo json_encode(['error' => $e->getMessage()]);
        }

    }

    public function crear_tienda() {
            
        $datos = authRequired(); // ✅ Extrae los datos del token 

        $propietario_id = $datos['propietario_id']; 

        $input = json_decode(file_get_contents("php://input"), true);

        $nombre = validarCampoRequerido($input, 'nombre');

        try {
            $pdo = getPDO();

            // Insertar tienda
            $stmt = $pdo->prepare("INSERT INTO tiendas (nombre, propietario_id, estado) VALUES (?, ?, 'activa')");
            $stmt->execute([$nombre, $propietario_id]);
            $tienda_id = $pdo->lastInsertId();

            // Generar slug
            $slug = generarSlugConHash($nombre, $tienda_id);
            $stmt = $pdo->prepare("UPDATE tiendas SET slug = ? WHERE id = ?");
            $stmt->execute([$slug, $tienda_id]);

            echo json_encode(["slug" => $slug]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otra tienda con ese código.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar tiendas: " . $e->getMessage()]);
            }
        }
    }

    function obtenerPlanPorPriceId($priceId) {
        foreach (PLANES_DISPONIBLES as $clave => $plan) {
            if ($plan['price_id'] === $priceId) {
                return $plan;
            }
        }
        return null; // No encontrado
    }

    public function misTiendas(){
        
        $datos = authRequired(); // ✅ Extrae los datos del token 

        $propietario_id = $datos['propietario_id']; 

        try {
            $pdo = getPDO();

            // ⚠️ Obtener el stripe_price_id desde la BD, no del token
            $stmt = $pdo->prepare("SELECT stripe_price_id FROM suscripciones WHERE propietario_id = ?");
            $stmt->execute([$propietario_id]);
            $stripe_price_id = $stmt->fetchColumn();

            $planSeleccionado = $this->obtenerPlanPorPriceId($stripe_price_id);

            $stmt = $pdo->prepare("SELECT id, nombre, slug, estado FROM tiendas WHERE propietario_id = ?");
            $stmt->execute([$propietario_id]);
            $tiendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "tiendas" => $tiendas,
                "planSeleccionado" => $planSeleccionado['nombre'],
                "limite" => $planSeleccionado['max_admins'],
                "disponibles" => $planSeleccionado['max_admins'] - count($tiendas)
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar tiendas: " . $e->getMessage()]);
        }

    }

    public function estadisticas() {

        $input = json_decode(file_get_contents('php://input'), true);

        $tienda_id = validarCampoRequerido($input, 'tienda_id');

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

    public function usuarios() {

        $input = json_decode(file_get_contents('php://input'), true);

        $tienda_id = validarCampoRequerido($input, 'tienda_id');

        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare("SELECT *  FROM usuarios WHERE tienda_id = ? 
            AND (rol='administrador' OR rol='vendedor' OR rol='colaborador')");
            $stmt->execute([$tienda_id]);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

           echo json_encode(["usuarios" => $usuarios ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar productos: " . $e->getMessage()]);
        }

       
    }

    public function add_usuario(){

        $input = json_decode(file_get_contents("php://input"), true);

        $email = validarCampoRequerido($input, 'email');
        $password = validarCampoRequerido($input, 'password');
        $tienda = validarCampoRequerido($input, 'tienda');

        try {
            
            $pdo = getPDO();

            // ¿Ya existe?
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(["error" => "Este correo ya está registrado."]);
                return;
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (
                email,
                password_hash,
                rol,
                tienda_id
                )
                VALUES (?,?,?,?)"
            );

            $stmt->execute([
                $email,
                $hash,
                'colaborador',
                $tienda     
            ]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro usuario con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar usuarios: " . $e->getMessage()]);
            }
        }
    }
   
    public function update_usuario(){

        $input = json_decode(file_get_contents("php://input"), true);

        $email = validarCampoRequerido($input, 'email');
        $tienda = validarCampoRequerido($input, 'tienda');
        $id = validarCampoRequerido($input, 'id');

        try {

            $pdo = getPDO();

            // ¿Ya existe?
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(["error" => "Este correo ya está registrado."]);
                return;
            }

            $stmt = $pdo->prepare(
                "UPDATE usuarios 
                SET email = ?,
                tienda_id = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $email,
                $tienda,
                $id
            ]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro usuario con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar usuarios: " . $e->getMessage()]);
            }
        }

    }

    public function rol_change(){

        $input = json_decode(file_get_contents("php://input"), true);

        $rol = validarCampoRequerido($input, 'rol');
        $id = validarCampoRequerido($input, 'id');

        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare(
                "UPDATE usuarios 
                SET rol = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $rol,
                $id
            ]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro usuario con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar usuarios: " . $e->getMessage()]);
            }
        }

    }

    public function update_tienda(){

        $input = json_decode(file_get_contents("php://input"), true);

        $id = validarCampoRequerido($input, 'id');
        $nombre = validarCampoRequerido($input, 'nombre');

        try {

            $pdo = getPDO();

            $slug = generarSlugConHash($nombre, $id);

            $stmt = $pdo->prepare(
                "UPDATE tiendas 
                SET nombre = ?,
                slug = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nombre,
                $slug,
                $id
            ]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro usuario con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar usuarios: " . $e->getMessage()]);
            }
        }

    }

    public function delete_tienda(){

        $input = json_decode(file_get_contents("php://input"), true);

        $id = validarCampoRequerido($input, 'id');

        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare("DELETE FROM tiendas WHERE id = ?");

            $stmt->execute([$id]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro usuario con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar usuarios: " . $e->getMessage()]);
            }
        }

    }

    public function filtros(){

        $input = json_decode(file_get_contents("php://input"), true);

        $id = validarCampoRequerido($input, 'id');

        $datos = authRequired(); // ✅ Extrae los datos del token 

        $propietario_id = $datos['propietario_id']; 

        try {
            $pdo = getPDO();

            // ⚠️ Obtener el stripe_price_id desde la BD, no del token
            $stmt = $pdo->prepare("SELECT stripe_price_id FROM suscripciones WHERE propietario_id = ?");
            $stmt->execute([$propietario_id]);
            $stripe_price_id = $stmt->fetchColumn();

            $planSeleccionado = $this->obtenerPlanPorPriceId($stripe_price_id);

            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE tienda_id = ?");
            $stmt->execute([$id]);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                /*"tiendas" => $tiendas,*/
                "plan_usuarios" => $planSeleccionado['plan_usuarios'],
                "usuarios" => count($usuarios)
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar tiendas: " . $e->getMessage()]);
        }

    }

    public function insertar_csv(){
    
        if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] === UPLOAD_ERR_OK) {
            $rutaTemp = $_FILES['archivo_csv']['tmp_name'];
            $archivo = fopen($rutaTemp, 'r');

            $encabezados = fgetcsv($archivo); // Lee la primera fila como encabezados

            echo "<h2>Productos Importados:</h2>";
            echo "<ul>";

            while (($fila = fgetcsv($archivo)) !== false) {
                $producto = array_combine($encabezados, $fila); // Convierte a array asociativo
                echo "<li>{$producto['nombre']} - {$producto['precio']}</li>";
                
                // Aquí podrías insertar en base de datos con mysqli o PDO
                // Ejemplo: insertar_producto($producto['nombre'], $producto['precio']);
            }

            echo "</ul>";
            fclose($archivo);
        } else {
            echo "❌ Error al subir el archivo.";
        }

    }
}
