<?php 

require_once 'backend/controllers/landing/LandingController.php';

use FastRoute\RouteCollector;

return function (RouteCollector $r, $ruta) { 

        $landingController = new LandingController(/*$authModel*/);

        function manejarVistaPlan($planKey, $vista, $carpeta, $price_id, $nivel, $ruta) {
                $pdo = getPDO();
                $propietario_id = 2;
                $stripe_customer_id ="cus_SYqjMQqB9vX7sp";
                $style = $ruta . "suscripciones/planes/$carpeta/css/style.php";
                $script = $ruta . 'suscripciones/contratar/js/script.php';
                $volver = BASE_URL . '/planes';
                
                require $ruta . "suscripciones/contratar/$vista.php";
        }

        $r->addRoute('GET', '/planes', function () use($ruta){
                require $ruta . 'suscripciones/contratar/planes.php';
        });

        $r->addRoute('GET', '/suscribirme/plan-basico', fn() => manejarVistaPlan(
                'PLAN_BASICO', 
                'plan_basico', 
                'basico',
                PLANES_DISPONIBLES['PLAN_BASICO']['price_id'],
                PLANES_DISPONIBLES['PLAN_BASICO']['nivel'],
                $ruta
        ));
        $r->addRoute('GET', '/suscribirme/plan-medio', fn() => manejarVistaPlan(
                'PLAN_MEDIO', 
                'plan_medio', 
                'medio',
                PLANES_DISPONIBLES['PLAN_MEDIO']['price_id'],
                PLANES_DISPONIBLES['PLAN_MEDIO']['nivel'],
                $ruta
        ));
        $r->addRoute('GET', '/suscribirme/plan-avanzado', fn() => manejarVistaPlan(
                'PLAN_AVANZADO', 
                'plan_avanzado', 
                'avanzado',
                PLANES_DISPONIBLES['PLAN_AVANZADO']['price_id'],
                PLANES_DISPONIBLES['PLAN_AVANZADO']['nivel'],
                $ruta
        ));

        $r->addRoute('POST', '/crear_suscripcion', function () use ($landingController)  {
                $landingController->crear_suscripcion();      
        });

        $r->addRoute('POST', '/sincronizar_bd_suscripcion', function () use ($landingController)  {
                $landingController->sincronizar_bd_suscripcion();
        });

        
        $r->addRoute('GET', '/home', function () {
                require 'frontend/landing/home/home.php';
        });

        $r->addRoute('GET', '/planes/basico', function () {
                require 'frontend/landing/planes/basico/index.php';
        });

          /*$r->addRoute('POST', '/crear_tienda', function () use ($tiendaController)  {
        $tiendaController->crear_tienda();         
    });

    $r->addRoute('POST', '/ctrl/crear-customer', function () use ($stripeController) {
        $stripeController->crearCustomerStripe();
    });*/
};