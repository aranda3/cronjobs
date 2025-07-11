<?php  

require_once 'modules/ventas/controllers/VentaController.php';

use FastRoute\RouteCollector;

return function (RouteCollector $r, $ruta) {

    $ventaController = new VentaController(/*$authModel*/);

    $r->addRoute('POST', '/api/productosEnStock', function () use ($ventaController)  {
        $ventaController->productos();     
    });

    $r->addRoute('POST', '/api/ventas/crear', function () use ($ventaController){
        $ventaController->crear_venta(); 
    });

    $r->addRoute('GET', '/{slug}/venta', function ($vars) use($ruta){
        $slug = $vars['slug'];
        $bootstrap = BASE_URL . '/assets/bootstrap/';
        $datatables = BASE_URL . '/assets/datatables/';
        $jquery = BASE_URL . '/assets/jquery/';
        $assets = BASE_URL . '/assets';
        require $ruta . 'views/venta.php';        
    });

    $r->addRoute('GET', '/debug/ventas', function () use($ruta){
        $pdo = getPDO();
        require $ruta . 'views/debug/debug_ventas.php';        
    });



};