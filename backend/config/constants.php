<?php

define('DB_SERVER', 'localhost');   
define('DB_NAME', 'changarrito');  
define('DB_USER', 'root');          
define('DB_PASS', '');             
define('DB_CHARSET', 'utf8mb4');
   
define('BASE_URL', '/web-changarrito');

define('SECRET_KEY', 'sk_test_51RddXhQoRt3Gj9iGWvciCnUtWYegGpnaOaEBZxCRNRupBm83UHfLhQJkKHB5heJ3bW9jk2ba2fNfxOKtN4IjTITw00fmP658nt');
define('PUBLIC_KEY', 'pk_test_51RddXhQoRt3Gj9iGpCHwVB15XOMaAaBdFqTcKRjl1Vmu3qp1aNfaBu68Xd8bHW4qQZlPhEeULhzxIzE460qDteiB0073TGF84z');

//plan_usuarios = administradores + vendedores
define('PLANES_DISPONIBLES', [
    'PLAN_BASICO' => [
        'price_id' => 'price_1RdeE9QoRt3Gj9iGdkFSSKCd',
        'nombre' => 'Plan Básico',
        'precio' => 1999,
        'max_admins' => 1,
        'max_vendedores' => 2,
        'ruta' => 'plan-basico',
        'nivel' => 1,
        'plan_usuarios' => 3
    ],
    'PLAN_MEDIO' => [
        'price_id' => 'price_1RdeF4QoRt3Gj9iG4Jzyx26Q',
        'nombre' => 'Plan Medio',
        'precio' => 2999,
        'max_admins' => 2,
        'max_vendedores' => 4,
        'ruta' => 'plan-medio',
        'nivel' => 2,
        'plan_usuarios' => 6
    ],
    'PLAN_AVANZADO' => [
        'price_id' => 'price_1RdeG0QoRt3Gj9iG0jf3mPkw',
        'nombre' => 'Plan Avanzado',
        'precio' => 3999,
        'max_admins' => 3,
        'max_vendedores' => 10,
        'ruta' => 'plan-avanzado',
        'nivel' => 3,
        'plan_usuarios' => 13
    ]
]);


define("GMAIL_USER", "clouseau.pe.0@gmail.com");
define("GMAIL_PASSWORD", "wqdrombpvwasqoqn");
