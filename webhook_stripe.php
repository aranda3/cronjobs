<?php
require 'vendor/autoload.php';
require 'conexion.php';

// 🔒 Aquí pegas tu secreto
$endpoint_secret = 'whsec_d1247d940e3bd5b3ab35c21e8b1784bbcdae307a46d46aa03fef8730c5a78925';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

try {
    // Verifica que el evento venga de Stripe
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit('❌ JSON inválido');
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit('❌ Firma inválida');
}

// Aquí manejas los eventos normalmente
switch ($event->type) {

    case 'invoice.payment_failed':
        $subscriptionId = $event->data->object->subscription;
        $stmt = $pdo->prepare("UPDATE clientes SET estado = 'suspendida' WHERE stripe_subscription_id = ?");
        $stmt->execute([$subscriptionId]);
        break;

    case 'invoice.paid':
        $subscriptionId = $event->data->object->subscription;
        $stmt = $pdo->prepare("UPDATE clientes SET estado = 'activa' WHERE stripe_subscription_id = ?");
        $stmt->execute([$subscriptionId]);
        break; 
}

http_response_code(200);
echo '✅ Evento recibido';