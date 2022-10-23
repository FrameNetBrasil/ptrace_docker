<?php
namespace App;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Trace implements MessageComponentInterface {
    protected $clients;
    protected $ws;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->ws = new \WebSocket\Client("ws://127.0.0.1:9998/");
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        print_r($msg . PHP_EOL);
        $lines = explode("<record_start>", $msg);
        foreach($lines as $line) {
            print_r($line . PHP_EOL);
            $this->ws->send($line);
            $this->ws->receive();
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $this->ws->close();
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}