<?php

/**
 * Hello World
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//Должно соответствовать очереди в которую отправляются публикации
$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, '\n';
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

//Блокируем код пока есть колбек
while ($channel->is_consuming()) {
    $channel->wait();
}