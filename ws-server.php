<?php 

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as ReactSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$loop = LoopFactory::create();

$app = new class implements MessageComponentInterface {

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nueva conexión ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Mensaje recibido: $msg\n";
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Conexión cerrada ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    public function enviarATodos($mensaje) {
        foreach ($this->clients as $client) {
            $client->send($mensaje);
        }
    }
};

$webSocket = new WsServer($app);
$httpServer = new HttpServer($webSocket);
$socket = new ReactSocket('0.0.0.0:8080', $loop);
$server = new IoServer($httpServer, $socket, $loop);

// 🔄 Revisar archivo cada segundo
$loop->addPeriodicTimer(1, function () use ($app) {
    $file = 'backend/controllers/tienda/venta_event.json';
    if (file_exists($file)) {
        $contenido = file_get_contents($file);
        if ($contenido) {
            echo "📨 Evento de venta detectado\n";
            $app->enviarATodos($contenido);
        }
        unlink($file); // eliminar para evitar reenvío
    }
});


echo "Servidor WebSocket escuchando en puerto...\n";
$loop->run();
