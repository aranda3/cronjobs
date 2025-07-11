<?php  

require_once 'backend/controllers/tienda/TiendaController.php';

use FastRoute\RouteCollector;

return function (RouteCollector $r, $ruta) {

    $tiendaController = new TiendaController(/*$authModel*/);

    $r->addRoute('GET', '/tiendas/login', function() use($ruta){
        include $ruta . 'auth/login.php';
    });

    $r->addRoute('GET', '/{slug}', function ($vars) use($ruta){
        $slug = $vars['slug'];
        require $ruta . 'dashboard/panel.php';    
    });

    $r->addRoute('POST', '/{slug}/ctrl/login', function () use ($tiendaController)  {
        $tiendaController->login();         
    });

    $r->addRoute('GET', '/ctrl/panel', function () use ($tiendaController)  {
        $tiendaController->panel();         
    });

    $r->addRoute('GET', '/{slug}/reportes', function ($vars) use($ruta){
        $slug = $vars['slug'];
        require $ruta . 'reportes/reportes.php';    
    });

    $r->addRoute('POST', '/ctrl/reportes', function () use ($tiendaController)  {
        $tiendaController->reportes();         
    });

    $r->addRoute('POST', '/ctrl/detventa', function () use ($tiendaController)  {
        $tiendaController->detventa();         
    });

    $r->addRoute('POST', '/api', function () use ($tiendaController)  {
        $tiendaController->api(); 
    });

    $r->addRoute('GET', '/{slug}/stock', function ($vars) use($ruta){
        $slug = $vars['slug'];
        require $ruta . '/stock/stock.php';        
    });

    $r->addRoute('GET', '/{slug}/gastos', function ($vars) use($ruta){
        $slug = $vars['slug'];
        require $ruta . '/gastos/gastos.php';        
    });

    $r->addRoute('POST', '/ctrl/gastos', function () use ($tiendaController)  {
        $tiendaController->gastos();         
    });


};