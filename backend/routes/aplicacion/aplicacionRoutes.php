<?php 

require_once 'backend/controllers/aplicacion/AplicacionController.php';

use FastRoute\RouteCollector;

return function (RouteCollector $r, $ruta) {

    $aplicacionController = new AplicacionController(/*$authModel*/);
    
    function manejarVistaPlanUpgrade($planKey, $vista, $carpeta, $ruta, $price_id) {
        $pdo = getPDO();
                
        $style = $ruta . "suscripciones/planes/$carpeta/css/style.php";
        $script = $ruta . 'suscripciones/actualizar/js/script.php';
        $volver = BASE_URL . '/upgrade/planes';
                
        /*$tiendasActuales = contarTiendas($propietario_id);
        $plan = PLANES_DISPONIBLES[$planKey];
        $maxTiendas = $plan['max_admins'];

        if ($tiendasActuales >= $maxTiendas) {
            require $ruta . 'suscripciones/limite_alcanzado.php';
        } else {*/
            require $ruta . "suscripciones/actualizar/$vista.php";
        //}
    }

    $r->addRoute('GET', '/upgrade/plan-medio', fn() => manejarVistaPlanUpgrade(
        'PLAN_MEDIO', 
        'plan_medio', 
        'medio', 
        $ruta, 
        PLANES_DISPONIBLES['PLAN_MEDIO']['price_id']
    ));
    $r->addRoute('GET', '/upgrade/plan-avanzado', fn() => manejarVistaPlanUpgrade(
        'PLAN_AVANZADO', 
        'plan_avanzado', 
        'avanzado', 
        $ruta, 
        PLANES_DISPONIBLES['PLAN_AVANZADO']['price_id']
    ));

    $r->addRoute('GET', '/upgrade/planes', function () use($ruta){
        $planActual = 'PLAN_MEDIO'; 
        require $ruta . 'suscripciones/actualizar/planes.php';
    });

    $r->addRoute('GET', '/tiendas', function () use($ruta){
        require $ruta . 'index.php';         
    });

    $r->addRoute('GET', '/api/tiendas', function () use ($aplicacionController)  {
        $aplicacionController->misTiendas();     
    });

    $r->addRoute('GET', '/login', function () use($ruta){
        require $ruta . 'auth/login.php';         
    });

    $r->addRoute('POST', '/api/tiendas/crear', function () use ($aplicacionController)  {
        $aplicacionController->crear_tienda();     
    });

    $r->addRoute('GET', '/registro', function () use($ruta){
        require $ruta . 'auth/registro.php';         
    });

    $r->addRoute('GET', '/crear_tienda', function () use($ruta){
        require $ruta . 'crear_tienda.php';         
    });

    $r->addRoute('POST', '/actualizar_suscripcion', function () use ($aplicacionController)  {
        $stripeController->actualizar_suscripcion();     
    });

    $r->addRoute('POST', '/sincronizar_bd_upgrade', function () use ($aplicacionController)  {
        $stripeController->sincronizar_bd_upgrade();  
    });

    $r->addRoute('POST', '/ctrl/login', function () use ($aplicacionController)  {
        $aplicacionController->login();         
    });

    $r->addRoute('POST', '/ctrl/registro', function () use ($aplicacionController) {
        $aplicacionController->registro();
    });

    $r->addRoute('GET', '/api/planes', function () use ($aplicacionController)  {
        $aplicacionController->planPorId();     
    });

    $r->addRoute('GET', '/estadisticas', function () use($ruta){
        require $ruta . 'tiendas/dashboard/index.php';         
    });

    $r->addRoute('POST', '/ctrl/estadisticas', function () use ($aplicacionController)  {
        $aplicacionController->estadisticas();         
    });
    
    $r->addRoute('POST', '/api/usuarios', function () use ($aplicacionController)  {
        $aplicacionController->usuarios();     
    });

    $r->addRoute('GET', '/usuarios', function () use($ruta){
        require $ruta . 'tiendas/usuarios/index.php';         
    });

    $r->addRoute('GET', '/usuario/agregar', function () use($ruta){
        require $ruta . 'tiendas/usuarios/agregar/agregar.php';      
    });

    $r->addRoute('GET', '/usuario/editar/{id}', function ($vars) use($ruta){
        $id = $vars['id'];
        require $ruta . 'tiendas/usuarios/editar/editar.php';      
    });

    $r->addRoute('POST', '/api/usuarios/add', function () use ($aplicacionController)  {
        $aplicacionController->add_usuario();     
    });

    $r->addRoute('POST', '/api/usuarios/update', function () use ($aplicacionController)  {
        $aplicacionController->update_usuario();     
    });

    $r->addRoute('POST', '/api/usuarios/rol_change', function () use ($aplicacionController)  {
        $aplicacionController->rol_change();     
    });

    $r->addRoute('GET', '/tiendas/update/{id}', function ($vars) use($ruta){
        $id = $vars['id'];
        require $ruta . 'tiendas/editar/editar.php';         
    });

    $r->addRoute('POST', '/api/tiendas/update', function () use ($aplicacionController)  {
        $aplicacionController->update_tienda();     
    });

    $r->addRoute('POST', '/api/tiendas/delete', function () use ($aplicacionController)  {
        $aplicacionController->delete_tienda();     
    });

    $r->addRoute('POST', '/api/filtros', function () use ($aplicacionController)  {
        $aplicacionController->filtros();     
    });

    $r->addRoute('GET', '/recordatorio', function () {
        require 'cronjobs/suscripciones/enviar_recordatorio.php';         
    });

    $r->addRoute('GET', '/migracion', function () {
        require 'cronjobs/suscripciones/migracion.php';         
    });

    $r->addRoute('GET', '/tablas', function () {
        require 'cronjobs/suscripciones/tablas.php';         
    });

};
