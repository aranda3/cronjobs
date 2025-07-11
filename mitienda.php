<?php 
//session_start();
// Asegurarse de que el usuario esté logueado
/*if (!isset($_SESSION['propietario_id'])) {
    exit("⛔ Debes iniciar sesión para acceder a tu tienda.");
}*/

//$propietario_id = $_SESSION['propietario_id'];

require 'vendor/autoload.php';
require 'conexion.php';

\Stripe\Stripe::setApiKey('sk_test_51RddXhQoRt3Gj9iGWvciCnUtWYegGpnaOaEBZxCRNRupBm83UHfLhQJkKHB5heJ3bW9jk2ba2fNfxOKtN4IjTITw00fmP658nt');

$propietario_id = 1;

// 1. Obtener propietario
function obtenerPropietario($pdo, $id) {
    $stmt = $pdo->prepare("SELECT stripe_customer_id FROM propietarios WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// 2. Obtener tienda
function obtenerTienda($pdo, $propietario_id) {
    $stmt = $pdo->prepare("
        SELECT t.nombre, t.stripe_price_id, t.fecha_creacion, p.nombre AS nombre_plan
        FROM tiendas t
        JOIN planes p ON p.stripe_price_id = t.stripe_price_id
        WHERE t.propietario_id = ?
    ");
    $stmt->execute([$propietario_id]);
    return $stmt->fetch();
}

// 3. Obtener suscripción activa de Stripe
function obtenerSuscripcionStripe($stripe_customer_id) {
    $subs = \Stripe\Subscription::all([
        'customer' => $stripe_customer_id,
        'limit' => 1
    ]);
    return $subs->data[0] ?? null;
}

// 4. Determinar estado actual
function determinarEstado($sub) {

    $ahora = time();
    $item = $sub->items->data[0] ?? null;
    $renovacion = $item->current_period_end ?? 0;

    /*if (!$renovacion) {
        echo "⚠️ current_period_end vacío o inválido para sub: " . $sub->id;
    }*/

    return ($ahora < $renovacion && $sub->status === 'active') ? 'activa' : 'inactiva';
}

// 👉 FLUJO PRINCIPAL
$cliente = obtenerPropietario($pdo, $propietario_id);
if (!$cliente) exit("⛔ Propietario no encontrado.");

$sub = obtenerSuscripcionStripe($cliente['stripe_customer_id']);
echo $sub;

if (!$sub) exit("⛔ No hay suscripción registrada.");

//echo $sub;

$estadoActual = determinarEstado($sub);

$tienda = obtenerTienda($pdo, $propietario_id);
if (!$tienda) exit("⚠️ No tienes una tienda registrada.");
if ($estadoActual !== 'activa') exit("⛔ Tu tienda está suspendida. Verifica tu pago.");

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Tienda - <?= htmlspecialchars($tienda['nombre']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .card {
      background: white; padding: 20px; border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 500px; margin: auto;
    }
    h2 { margin-top: 0; }
    .info { margin-bottom: 10px; }
    .status { color: green; font-weight: bold; }
  </style>
</head>
<body>

<div class="card">
  <h2>🛍️ <?= htmlspecialchars($tienda['nombre']) ?></h2>

  <div class="info">📦 Plan actual: <strong><?= htmlspecialchars($tienda['nombre_plan']) ?></strong></div>
  <div class="info">📅 Creada el: <?= date('d/m/Y', strtotime($tienda['fecha_creacion'])) ?></div>
  <div class="info">✅ Estado: <span class="status"><?= $estadoActual ?></span></div> 

  <p>¡Bienvenido a tu tienda virtual! Aquí podrás gestionar productos, empleados y ventas.</p>
</div>

</body>
</html>
