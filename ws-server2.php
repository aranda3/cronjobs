<?php 

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class App implements MessageComponentInterface {

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }


    /*public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nueva conexión ({$conn->resourceId})\n";
    }*/

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $conn->usuario = "desconocido"; // Default temporal
        echo "Nueva conexión ({$conn->resourceId})\n";
    }
    
    /*public function onMessage(ConnectionInterface $from, $msg) {
        echo "Mensaje recibido: $msg\n";
        //$from->send("Echo: " . $msg);
        foreach ($this->clients as $client) {
            $client->send("📢 " . $msg);
        }
    }*/

    /*public function onMessage(ConnectionInterface $from, $msg) {
        echo "Mensaje recibido: $msg\n";

        $data = json_decode($msg, true);
        if (!$data) {
            $from->send("⚠️ Mensaje no válido");
            return;
        }

        switch ($data["comando"]) {
            case "conectar":

                if (!isset($data["token"])) {
                    $from->send("❌ Token no proporcionado");
                    return;
                }

                try {
                    $secret = "3N*s~5gZ!kX9@aP1R#vBtD6zWqLmCz2^"; 
                    $decoded = JWT::decode($data["token"], new Key($secret, 'HS256'));

                    // Si el token es válido, usamos el nombre del payload
                    $from->usuario = $decoded->usuario ?? "anon";
                    $this->broadcast("🔔 {$from->usuario} se ha conectado");

                } catch (\Exception $e) {
                    $from->send("❌ Token inválido: " . $e->getMessage());
                    return;
                }

                break;

            case "mensaje":
                $usuario = $from->usuario ?? "anon";
                $texto = $data["contenido"] ?? "";
                $this->broadcast("💬 {$usuario}: {$texto}");
                break;

            default:
                $from->send("❓ Comando desconocido");
        }
    }*/

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Mensaje recibido:\n$msg\n";

        // Divide por saltos de línea
        $lineas = preg_split("/\r\n|\n|\r/", trim($msg));
        if (count($lineas) < 1) {
            $from->send("ERROR 400 Formato inválido\r\n");
            return;
        }

        // 1. Primera línea: método y recurso
        if (preg_match('/^(GET|POST|PING|LOGIN|MSG) (.+)$/', $lineas[0], $m)) {
            $comando = $m[1];
            $recurso = $m[2];
        } else {
            $from->send("ERROR 400 Comando inválido\r\n");
            return;
        }

        // 2. Parsear headers
        $headers = [];
        for ($i = 1; $i < count($lineas); $i++) {
            if (strpos($lineas[$i], ": ") !== false) {
                list($key, $val) = explode(": ", $lineas[$i], 2);
                $headers[$key] = $val;
            }
        }

        // 3. Ejecutar lógica según comando
        if ($comando === "GET" && $recurso === "/productos") {
            $from->send("200 OK\r\nContent-Type: text/plain\r\n\r\nLista de productos: Pan, Leche");
        } elseif ($comando === "PING") {
            $from->send("200 OK\r\nPONG\r\n");
        } else {
            $from->send("ERROR 404 Recurso no encontrado\r\n");
        }
    }

    protected function broadcast($mensaje) {
        foreach ($this->clients as $client) {
            $client->send($mensaje);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Conexión cerrada ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(new App())
    ), 8080);

echo "Servidor WebSocket escuchando en puerto...\n";
$server->run();
