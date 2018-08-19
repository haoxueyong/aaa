<?php

namespace common\components;

use Yii;
use yii\base\Component;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ extends Component {

    public $host;
    public $port;
    public $user;
    public $password;
    public $exchange;
    public $exchange_type;
    public static $channel = false;
    public static $connection = false;

    public function getConnection($reConnection = false) {
        if (!self::$connection || $reConnection != false) {
            self::$connection = new AMQPStreamConnection(
                $this->host, $this->port, $this->user, $this->password
            );
        }
        return self::$connection;
    }

    public function getChannel($reConnection = false) {
        if (!self::$channel || $reConnection != false) {
            $connection = $this->getConnection($reConnection);
            self::$channel = $connection->channel();
        }
        return self::$channel;
    }

    public function readMessage($queueName = '', $callback) {
        $channel = $this->getChannel();
        //var_dump($queueName);die;
        $channel->exchange_declare($this->exchange, $this->exchange_type, false, false, true);
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->queue_bind($queueName, $this->exchange);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $channel->basic_consume($queueName, '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
        // $channel->close();
        // $connection->close();
    }

    public function sendMessage($queueName = '', $msg) {
        $channel = $this->getChannel();
        $channel->exchange_declare($this->exchange, $this->exchange_type, false, false, true);
        $channel->queue_declare($queueName, false, false, false, true);
        $channel->queue_bind($queueName, $this->exchange);
        $msg = new AMQPMessage($msg);
        $channel->basic_publish($msg, '', $queueName);
    }

}
