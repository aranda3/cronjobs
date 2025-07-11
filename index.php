<?php   

require_once 'vendor/autoload.php'; 

require_once 'backend/config/constants.php';
require_once 'backend/config/database.php';
require_once 'backend/helpers/helpers.php';
require_once 'backend/helpers/jwt_helper.php';
require_once 'backend/helpers/auth_middleware.php';
require_once 'backend/helpers/slug_helper.php';

/*require_once 'backend/models/Model.php'; 

foreach (glob('backend/models/*Model.php') as $modelFile) {
    if (basename($modelFile) !== 'Model.php') {
        require_once $modelFile;
    }
}*/

use FastRoute\RouteCollector;

//$authModel = new AuthModel($pdo);

// Definir rutas
$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {

    $ruta_landing = "frontend/landing/";
    $landingRoutes = require 'backend/routes/landing/landingRoutes.php';
    $landingRoutes($r, $ruta_landing);

    $ruta_aplicacion = "frontend/aplicacion/";
    $aplicacionRoutes = require 'backend/routes/aplicacion/aplicacionRoutes.php';
    $aplicacionRoutes($r, $ruta_aplicacion);

    $ruta_tienda = "frontend/tiendas/";
    $tiendaRoutes = require 'backend/routes/tienda/tiendaRoutes.php';
    $tiendaRoutes($r, $ruta_tienda);
    $productoRoutes = require 'backend/routes/tienda/productoRoutes.php';
    $productoRoutes($r, $ruta_tienda);

    $ruta_venta = "modules/ventas/";
    $ventaRoutes = require 'modules/ventas/routes/ventaRoutes.php';
    $ventaRoutes($r, $ruta_venta);

});

// Obtener la ruta actual
$httpMethod = $_SERVER['REQUEST_METHOD'];
//$uri = BASE_URL . $_SERVER['REQUEST_URI'];
$uri = $_SERVER['REQUEST_URI'];

/*echo "REQUEST_URI: " . $uri;
echo "<br>";*/

// Eliminar el prefijo /project-inventario
$uri = str_replace(BASE_URL, '', $uri);

// Limpiar parámetros de la URI
$uri = rawurldecode(explode('?', $uri)[0]);

/*echo $uri;
echo "<br>";*/

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/*echo print_r($routeInfo);
echo "<br>";*/

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "Página no encontrada.";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "Método no permitido.";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        call_user_func($handler, $vars);
        break;
}
