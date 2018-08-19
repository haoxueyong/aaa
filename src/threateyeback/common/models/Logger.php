<?php

namespace common\models;

use Yii;

class Logger {

    /**
     * Logger model
     *
     */
    public static function info($msg, $path = 'run') {
        self::put_contents($msg, $path, 'info');
    }

    public static function warning($msg, $path = 'warning') {
        self::put_contents($msg, $path, 'warning');
    }

    private static function put_contents($msg, $path, $level) {
        if (is_object($msg)) {
            if (isset($msg->attributes)) {
                $msg = json_encode($msg->attributes);
            } else {
                $msg = get_class($msg);
            }
        } elseif (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $path = Yii::$app->params['logPath'] . '/' . $path . '/' . date('Ym', time());
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $logFilePath = $path . '/' . date('Ymd', time()) . '.log';
        $msg = '[' . date('Y-m-d H:i:s', time()) . '][' . $level . ']' . $msg;
        file_put_contents($logFilePath, $msg . "\n\r", FILE_APPEND);
    }

}
