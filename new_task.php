<?php

/**
 * Рабочие очереди (Очереди задач)
 * Распределение трудоемких задач
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('task_queue', false, true, false, false);

$data = implode(' ', array_splice($argv, 1));
if (empty($data)) {
    $data = "Hello World!";
}
//Делаем сообщения постоянными (не потеряются если сервер перезапустится)
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

$channel->basic_publish($msg, '', 'task_queue');

echo ' [x] Sent ', $data, '\n';

$channel->close();
$connection->close();