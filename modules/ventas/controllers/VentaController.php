<?php    
 
class VentaController { 

    public function productos(){

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

            $stmt = $pdo->prepare(
                "SELECT p.*  FROM productos p
                INNER JOIN tiendas t
                ON p.tienda_id = t.id
                WHERE t.slug = ? AND p.activo='1' AND p.stock > 0");
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

    public function crear_venta() {

        $input = json_decode(file_get_contents('php://input'), true);

        $tienda_id = validarCampoRequerido($input, 'tienda_id');
        $usuario_id = validarCampoRequerido($input, 'usuario_id');
        $total = validarCampoRequerido($input, 'total');
        $productos = $input['productos'];

        try {

            $pdo = getPDO();

            $pdo->beginTransaction();

            // Validación de stock
            $faltantes = [];

            foreach ($productos as $p) {
                $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                $stmt->execute([$p['producto_id']]);
                $stockActual = $stmt->fetchColumn();

                if ($stockActual === false || $stockActual < $p['cantidad']) { 
                    $faltantes[] = [
                        "producto_id" => $p['producto_id'],
                        "stock_disponible" => (int)$stockActual
                    ];
                }
            }

            if (!empty($faltantes)) {
                $pdo->rollBack();
                echo json_encode(["success" => false, "faltantes" => $faltantes]);
                return;
            }

            // Insertar venta
            $stmtVenta = $pdo->prepare("INSERT INTO ventas (tienda_id, usuario_id, total) VALUES (?, ?, ?)");
            $stmtVenta->execute([$tienda_id, $usuario_id, $total]);

            $venta_id = $pdo->lastInsertId();

            $stmtDetalle = $pdo->prepare("INSERT INTO detalle_ventas (producto_id, venta_id, precio_venta, cantidad, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

            foreach ($productos as $producto) {
                $stmtDetalle->execute([
                    $producto['producto_id'],
                    $venta_id,
                    $producto['precio_venta'],
                    $producto['cantidad'],
                    $producto['subtotal']
                ]);

                // Actualizar stock real
                $stmtStock->execute([$producto['cantidad'], $producto['producto_id']]);
            }

            $pdo->commit();

            $log = date("Y-m-d H:i:s") . " | Venta ID: $venta_id | Tienda: $tienda_id | Total: S/ $total\n";
            $log .= "Productos:\n";

            foreach ($productos as $p) {
                $log .= "- {$p['producto_id']} | Cant: {$p['cantidad']} | Precio: S/ {$p['precio_venta']} | Subtotal: S/ {$p['subtotal']}\n";
            }

            $log .= str_repeat("-", 50) . "\n";

            file_put_contents(__DIR__ . '/venta_log.txt', $log, FILE_APPEND);

            echo json_encode(["success" => true, "venta_id" => $venta_id]);

            // Emitir a WebSocket / JSON file para difusión
            $mensaje = json_encode([
                "tipo" => "venta_nueva",
                "venta_id" => $venta_id,
                "tienda_id" => $tienda_id,
                "total" => $total,
                "productos" => $productos
            ]);

            file_put_contents(__DIR__ . '/venta_event.json', $mensaje);

        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    }
}