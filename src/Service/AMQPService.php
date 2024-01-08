<?php

namespace Fly321\QueueTask\Service;

use Fly321\QueueTask\Entity\RabbitMqEntity;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

interface AMQPService
{
    /**
     * 获取实例对象 AMQPService
     *
     * @param RabbitMqEntity $rabbitMqEntity 兔mq实体对象
     * @return AMQPService
     */
    public static function getInstance(RabbitMqEntity $rabbitMqEntity): AMQPService;

    /**
     * 获取连接对象 AMQPStreamConnection
     *
     * @return AMQPStreamConnection
     */
    public function getAMQPStreamConnection(): AMQPStreamConnection;

    /**
     * 获取通道对象 AMQPChannel
     *
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel;


    /**
     * 声明一个队列
     * @param string $name 队列名称
     * @param bool $passive 是否为被动模式，默认为false
     * @param bool $durable 是否持久化，默认为true
     * @param bool $exclusive 是否独占使用该队列，默认为false
     * @param bool $auto_delete 是否自动删除队列，默认为false
     * @return AMQPChannel
     */
    public function queueDeclare(
        string $name,
        bool $passive = false,
        bool $durable = true,
        bool $exclusive = false,
        bool $auto_delete = false
    ): AMQPChannel;

    /**
     * 声明一个交换机
     * @param string $name 交换机名称
     * @param string $type 交换机类型，默认为direct
     * @param bool $passive 是否为被动模式，默认为false
     * @param bool $durable 是否持久化，默认为true
     * @param bool $auto_delete 是否自动删除交换机，默认为false
     * @return AMQPChannel
     */
    public function exchangeDeclare(
        string $name,
        string $type = 'direct',
        bool $passive = false,
        bool $durable = true,
        bool $auto_delete = false
    ): AMQPChannel;

    /**
     * 队列绑定交换机
     * @param string $exchange 交换机名称
     * @param string $queue 队列名称
     * @param string $routing_key
     * @param bool $nowait
     * @return AMQPChannel
     */
    public function queueBind(
        string $exchange,
        string $queue,
        string $routing_key = '',
        bool $nowait = false
    ): AMQPChannel;

    /**
     * 发布消息
     * @param string $body 消息体
     * @param string $exchange
     * @param string $routing_key
     * @param bool $mandatory
     * @param bool $immediate
     * @return void
     */
    public function basicPublish(
        string $body,
        string $exchange,
        string $routing_key = '',
        bool $mandatory = false,
        bool $immediate = false
    ): void;

    /**
     * 消费消息
     * @param string $queue
     * @param callable $callback
     * @param string $consumer_tag
     * @param bool $no_local
     * @param bool $no_ack
     * @param bool $exclusive
     * @param bool $nowait
     * @return void
     */
    public function basicConsume(
        string $queue,
        callable $callback,
        string $consumer_tag = '',
        bool $no_local = false,
        bool $no_ack = false,
        bool $exclusive = false,
        bool $nowait = false
    ): void;
}