<?php

use Fly321\QueueTask\Entity\RabbitMqEntity;
use Fly321\QueueTask\Task\QueueTask;

require_once __DIR__ . '/../vendor/autoload.php';

$entity = new RabbitMqEntity([
    'host' => 'rabbitmt1',
    'port' => '5672',
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => '/',
]);

$service = new QueueTask($entity);
$service->queueBind("test01", "fly123");
$service->consume("fly123", function ($msg) {
    echo "\n--------\n";
    var_dump(json_decode($msg->body), true);
    echo "\n--------\n";
    $msg->ack();
});
