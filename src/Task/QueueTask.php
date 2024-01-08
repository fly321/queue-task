<?php

namespace Fly321\QueueTask\Task;

use Fly321\QueueTask\Entity\RabbitMqEntity;
use Fly321\QueueTask\Service\AMQPService;
use Fly321\QueueTask\Service\AMQPServiceImpl;

final class QueueTask
{
    protected AMQPService $service;
    protected ?string $exchange = null;
    protected ?string $queue = null;

    public function __construct(RabbitMqEntity $rabbitMqEntity)
    {
        $this->service = AMQPServiceImpl::getInstance($rabbitMqEntity);
    }

    /**
     * 绑定交换机队列名
     * @param string $queue
     * @param string $exchange
     * @param bool $nowait
     * @param string $routing_key
     * @return void
     */
    public function queueBind(
        string $exchange,
        string $queue,
        string $routing_key = '',
        bool   $nowait = false
    ): void
    {
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->service->queueBind($exchange, $queue, $routing_key, $nowait);
    }

    /**
     * 发送消息
     * @param string $body
     * @param string $routing_key
     * @param bool $mandatory
     * @param bool $immediate
     * @return void
     */
    public function queueSend(
        string $body,
        string $routing_key = '',
        bool   $mandatory = false,
        bool   $immediate = false
    ): void
    {
        $this->service->basicPublish($body, $this->exchange, $routing_key, $mandatory, $immediate);
    }

    /**
     * 消费队列
     * @param string $queue
     * @param callable $callback
     * @param string $consumer_tag
     * @param bool $no_local
     * @param bool $no_ack
     * @param bool $exclusive
     * @param bool $nowait
     * @return void
     */
    public function consume(
        string $queue,
        callable $callback,
        string $consumer_tag = '',
        bool $no_local = false,
        bool $no_ack = false,
        bool $exclusive = false,
        bool $nowait = false
    ): void
    {
        $this->service->basicConsume($queue, $callback, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait);
    }
}