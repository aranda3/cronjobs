<?php  

require_once 'backend/controllers/tienda/ProductoController.php';

use FastRoute\RouteCollector;

return function (RouteCollector $r, $ruta) {

    $productoController = new ProductoController(/*$authModel*/);

    $r->addRoute('POST', '/productos', function () use ($productoController)  {
        $productoController->productos();     
    });

    $r->addRoute('GET', '/{slug}/productos', function ($vars) use($ruta){
        $slug = $vars['slug'];
        require $ruta . 'productos/index.php';    
    });

    $r->addRoute('GET', '/{slug}/producto/editar/{codigo}', function ($vars) use($ruta){
        $slug = $vars['slug']; 
        $codigo = $vars['codigo'];
        require $ruta . 'productos/editar/editar.php';      
    });

    $r->addRoute('GET', '/{slug}/producto/agregar', function ($vars) use($ruta){
        $slug = $vars['slug']; 
        require $ruta . 'productos/agregar/agregar.php';      
    });

    $r->addRoute('POST', '/api/productos/update', function () use ($productoController)  {
        $productoController->update();     
    });

    $r->addRoute('POST', '/api/productos/add', function () use ($productoController)  {
        $productoController->add();     
    });

    /*$r->addRoute('POST', '/{slug}/producto/editar', function ($vars) use($ruta){
        $slug = $vars['slug'];
        $tienda_id = $_POST['tienda_id'] ?? null;
        $codigo = $_POST['codigo'] ?? null;
        require $ruta . 'tiendas/productos/editar/editar.php';    
    });*/

};