<?php

/**
 * Шаблон опубликовать/подписаться
 * Доставка сообщений нескольким консьюмерам
 */

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//Полная модель обмена сообщениями
//fanout просто транслирует все полученные сообщения во все известные ему очереди
$channel->exchange_declare('logs', 'fanout', false, false, false);

//Создаем временную очередь со сгенерированным именем
//$queue_name содержит случайное имя очереди, сгенерированное RabbitMQ
//Когда соединение закрывается, очередь будет удалена, поскольку объявлена эксклюзивной
list($queue_name, ,) = $channel->queue_declare('', false, false, true, false);

//Привязываем обмен и очередь
$channel->queue_bind($queue_name, 'logs');

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();