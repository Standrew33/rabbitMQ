<?php

/**
 * Hello World
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//Соединение к серверу Rabbit и создание канала
$connection = new AMQPStreamConnection('localhost', '5672', 'guest', 'guest');
$channel = $connection->channel();

//Объявляем очередь для отправки
$channel->queue_declare('hello', false, false, false, false);

//Содержимое очереди - массив байтов
$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

//Закрываем канал и соединение
$channel->close();
$connection->close();