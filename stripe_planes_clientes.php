<?php  
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51RddXhQoRt3Gj9iGWvciCnUtWYegGpnaOaEBZxCRNRupBm83UHfLhQJkKHB5heJ3bW9jk2ba2fNfxOKtN4IjTITw00fmP658nt'); // Reemplaza con tu secret key 

//header('Content-Type: application/json'); 

try {
    // Obtener precios (planes)
    $prices = \Stripe\Price::all(['limit' => 100]);
    $priceMap = [];
    foreach ($prices->data as $price) {
        $planName = $price->nickname ?: $price->id;
        $priceMap[$price->id] = $planName;
    }

    // Obtener suscripciones activas
    $subscriptions = \Stripe\Subscription::all([
        'status' => 'active',
        'limit' => 100,
        'expand' => ['data.customer']
    ]);

    $result = [];

    foreach ($subscriptions->data as $sub) {
        $email = $sub->customer->id ?? 'Desconocido';
        $priceId = $sub->items->data[0]->price->id ?? '';
        $plan = $priceMap[$priceId] ?? $priceId;
        $estado = $sub->status;
        $renovacion = date('Y-m-d', $sub->current_period_end);

        $result[] = [
            'email' => $email,
            'plan' => $plan,
            'estado' => $estado,
            'renovacion' => $renovacion
        ];
    }

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
