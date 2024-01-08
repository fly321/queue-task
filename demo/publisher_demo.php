<?php


use Fly321\QueueTask\Entity\RabbitMqEntity;
use Fly321\QueueTask\Task\QueueTask;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * 演示发布任务
 */
$entity = new RabbitMqEntity([
    'host' => 'rabbitmt1',
    'port' => '5672',
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => 'test',
]);

$p = new QueueTask($entity);
$p->queueBind("test", "fly123");
$p->queueSend(mt_rand(100,999));