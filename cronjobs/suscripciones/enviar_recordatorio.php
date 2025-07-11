<?php 

//require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$secret_key = "sk_test_51RddXhQoRt3Gj9iGWvciCnUtWYegGpnaOaEBZxCRNRupBm83UHfLhQJkKHB5heJ3bW9jk2ba2fNfxOKtN4IjTITw00fmP658nt";
\Stripe\Stripe::setApiKey($secret_key);

// 1. Conectarse a tu base de datos y obtener suscripciones activas
$dsn = "pgsql:host=dpg-d1ob2n49c44c73fcmc40-a;port=5432;dbname=mitienda03_postgres;user=mitienda03_postgres_user;password=FAamO0g0MwEtsCtHVXozYKzDtbaMuNP4";
$pdo = new PDO($dsn);
// $pdo = new PDO('mysql:host=localhost;dbname=changarrito', 'root', '');
$subscripciones = $pdo->query("SELECT * FROM suscripciones WHERE estado = 'activa'")->fetchAll(PDO::FETCH_ASSOC);

// 2. Fecha objetivo
$zona_horaria = new DateTimeZone("America/Mexico_City");
$hoy = new DateTime("now", $zona_horaria);
//$hoy = new DateTime();

$fecha_objetivo = (clone $hoy)->format('d-m-Y');
//$fecha_objetivo = "07-08-2025";

//echo $hoy->format('d-m-Y H:i:s') . '<br>';
//echo "hoy: " . $fecha_objetivo . '<br><br>';

$correo = "clouseau.pe.0@gmail.com";
$password = "wqdrombpvwasqoqn";

foreach ($subscripciones as $sub) {
    try {

        
        $stripe_sub = \Stripe\Subscription::retrieve($sub['stripe_subscription_id']);

        //echo 'status: ' . $stripe_sub->status . '<br><br>'; 

        //echo "inicio: " . (new DateTime())->setTimestamp($stripe_sub->items->data[0]->current_period_start)->format('Y-m-d') . '<br>';
        $fecha_vencimiento = (new DateTime())->setTimestamp($stripe_sub->items->data[0]->current_period_end);
        //echo "fecha_vencimiento: " .  $fecha_vencimiento->format('d-m-Y') . '<br>'; 

        $fecha_recordatorio = (clone $fecha_vencimiento)->modify('-3 days')->format('d-m-Y');

        echo "hoy: " . $fecha_objetivo . " = fecha_recordatorio: " . $fecha_recordatorio . '?<br>';

        if ($fecha_recordatorio === $fecha_objetivo) {

            if ($stripe_sub->status === 'active') {

                //echo "customer: " . $stripe_sub->customer . '<br><br>';
                echo "customer: " . $stripe_sub->customer . '<br>';
            
                //enviar correo
                // Obtener el cliente
                $customer = \Stripe\Customer::retrieve($stripe_sub->customer);
                $email = $customer->email;
                //echo "email: " . $email . '<br><br>';

                // Enviar correo
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $correo;
                $mail->Password = $password;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom($correo, 'Changarrito');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Tu suscripcion vence pronto';
                $mail->Body = "Hola,<br><br>Tu suscripción vencerá el <strong>{$fecha_vencimiento->format('d-m-Y')}</strong>.<br>Si deseas continuar con el servicio, asegúrate de tener tu método de pago activo.<br><br>Gracias por usar Changarrito.";

                $mail->send();
                echo "✅ Correo enviado a $email<br><br>";
            }
        }

    } catch (Exception $e) {
        echo "❌ Error al procesar suscripción {$sub['stripe_subscription_id']}: {$e->getMessage()}<br>";
    }
}
