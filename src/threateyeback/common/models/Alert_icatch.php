<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\FileAlert;
use common\models\IPAlert;
use common\models\Email;

class Alert extends ActiveRecord {

    /**
     * Alert model
     *
     * @property integer $id
     */
    const Type_none = 0;
    const Type_File = 1;
    const Type_IP = 2;
    const Type_URL = 3;
    const Type_REG = 4;
    const Type_EX = 5;
    const Type_Loophole = 6;

    public static function tableName() {
        return '{{%alert}}';
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

    public static function receive($json) {
        $ret = false;
        if (isset($json['AlertID'])) {
            $ret = 'update';
            $alert = self::find()->where(['AlertID' => $json['AlertID']])->one();
            if (empty($alert)) {
                $alert = new Alert();
                $alert->AlertID = $json['AlertID'];
                $alert->AlertType = array_key_exists('AlertType', $json) ? $json['AlertType'] : 0;
                $ret = 'add';
            }
            $alert->Timestamp = array_key_exists('Timestamp', $json) ? $json['Timestamp'] : time();
            $alert->SensorID = array_key_exists('SensorID', $json) ? $json['SensorID'] : 0;
            $alert->SrcType = array_key_exists('SrcType', $json) ? $json['SrcType'] : 0;
            $alert->Point = $json['AlertType'] > 3 ? 0 : 100;
            if (array_key_exists('IsSolveBy3rd', $json) && $json['IsSolveBy3rd'] == 1) {
                $alert->IsSolveBy3rd = 1;
                $alert->status = 2;
            }
            $alert->ExceptionAlertList = array_key_exists('ExceptionAlertList', $json) ? $json['ExceptionAlertList'] : [];
            $alert->AlertFileList = array_key_exists('AlertFileList', $json) ? $json['AlertFileList'] : [];
            $alert->AlertIPList = array_key_exists('AlertIPList', $json) ? $json['AlertIPList'] : [];
            $alert->AlertURLList = array_key_exists('AlertURLList', $json) ? $json['AlertURLList'] : [];
            $alert->HitRegulationList = array_key_exists('HitRegulationList', $json) ? $json['HitRegulationList'] : [];
            if (count($alert->HitRegulationList) > 0) {
                $alert->Label = '[' . $alert->HitRegulationList[0]['RegID'] . '] ' . $alert->HitRegulationList[0]['RegDesc'];
            }
            $EventList = array_key_exists('EventList', $json) ? $json['EventList'] : [];
            foreach ($EventList as &$event) {
                self::objFill($event['SrcObj']);
                self::objFill($event['TarObj']);
            }
            $alert->EventList = $EventList;
            $alert->save();
            if ($ret == 'add') {
                Email::addAlert($alert);
            }
        }
        return $ret;
    }

    public static function commandResult($result) {
        $Types = [
            Command::MSG_M2E_RESLOVE_OBJECT,
            Command::MSG_M2E_UPDATE_WHITELIST_1,
            Command::MSG_M2E_RESLOVE_ALERT,
            Command::MSG_M2E_IGNORE_ALERT,
            Command::MSG_M2E_REMOVE_WHITELIST,
        ];
        $command = Command::find()->where(['CommandID' => $result->CommandID, 'Type' => $Types])->one();
        if (!empty($command)) {
            $json = $result->data;
            $data = $command->data;
            if (array_key_exists('Result', $json)) {
                $data['Result'] = $json;
                $command->data = $data;
                $command->status = ($json['Result'] == 0 ? Command::STATUS_SUCCESS : Command::STATUS_ERROR);
                $command->save();
            }
        }
        return $command;
    }

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $jsonName = [
            'ExceptionAlertList',
            'AlertFileList',
            'AlertIPList',
            'AlertURLList',
            'HitRegulationList',
            'EventList',
        ];
        $value = parent::__get($name);
        if (in_array($name, $jsonName)) {
            $value = json_decode($value, true);
        }
        return $value;
    }

    public static function change($type, $alertArr) {
        if ($type == 'setWhiteBeh') {
            self::updateAll(['status' => 4], 'id = ' . $alertArr['rid']);
        } else {
            self::updateAll(['status' => 2], 'id = ' . $alertArr['rid']);
        }
    }

    public static function objFill(&$obj) {
        $TypeName = [
            'NONE' => '未知',
            'OBJ_PROCESS' => '进程',
            'OBJ_FILE' => '文件',
            'OBJ_WIN_REG_KEY' => '注册表',
            'OBJ_NETWORK_CONN' => '网络连接',
            'OBJ_WIN_VOLUME' => '盘符',
            'OBJ_WIN_USER_ACCOUNT' => '用户账户',
            'OBJ_WIN_SERVICE' => '服务',
            'OBJ_DNS' => 'DNS',
            'OBJ_NETWORK_SHARE' => '网络共享',
            'OBJ_ADDRESS' => 'IP地址',
            'OBJ_USER_LOGON' => '用户登录',
            'OBJ_USB_PLUG' => 'USB',
        ];

        if (!array_key_exists($obj['ObjType'], $TypeName)) {
            $obj['ObjType'] = 'NONE';
        }

        $obj['TypeName'] = $TypeName[$obj['ObjType']];
        $obj['id'] = uniqid();
        switch ($obj['ObjType']) {
            case 'OBJ_PROCESS':
                $obj['name'] = $obj['ProcessName'];
                $obj['id'] = md5($obj['ObjType'] . $obj['ImagePath']);
                break;
            case 'OBJ_FILE':
                $names = explode("\\", $obj['FilePath']);
                $obj['name'] = $names[count($names) - 1];
                $obj['id'] = md5($obj['ObjType'] . $obj['FilePath']);
                break;
            case 'OBJ_WIN_REG_KEY':
                $obj['name'] = $obj['Key'] . '/' . $obj['ValueName'];
                $obj['id'] = md5($obj['name']);
                break;
            case 'OBJ_NETWORK_CONN':
                $obj['name'] = $obj['RemoteIP'] . ':' . $obj['RemotePort'];
                $obj['id'] = md5($obj['name']);
                break;
            case 'OBJ_WIN_SERVICE':
                $obj['name'] = $obj['ServiceName'];
                $obj['id'] = md5($obj['ImagePath']);
                break;
            case 'OBJ_DNS':
                $obj['name'] = $obj['DomainName'];
                $obj['id'] = md5($obj['name']);
                break;
            default:
                $obj['name'] = $TypeName[$obj['ObjType']];
                break;
        }


        $obj['LongName'] = $obj['name'];

        if (mb_strlen($obj['name'], 'utf-8') != strlen($obj['name'])) {
            if (strlen($obj['name']) > 12) {
                $obj['name'] = mb_substr($obj['name'], 0, 11, 'utf-8') . '...';
            }
        } else {
            if (strlen($obj['name']) > 24) {
                $obj['name'] = substr($obj['name'], 0, 21) . '...';
            }
        }


        return $obj;
    }

    public function save($runValidation = true, $attributeNames = null) {
        $ret = parent::save();
        switch ($this->AlertType) {
            case self::Type_File:
                FileAlert::addFileList($this);
                break;
            case self::Type_IP:
                IPAlert::addIPList($this);
                break;
            case self::Type_URL:
                URLAlert::addURLList($this);
                break;
        }
        return $ret;
    }

}
