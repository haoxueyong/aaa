<?php

namespace common\models;

use Yii;

class SocketConnection {

    public static $fp = false;

    public static function getContent($reConnection = false) {
        if (!self::$fp || $reConnection != false) {
            error_reporting(E_ERROR);
            $ssl = Yii::$app->params['ssl'];
            $socketUrl = Yii::$app->params['socketUrl'];
            $stream_context = stream_context_create($ssl);
            // $date = date('Y-m-d H:i:s',time());
            // echo "[{$date}]{$socketUrl}\n";
            self::$fp = stream_socket_client($socketUrl, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream_context);
            stream_set_blocking(self::$fp, 0);
        }
        return self::$fp;
    }

}
