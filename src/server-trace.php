<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

$socket = new React\Socket\TcpServer('0.0.0.0:9999');
$ws = new \WebSocket\Client("ws://127.0.0.1:9998/");

$socket->on('connection', function (React\Socket\ConnectionInterface $connection) use ($ws) {
    print_r("Hello " . $connection->getRemoteAddress() . "!\n");

    $connection->on('data', function ($data) use ($connection, $ws) {
        print_r( "-- received -- " . $data . "\n");
        $lines = explode("<record_start>", $data);
        foreach($lines as $line) {
            print_r($line . PHP_EOL);
            if (trim($line != '')) {
                $ws->send(trim($line));
            }
            //$this->ws->receive();
        }
//        $ws->send(substr($data,14));
    });

    $connection->on('end', function () {
        print_r( 'ended' . "\n");
    });

    $connection->on('error', function (Exception $e) {
        print_r('error: ' . $e->getMessage() . "\n");
    });

    $connection->on('close', function () {
        print_r('closed' . "\n");
    });
});

echo "Running server-trace \n";