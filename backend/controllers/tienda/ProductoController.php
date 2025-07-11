<?php    
 
class ProductoController { 
    
    public function productos() {

        $datos = authRequired(); // ✅ Extrae los datos del token 

        $usuario_id = $datos['usuario_id']; 

        $input = json_decode(file_get_contents("php://input"), true);

        $slug = trim($input['slug'] ?? '');

        if (!$slug) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos"]);
            return;
        }

        try {

            $pdo = getPDO();

            // ✅ Consulta real desde la base de datos
            $stmt = $pdo->prepare(
                "SELECT p.*  FROM productos p
                INNER JOIN tiendas t
                ON p.tienda_id = t.id
                WHERE t.slug = ? AND p.activo='1'");
            $stmt->execute([$slug]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

           echo json_encode([
           "productos" => $productos, 
           "usuario_id" => $usuario_id
        ]);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al cargar productos: " . $e->getMessage()]);
        }

       
    }

    public function update(){

        $input = json_decode(file_get_contents("php://input"), true);

        $codigo_nuevo = validarCampoRequerido($input, 'codigo_nuevo');
        $nombre = validarCampoRequerido($input, 'nombre');
        $marca = validarCampoRequerido($input, 'marca');
        $categoria = validarCampoRequerido($input, 'categoria');
        $precio_venta = validarCampoRequerido($input, 'precio_venta');
        $precio_compra = validarCampoRequerido($input, 'precio_compra');
        $stock = validarCampoRequerido($input, 'stock');
        $unidad = validarCampoRequerido($input, 'unidad');
        $tienda_id = validarCampoRequerido($input, 'tienda_id');
        $codigo = validarCampoRequerido($input, 'codigo');
        $cantidad_en_paquete = validarCampoRequerido($input, 'cantidad_en_paquete');

        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare(
                "UPDATE productos 
                SET codigo = ?,
                nombre = ?, 
                marca = ?, 
                categoria = ?, 
                precio_venta = ?, 
                precio_compra = ?, 
                stock = ?, 
                unidad = ?,
                cantidad_en_paquete = ?
                WHERE tienda_id = ? AND codigo = ?
            ");

            $stmt->execute([
                $codigo_nuevo,
                $nombre,
                $marca,
                $categoria,
                $precio_venta,
                $precio_compra,
                $stock,
                $unidad,
                $cantidad_en_paquete,
                $tienda_id,
                $codigo
            ]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro producto con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar productos: " . $e->getMessage()]);
            }
        }

    }

    public function add(){

        $input = json_decode(file_get_contents("php://input"), true);

        $codigo_nuevo = validarCampoRequerido($input, 'codigo_nuevo');
        $nombre = validarCampoRequerido($input, 'nombre');
        $marca = validarCampoRequerido($input, 'marca');
        $categoria = validarCampoRequerido($input, 'categoria');
        $precio_venta = validarCampoRequerido($input, 'precio_venta');
        $precio_compra = validarCampoRequerido($input, 'precio_compra');
        $stock = validarCampoRequerido($input, 'stock');
        $unidad = validarCampoRequerido($input, 'unidad');
        $tienda_id = validarCampoRequerido($input, 'tienda_id');
        $cantidad_en_paquete = validarCampoRequerido($input, 'cantidad_en_paquete');


        try {

            $pdo = getPDO();

            $stmt = $pdo->prepare(
                "INSERT INTO productos (
                tienda_id,
                codigo,
                nombre, 
                marca, 
                categoria, 
                precio_venta, 
                precio_compra, 
                stock, 
                unidad,
                cantidad_en_paquete
                )
                VALUES (?,?,?,?,?,?,?,?,?,?)"
            );

            $stmt->execute([
                $tienda_id,
                $codigo_nuevo,
                $nombre,
                $marca,
                $categoria,
                $precio_venta,
                $precio_compra,
                $stock,
                $unidad,
                $cantidad_en_paquete            
            ]);

            echo json_encode(["success" => true]);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {
                http_response_code(400);
                echo json_encode(['error' => 'Ya existe otro producto con ese código en esta tienda.']);
                exit();
            }else if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al cargar productos: " . $e->getMessage()]);
            }
        }
    }
}

 

