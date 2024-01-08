<?php

namespace Fly321\QueueTask\Service;

use Exception as ExceptionAlias;
use Fly321\QueueTask\Entity\RabbitMqEntity;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPServiceImpl implements AMQPService
{
    private function __construct(RabbitMqEntity $rabbitMqEntity)
    {
        $this->rabbitMqEntity = $rabbitMqEntity;
    }
    private function __clone()
    {
    }

    public static ?AMQPService $instance = null;
    public RabbitMqEntity $rabbitMqEntity;

    public ?AMQPChannel $channel = null;

    /**
     * @var string|null $exchange 交换机名称
     */
    public ?string $exchange = null;

    /**
     * @var string|null $queue 队列名称
     */
    public ?string $queue = null;

    /**
     * @inheritDoc
     * @throws ExceptionAlias
     */
    public function getAMQPStreamConnection(): AMQPStreamConnection
    {
        if (!$this->rabbitMqEntity->connection instanceof AMQPStreamConnection) {
            $this->rabbitMqEntity->connection = new AMQPStreamConnection(
                $this->rabbitMqEntity->host,
                $this->rabbitMqEntity->port,
                $this->rabbitMqEntity->user,
                $this->rabbitMqEntity->password,
                $this->rabbitMqEntity->vhost
            );
        }
        return $this->rabbitMqEntity->connection;
    }

    /**
     * @inheritDoc
     */
    public static function getInstance(RabbitMqEntity $rabbitMqEntity): AMQPService
    {
        if (self::$instance instanceof AMQPService) {
            return self::$instance;
        }
        self::$instance = new self($rabbitMqEntity);
        return self::$instance;
    }

    /**
     * @inheritDoc
     * @throws ExceptionAlias
     */
    public function getChannel(): AMQPChannel
    {
        if (!$this->channel instanceof AMQPChannel) {
            $this->channel = $this->getAMQPStreamConnection()->channel();
        }
        return $this->channel;
    }


    /**
     * @inheritDoc
     * @throws ExceptionAlias
     */
    public function queueDeclare(
        string $name,
        bool $passive = false,
        bool $durable = true,
        bool $exclusive = false,
        bool $auto_delete = false
    ): AMQPChannel
    {
        $this->getChannel()->queue_declare($name, $passive, $durable, $exclusive, $auto_delete);
        $this->exchange = $name;
        return $this->getChannel();
    }

    /**
     * @throws ExceptionAlias
     */
    public function exchangeDeclare(
        string $name,
        string $type = AMQPExchangeType::DIRECT,
        bool $passive = false,
        bool $durable = true,
        bool $auto_delete = false
    ): AMQPChannel
    {
        $this->queue = $name;
        $this->getChannel()->exchange_declare($name, $type, $passive, $durable, $auto_delete);
        return $this->getChannel();
    }

    /**
     * @throws ExceptionAlias
     */
    public function queueBind(
        string $exchange,
        string $queue,
        string $routing_key = '',
        bool $nowait = false
    ): AMQPChannel
    {
        if ($this->queue !== $queue) {
            $this->queueDeclare($queue);
        }
        if ($this->exchange !== $exchange) {
            $this->exchangeDeclare($exchange);
        }
        $this->getChannel()->queue_bind($queue, $exchange);
        return $this->getChannel();
    }

    /**
     * @throws ExceptionAlias
     */
    public function basicPublish(
        string $body,
        string $exchange,
        string $routing_key = '',
        bool $mandatory = false,
        bool $immediate = false
    ): void
    {
        $this->getChannel()->basic_publish(
            $this->getAmqpMessage($body),
            $exchange,
            $routing_key,
            $mandatory,
            $immediate
        );
    }

    /**
     * 返回AMQPMessage
     * @param string $body
     * @return AMQPMessage
     */
    private function getAmqpMessage(string $body): AMQPMessage
    {
        return new AMQPMessage($body, [
            'content_type' => 'text/plain',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
    }

    /**
     * @throws ExceptionAlias
     */
    public function basicConsume(
        string $queue,
        callable $callback,
        string $consumer_tag = '',
        bool $no_local = false,
        bool $no_ack = false,
        bool $exclusive = false,
        bool $nowait = false,
    ): void
    {
        $this->getChannel()
            ->basic_consume(
                $queue,
                $consumer_tag,
                $no_local,
                $no_ack,
                $exclusive,
                $nowait,
                $callback
            );
        $this->getChannel()->consume();
    }

    /**
     * @throws ExceptionAlias
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->getAMQPStreamConnection()->close();
    }


}