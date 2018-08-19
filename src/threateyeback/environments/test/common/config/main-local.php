<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=iconnect',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
        'mq' => [
            'class' => 'common\components\RabbitMQ',
            'host' => '192.168.1.20',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'exchange' => 'SyslogMatcher',
            'exchange_type' => 'fanout',
        ],
        'ResultClient' => [
            'class' => 'common\components\ResultClient',
            'protocol' => 'http',
            'host' => '192.168.1.20',
            'port' => 5000,
            'Authorization' => 'Basic YWRtaW46Y3liZXJodW50',
            'ssl_verify' => false,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'database' => 1,
            ],
        ],
    ],
];
