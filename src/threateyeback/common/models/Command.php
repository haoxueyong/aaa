<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\Sensor;
use common\models\Logger;

class Command extends ActiveRecord {

    /**
     * Command model
     *
     * @property integer $id
     */
    const MSG_BASE_LEN = 29;
    //sensor-manager
    const MSG_SENSOR_LOGON = "\x00"; //完成
    const MSG_S2M_COMMAND_RESULT = "\x01"; //完成
    const MSG_S2M_START_SCAN = "\x03"; //完成
    const MSG_S2M_FINISH_SCAN = "\x04"; //完成
    //engine-manager
    const MSG_E2M_COMMAND_RESULT = "\x21";
    const MSG_E2M_ALERT = "\x22";
    const MSG_E2M_ALERT_UPDATE = "\x23";
    const MSG_E2M_SENSOR_OFFLINE = "\x26"; //完成
    const MSG_E2M_HEART_BEAT = "\x27"; //完成
    //manager-sensor
    const MSG_M2S_HANDSHAKE = "\x40"; //完成
    const MSG_M2S_UPDATE = "\x41"; //完成
    const MSG_M2S_UNINIT = "\x42"; //完成
    const MSG_M2S_ISOLATE_UP = "\x43"; //完成
    const MSG_M2S_ISOLATE_DOWN = "\x44"; //完成
    const MSG_M2S_UPDATE_PROFILE = "\x45"; //完成
    const MSG_M2S_PAUSE_SENSOR = "\x46"; //完成
    const MSG_M2S_RESUME_SENSOR = "\x47"; //完成
    const MSG_M2S_SCAN = "\x48"; //完成
    const MSG_M2S_KILL_PROCESS = "\x4a"; //完成
    //manager-engine
    const MSG_M2E_HANDSHAKE = "\x50"; //完成
    const MSG_M2E_UPDATE_BASES = "\x52";
    const MSG_M2E_UPDATE_WHITELIST = "\x53";
    const MSG_M2E_UPDATE_WHITELIST_1 = "\x54"; //完成
    const MSG_M2E_IOC = "\x55"; //待定
    const MSG_M2E_MANAGER_LOGON = "\x56"; //完成
    const MSG_M2E_RESLOVE_ALERT = "\x57"; //完成
    const MSG_M2E_IGNORE_ALERT = "\x58"; //完成
    const MSG_M2E_HEART_BEAT = "\x59"; //完成
    const MSG_M2E_RESLOVE_OBJECT = "\x5a"; //完成
    const MSG_M2E_REMOVE_WHITELIST = "\x5b"; //完成
    const MSG_M2E_SEARCH = "\x5c";
    const MSG_SHELL_SETIP = "\x60";
    const MSG_SHELL_SET_SYSTEM_IP = "\x61";
    //FometType
    const BASIC = 0;
    const HAS_DATA = 1;
    const HAS_STATUS = 1;
    const STATUS_UNSENT = 0;
    const STATUS_SEND = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_ERROR = 3;

    public static function HANDSHAKE_TYPE($type = false) {
        $HANDSHAKE = [
            //MSG_M2S_HANDSHAKE
            self::MSG_SENSOR_LOGON => self::MSG_M2S_HANDSHAKE,
            self::MSG_S2M_COMMAND_RESULT => self::MSG_M2S_HANDSHAKE,
            self::MSG_S2M_START_SCAN => self::MSG_M2S_HANDSHAKE,
            self::MSG_S2M_FINISH_SCAN => self::MSG_M2S_HANDSHAKE,
            //MSG_M2E_HANDSHAKE
            self::MSG_E2M_COMMAND_RESULT => self::MSG_M2E_HANDSHAKE,
            self::MSG_E2M_ALERT => self::MSG_M2E_HANDSHAKE,
            self::MSG_E2M_SENSOR_OFFLINE => self::MSG_M2E_HANDSHAKE,
            self::MSG_E2M_HEART_BEAT => self::MSG_M2E_HEART_BEAT,
        ];
        if (empty($HANDSHAKE[$type])) {
            return false;
        }
        return $HANDSHAKE[$type];
    }

    public static function tableName() {
        return '{{%command}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    public static function uuid2bin($uuid = false) {
        if ($uuid === false || $uuid == '') {
            $uuid = self::createUuid();
        }
        return hex2bin(str_replace("-", "", $uuid));
    }

    public static function bin2uuid($bin_uuid) {
        $hex_str = bin2hex($bin_uuid);
        return self::createUuid($hex_str);
    }

    public static function createUuid($hex_str = false) {
        if ($hex_str === false) {
            $hex_str = md5(uniqid() . time());
        }
        $uuid = substr($hex_str, 0, 8) . "-"
                . substr($hex_str, 8, 4) . "-"
                . substr($hex_str, 12, 4) . "-"
                . substr($hex_str, 16, 4) . "-"
                . substr($hex_str, 20, 12);
        return $uuid;
    }

    public static function read() {
        $count = self::shellWork();
        $count += self::sensorWork();
        $count += self::alertWork();
        return $count;
    }

    public static function shellWork() {
        $Types = [
            Command::MSG_SHELL_SETIP,
            Command::MSG_SHELL_SET_SYSTEM_IP,
        ];
        $commandList = self::find()->where(['status' => [self::STATUS_UNSENT], 'Type' => $Types])->all();
        foreach ($commandList as $key => $command) {
            $data = $command->data;
            exec($data['shell'], $data['res']);
            $command->status = self::STATUS_SEND;
            if ($command->Type == Command::MSG_SHELL_SET_SYSTEM_IP) {
                if (count($data['res']) > 0 && array_key_exists('back_shell', $data)) {
                    $command->status = self::STATUS_ERROR;
                    exec($data['back_shell'], $data['back_res']);
                } else {
                    $command->status = self::STATUS_SUCCESS;
                }
            }
            $command->data = $data;
            $command->save();
        }
        $count = count($commandList);
        if ($count > 0) {
            Logger::info("Handle command:  {$count}", 'shellWork');
        }
        return $count;
    }

    public static function alertWork() {
        $Types = [
            Command::MSG_M2E_RESLOVE_OBJECT,
            Command::MSG_M2E_UPDATE_WHITELIST_1,
            Command::MSG_M2E_RESLOVE_ALERT,
            Command::MSG_M2E_IGNORE_ALERT,
            Command::MSG_M2E_REMOVE_WHITELIST,
        ];
        $timestamp = time() - 60;
        $commandList = self::find()->where(['status' => [self::STATUS_SEND, self::STATUS_UNSENT], 'Type' => $Types])->andWhere(['<', 'updated_at', $timestamp])->all();
        foreach ($commandList as $key => $command) {
            $command->send();
            $command->updated_at = time();
            $command->save();
            if (($key + 1) < count($commandList)) {
                usleep(20000);
            }
        }
        $count = count($commandList);
        if ($count > 0) {
            Logger::info("Handle command:  {$count}", 'AlertWork');
        }
        return $count;
    }

    public static function sensorWork() {
        $SensorTypeList = [
            self::MSG_M2S_UPDATE,
            self::MSG_M2S_UNINIT,
            self::MSG_M2S_ISOLATE_UP,
            self::MSG_M2S_ISOLATE_DOWN,
            self::MSG_M2S_UPDATE_PROFILE,
            self::MSG_M2S_PAUSE_SENSOR,
            self::MSG_M2S_RESUME_SENSOR,
            self::MSG_M2S_SCAN,
        ];
        $timestamp = time() - 20;
        $commandList = self::find()->where(['status' => self::STATUS_SEND, 'Type' => $SensorTypeList])->andWhere(['<', 'created_at', $timestamp])->all();
        foreach ($commandList as $key => $command) {
            $sensor = Sensor::find()->where(['SensorID' => $command->SensorID])->one();
            if (isset($sensor)) {
                if ($command->Type == self::MSG_M2S_UNINIT) {
                    $sensor->status = Sensor::UNINIT;
                    $sensor->work = Sensor::WORK_SUCCESS;
                } else {
                    $sensor->work = Sensor::WORK_ERROR;
                }
                $sensor->save();
            }
            $command->status = self::STATUS_UNSENT;
            $command->save();
        }
        $count = count($commandList);
        if ($count > 0) {
            Logger::info("Handle command:  {$count}", 'SensorWork');
        }
        return $count;
    }

    public function setCommandID4bin($bin) {
        $this->CommandID = self::bin2uuid($bin);
        return $this;
    }

    public function getCommandIDBin() {
        return self::uuid2bin($this->CommandID);
    }

    public function getCommandBin() {
        $data = empty($this->data) ? '' : json_encode($this->data);
        return $this->Type . pack("i", self::MSG_BASE_LEN + strlen($data)) . pack("Q", $this->SensorID) . $this->getCommandIDBin() . $data;
    }

    public function analy($commandBin) {
        $this->Type = substr($commandBin, 0, 1);
        $this->SensorID = unpack("Q", substr($commandBin, 5, 8))[1];
        $this->setCommandID4bin(substr($commandBin, 13, 16));
        if (strlen($commandBin) > self::MSG_BASE_LEN) {
            $this->data = substr($commandBin, self::MSG_BASE_LEN);
        }
        $this->splitWork();
        $this->reply();
        return $this;
    }

    public function reply() {
        $reply = new Command();
        $reply->Type = self::HANDSHAKE_TYPE($this->Type);
        $reply->SensorID = $this->SensorID;
        $reply->CommandID = $this->CommandID;

        if ($reply->Type === false) {
            Logger::info('noReply');
            return $this;
        }
        Logger::info('ReplyType:' . bin2hex($reply->Type));
        Logger::info('ReplySensorID:' . $reply->SensorID);
        Logger::info('ReplyData:' . json_encode($reply->data));
        $reply->send();
        return $this;
    }

    public function send() {
        if (empty($this->CommandID)) {
            $this->CommandID = self::createUuid();
        }
        $fp = SocketConnection::getContent();
        if (!$fp) {
            $this->status = self::STATUS_UNSENT;
        } else {
            $data = $this->getCommandBin();
            fwrite($fp, $data);
            $this->status = self::STATUS_SEND;
        }
        return $this;
    }

    public function save($runValidation = true, $attributeNames = null) {
        if (empty($this->CommandID)) {
            $this->CommandID = self::createUuid();
        }
        return parent::save();
    }

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $jsonName = [
            'data',
        ];
        $value = parent::__get($name);
        if (in_array($name, $jsonName)) {
            $value = json_decode($value, true);
        }
        return $value;
    }

    public function splitWork() {
        Logger::info('CommandType:' . bin2hex($this->Type));
        Logger::info('CommandSensorID:' . $this->SensorID);
        Logger::info('CommandData:' . json_encode($this->data));

        switch ($this->Type) {
            case self::MSG_SENSOR_LOGON:
                $sensor = Sensor::logon($this);
                $this->SensorID = $sensor->SensorID;
                break;
            case self::MSG_E2M_SENSOR_OFFLINE:
                Sensor::offLine($this->data);
                break;
            case self::MSG_S2M_COMMAND_RESULT:
                Sensor::commandResult($this);
                break;
            case self::MSG_S2M_START_SCAN:
                Sensor::scan($this->SensorID, Sensor::SCAN_ING);
                break;
            case self::MSG_S2M_FINISH_SCAN:
                Sensor::scan($this->SensorID, Sensor::SCAN_NO);
                break;
            case self::MSG_E2M_ALERT:
            case self::MSG_E2M_ALERT_UPDATE:
                Alert::receive($this->data);
                break;
            case self::MSG_E2M_COMMAND_RESULT:
                Alert::commandResult($this);
                break;
            default:
                Logger::info('noWork');
        }
        return $this;
    }

}
