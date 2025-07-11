<?php            

class LandingController {  

    public function crearCustomerStripe() {

        $input = json_decode(file_get_contents("php://input"), true);
        $email = trim($input['email'] ?? '');
        $propietario_id = $input['propietario_id'] ?? null;

        if (!$email || !$propietario_id) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos"]);
            return;
        }

        \Stripe\Stripe::setApiKey(SECRET_KEY);

        try {

             // Buscar si ya existe un cliente con ese email
            $customers = \Stripe\Customer::all([
                'email' => $email,
                'limit' => 1
            ]);

            if (count($customers->data) > 0) {
                $customer = $customers->data[0];
            } else {
                $customer = \Stripe\Customer::create([
                    'email' => $email
                ]);
            }

            echo json_encode(["stripe_customer_id" => $customer->id]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Stripe error: " . $e->getMessage()]);
        }
    }

    public function crear_suscripcion() {

        \Stripe\Stripe::setApiKey(SECRET_KEY);

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['stripe_price_id'], $input['payment_method_id'])) {
            http_response_code(400);
            echo json_encode(['error' => "Faltan parámetros obligatorios."]);
            exit();
        }

        $stripe_price_id = $input['stripe_price_id'];
        $payment_method_id = $input['payment_method_id'];
        $email = "morin.pe.0@gmail.com";

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {

            $customers = \Stripe\Customer::all([
                'email' => "tu.correo.0@gmail.com",
                'limit' => 1
            ]);

            if (count($customers->data) > 0) {
                echo json_encode(['error' => 'Ya esta registrado: ' . $customers->data[0]->id]);
                exit;
            } 
                
            $customer = \Stripe\Customer::create(['email' => $email]);

            $stripe_customer_id = $customer->id;

            // ⚠️ ¡Adjunta el método de pago al cliente!
            \Stripe\PaymentMethod::retrieve($payment_method_id)->attach([
                'customer' => $stripe_customer_id
            ]);

            $subscription = \Stripe\Subscription::create([
                'customer' => $stripe_customer_id,
                'items' => [[ 'price' => $stripe_price_id ]],
                'default_payment_method' => $payment_method_id, 
                'expand' => ['latest_invoice.payment_intent']
            ]);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Cambia según tu proveedor
            $mail->SMTPAuth   = true;
            $mail->Username   = GMAIL_USER; 
            $mail->Password   = GMAIL_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom(GMAIL_USER, 'Changarrito');
            $mail->addAddress($email); // Cliente

            $mail->isHTML(true);
            $mail->Subject = 'Suscripcion Confirmada';
            $mail->Body    = "Hola,<br><br>Tu suscripción ha sido activada correctamente. <br><br>ID de Suscripción: <strong>{$subscription->id}</strong><br>Gracias por confiar en Changarrito.";

            if ($mail->send()) {
                echo json_encode(['subscription_id' => $subscription->id]);
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(402); // Stripe API error
            echo json_encode(['error' => 'Stripe: ' . $e->getMessage()]);
        } catch (\PHPMailer\PHPMailer\Exception $e) { 
            echo json_encode(['error' => 'Error al enviar correo: ' . $mail->ErrorInfo]);
        } catch (Exception $e) {
            http_response_code(400); // General error
            echo json_encode(['error' => $e->getMessage()]);
        } 

        /*4242 4242 4242 4242
        4000 0000 0000 9995
        12 / 34
        123
        81003*/
        
    }

    public function sincronizar_bd_suscripcion() {

        // Leer datos JSON enviados por JS
        $input = json_decode(file_get_contents("php://input"), true);

        // Validar datos requeridos
        if (!isset($input['stripe_subscription_id'], $input['stripe_price_id'], $input ['propietario_id'], $input ['nivel'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos necesarios.']);
            exit;
        }

        $stripe_subscription_id = $input['stripe_subscription_id'];
        $stripe_price_id = $input['stripe_price_id'];
        $propietarioId = $input['propietario_id'];
        $nivel = $input['nivel'];

        try {

            $estructura = verificarEstructuraTabla('suscripciones', [
                'propietario_id',
                'stripe_subscription_id',
                'stripe_price_id',
                'estado',
                'nivel_plan'
            ]);

            if ($estructura !== true) {
                throw new Exception($estructura);
            }

            $pdo = getPDO();
            $stmt = $pdo->prepare("INSERT INTO suscripciones (propietario_id, stripe_subscription_id, stripe_price_id, estado, nivel_plan) VALUES (?, ?, ?, 'activa', ?)");

            if (!$stmt->execute([$propietarioId, $stripe_subscription_id, $stripe_price_id, $nivel])) {
                throw new Exception('Error al ejecutar la consulta INSERT.');
            }

            if ($stmt->rowCount() === 0) {
                throw new Exception('La inserción no afectó ninguna fila.');
            }
          
            //4242 4242 4242 4242
            
            echo json_encode(['success' => true]);
                  

        } catch (PDOException $e) {
             // Analizar si es un error de clave foránea
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'foreign key')) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Violación de clave foránea.']);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Error BD: ' . $e->getMessage()]);
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        
    }

    public function other(){

         /*$charge = \Stripe\Charge::create([
            'amount' => 10 * 100, // $10.00 USD en centavos
            'currency' => 'usd',
            'source' => 'tok_visa',
            'description' => 'Cargo de ejemplo'
        ]);*/

        /*
        if ($cliente['estado'] !== 'activa') {
        exit("Acceso suspendido por falta de pago.");
        }
        */

        // 2. Asocia método de pago (si es nuevo)
        /*\Stripe\PaymentMethod::attach($paymentMethodId, [
        'customer' => $stripe_customer_id
        ]);

        // 3. Establecer como método por defecto
        \Stripe\Customer::update($stripe_customer_id, [
        'invoice_settings' => [
            'default_payment_method' => $paymentMethodId
        ]
        ]);*/
    }
 
}
