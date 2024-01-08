<?php

namespace Fly321\QueueTask\Entity;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqEntity extends BaseEntity
{
    /**
     * @var string $host rabbitmq host
     */
    public string $host;

    /**
     * @var string $port rabbitmq port
     */
    public string $port;

    /**
     * @var string $user rabbitmq user
     */
    public string $user;

    /**
     * @var string $password rabbitmq password
     */
    public string $password;

    /**
     * @var string $vhost rabbitmq vhost
     */
    public string $vhost;

    /**
     * @var string $exchange rabbitmq exchange
     */
    public string $exchange;

    /**
     * @var string $queue rabbitmq queue
     */
    public string $queue;

    /**
     * @var ?AMQPStreamConnection $connection rabbitmq connection
     */
    public ?AMQPStreamConnection $connection = null;
}