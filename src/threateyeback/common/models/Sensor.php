<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Sensor extends ActiveRecord {

    /**
     * Sensor model
     *
     * @property integer $id
     */
    //status const
    const UNINIT = 0;
    const ON_LINE = 1;
    const OFF_LINE = 2;
    //pause const
    const PAUSE = 1;
    const RESUME = 0;
    //isolate const
    const ISOLATE_UP = 1;
    const ISOLATE_DOWN = 0;
    //work const
    const WORK_NO = 0;
    const WORK_ING = 1;
    const WORK_SUCCESS = 2;
    const WORK_ERROR = 3;
    //scan const
    const SCAN_NO = 0;
    const SCAN_ING = 1;

    public static function tableName() {
        return '{{%sensor}}';
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

    public function getGroups() {
        return $this->hasMany(Group::className(), ['id' => 'gid'])
                        ->viaTable('group_sensor', ['sid' => 'id']);
    }

    public static function logon($command) {
        $data = $command->data;
        $commandOld = Command::find()->where(['CommandID' => $command->CommandID])->one();
        if (isset($commandOld)) {
            $command = $commandOld;
            $command->data = $data;
        }
        $SensorID = $command->SensorID;
        if ($SensorID == 0) {
            $sensor = new sensor();
        } else {
            $sensor = self::find()->where(['SensorID' => $SensorID])->one();
            if (empty($sensor)) {
                $sensor = new sensor();
                $sensor->SensorID = $SensorID;
            }
        }
        $sensor->status = self::ON_LINE;
        $sensor->ComputerName = $data['ComputerName'];
        $sensor->SensorVersion = $data['SensorVersion'];
        $sensor->OSType = $data['OSType'];
        $sensor->IP = $data['IP'];
        $sensor->Domain = empty($data['Domain']) ? '' : $data['Domain'];
        $OSTypeArr = explode(",", $sensor->OSType);
        $OSbits = explode(" ", trim($OSTypeArr[4]))[0];
        $sensor->OSTypeShort = trim($OSTypeArr[0]) . ' (' . $OSbits . '-bit)';
        //暂时没有ProfileVersion
        // $sensor->ProfileVersion = $data['ProfileVersion'];
        $sensor->ProfileVersion = $data['SensorVersion'];
        $sensor->Timestamp = $data['Timestamp'];
        $sensor->save();
        $command->SensorID = $sensor->SensorID;
        if ($SensorID == 0) {
            $command->save();
        }
        return $sensor;
    }

    public static function scan($SensorID, $scan) {
        $sensor = self::find()->where(['SensorID' => $SensorID])->one();
        if (!empty($sensor)) {
            $sensor->scan = $scan;
            $sensor->save();
        }
        return $sensor;
    }

    public static function offLine($data) {
        $SensorIDList = $data;
        self::updateAll(['status' => self::OFF_LINE], ['SensorID' => $SensorIDList]);
    }

    public static function offLineAll() {
        self::updateAll(['status' => self::OFF_LINE], ['status' => self::ON_LINE]);
    }

    public static function commandResult($result) {
        $command = Command::find()->where(['CommandID' => $result->CommandID])->one();

        if (!empty($command)) {
            $sensor = self::find()->where(['SensorID' => $command->SensorID])->one();
            $json = $result->data;
            if (!empty($sensor) && array_key_exists('Result', $json)) {
                $command->data['Result'] = $result->data;
                $command->status = ($json['Result'] == 0 ? Command::STATUS_SUCCESS : Command::STATUS_ERROR);
                $command->save();
                $sensor->work = ($json['Result'] == 0 ? self::WORK_SUCCESS : self::WORK_ERROR);
                if (array_key_exists('SensorVersion', $json)) {
                    $sensor->SensorVersion = $json['SensorVersion'];
                }
                if (array_key_exists('Isolate', $json)) {
                    $sensor->isolate = $json['Isolate'];
                }
                if (array_key_exists('Pause', $json)) {
                    $sensor->pause = $json['Pause'];
                }
                // if(array_key_exists('ProfileVersion',$json))
                // {
                //     $sensor->ProfileVersion = $json['ProfileVersion'];
                // }
                $sensor->save();
            }
        }
        return $sensor;
    }

    public static function unpackSensorID($SensorIDBin) {
        return unpack("Q", $SensorIDBin)[1];
    }

    public function getSensorIDBin() {
        return pack("Q", $this->SensorID);
    }

    public function save($runValidation = true, $attributeNames = null) {
        $ret = parent::save();
        if (empty($this->SensorID)) {
            $this->SensorID = unpack("Q", pack("i*", $this->id, Yii::$app->params['orgId']))[1];
            $ret = parent::save();
        }
        return $ret;
    }

}
