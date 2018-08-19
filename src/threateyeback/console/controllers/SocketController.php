<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Sensor;
use common\models\Command;
use common\models\Logger;
use common\models\SocketConnection;
use common\models\UserLog;
use yii\helpers\Json;

const TIMEOUT = 60;

/**
 * Test controller
 */
class SocketController extends Controller {

    public $runing = false;
    public $HEART_BEAT_TIME = 0;
    public $EngineStatus = false;

    public function onMessage($commandBin) {
        $command = new Command();
        $command->analy($commandBin);
    }

    public function onError() {
        
    }

    public function onClose() {
        Logger::info('Closed');
        if ($this->EngineStatus) {
            $userLog = new UserLog();
            $userLog->type = UserLog::Type_EngineDown;
            $userLog->status = UserLog::Success;
            $userLog->info = 'The engine has stopped';
            $userLog->username = '';
            $userLog->save();
            $this->EngineStatus = false;
        }
        Sensor::offLineAll();
        $this->actionRun();
    }

    public function onOpen() {
        $this->runing = true;
        Logger::info('Connected');
        if (!$this->EngineStatus) {
            $userLog = new UserLog();
            $userLog->type = UserLog::Type_EngineUP;
            $userLog->status = UserLog::Success;
            $userLog->info = 'The engine has started';
            $userLog->username = '';
            $userLog->save();
            $this->EngineStatus = true;
        }
    }

    public function M2E_MANAGER_LOGON() {
        $command = new Command();
        $command->Type = Command::MSG_M2E_MANAGER_LOGON;
        $command->SensorID = 0;
        $command->data = '';
        $command->send();
    }

    public function socketOpen() {
        $fp = SocketConnection::getContent(true);
        if (!$fp) {
            Logger::info('Connection failed');
            return false;
        } else {
            return true;
        }
    }

    public function hasProcess() {
        exec("ps aux | grep 'yii socket'", $output);
        $count = 0;
        foreach ($output as $key => $line) {
            $isMatched = preg_match('/(.*)\sphp\s(.*)\/yii\ssocket$/', $line, $matches);
            if ($isMatched) {
                $count++;
            }
        }
        if ($count > 1) {
            return true;
        }
        return false;
    }

    /**
     * index Socket
     *
     * @return 0
     */
    public function actionIndex() {
        if (!$this->hasProcess()) {
            $this->actionRun();
        }
        return 0;
    }

    /**
     * RunOld Socket
     *
     * @return 0
     */
    public function actionRunOld() {
        $Connected = $this->socketOpen();
        if ($Connected) {
            $this->onOpen();
            $this->M2E_MANAGER_LOGON();
            $onMSG = new onMSG('onMSG');
            $onMSG->start();
            while ($onMSG->runing) {
                if ($onMSG->res != false) {
                    if ($onMSG->res == 'closed') {
                        $onMSG->runing = false;
                        $this->onClose();
                    } else {
                        $this->onMessage($onMSG->res);
                    }
                    $onMSG->res = false;
                } else {
                    usleep(10000);
                }
            }
        } else {
            Logger::info((TIMEOUT / 2) . ' seconds after trying to connect');
            Sensor::offLineAll();
            sleep((TIMEOUT / 2));
            $this->actionRun();
        }
        return 0;
    }

    /**
     * Run Socket
     *
     * @return 0
     */
    public function actionRun() {
        Logger::info('Process has been opened');
        $pid = getmypid();
        $file = fopen(Yii::$app->params['confPath'] . '/socketPid', 'w');
        fwrite($file, $pid);
        fclose($file);
        $Connected = $this->socketOpen();
        if ($Connected) {
            $this->onOpen();
            $this->M2E_MANAGER_LOGON();
            $fp = SocketConnection::getContent();
            $this->HEART_BEAT_TIME = time();
            while ($this->runing) {
                if (!$fp || (time() - $this->HEART_BEAT_TIME) > TIMEOUT) {
                    $this->runing = false;
                    $this->onClose();
                    break;
                }
                $commandBin = fread($fp, 29);
                $commandData = "";
                $length = unpack("i", substr($commandBin, 1, 4))[1] - 29;
                if ($length > 0) {
                    $commandData = fread($fp, $length);
                    // while($line = fread($fp,1024)){
                    //     $commandData = $commandData.$line;
                    // }
                    $json = json_decode($commandData, true);
                    if (strlen($commandData) != $length) {
                        Logger::warning('Length error');
                        Logger::warning('command Type:' . bin2hex(substr($commandBin, 0, 1)));
                        Logger::warning('command Length:' . $length + 29);
                        Logger::warning('command True Length:' . strlen($commandData) + 29);
                        Logger::warning('command SensorID:' . unpack('Q', substr($commandBin, 5, 8))[1]);
                        Logger::warning('command Data:' . $commandData);
                        $commandBin = "";
                    } elseif (!$json) {
                        Logger::warning('JSON error');
                        Logger::warning('command Type:' . bin2hex(substr($commandBin, 0, 1)));
                        Logger::warning('command Length:' . $length);
                        Logger::warning('command SensorID:' . unpack('Q', substr($commandBin, 5, 8))[1]);
                        Logger::warning('command Data:' . $commandData);
                        $commandBin = "";
                    }
                }
                if (!empty($commandBin)) {
                    $this->onMessage($commandBin . $commandData);
                    $this->HEART_BEAT_TIME = time();
                } else {
                    usleep(10000);
                }
                Yii::$app->db->close();
            }
        } else {
            Logger::info((TIMEOUT / 2) . ' seconds after trying to connect');
            Sensor::offLineAll();
            sleep((TIMEOUT / 2));
            $this->actionRun();
        }
        return 0;
    }

}
