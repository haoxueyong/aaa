<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\IPObj;
use common\models\Alert;

class IPAlert extends ActiveRecord {

    /**
     * IPAlert model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%IP_alert}}';
    }

    public static function addIPList($alert) {
        foreach ($alert->AlertIPList as $key => $IPArr) {
            if ($IPArr['IPType'] == IPObj::OK) {
                continue;
            }
            $ipObj = IPObj::find()->where(['IP' => $IPArr['IP']])->one();
            if (empty($ipObj)) {
                $ipObj = new IPObj();
                $ipObj->IP = $IPArr['IP'];
            }
            $ipObj->IPType = $IPArr['IPType'];
            $ipObj->save();
            $ip_alert = IPAlert::find()->where(['IPID' => $ipObj->id, 'AlertID' => $alert->id])->one();
            if (empty($ip_alert)) {
                $ip_alert = new IPAlert();
                $ip_alert->IPID = $ipObj->id;
                $ip_alert->AlertID = $alert->id;
            }
            if ($alert->IsSolveBy3rd == 1) {
                $ip_alert->IsSolveBy3rd = 1;
                $ip_alert->status = 2;
            }
            $ip_alert->EventID = $IPArr['EventID'];
            $ip_alert->Detail = $IPArr['Detail'];
            $ip_alert->save();
        }
    }

    public static function change($type, $ipArr) {
        if ($type == 'setWhite') {
            $ipObj = IPObj::find()->where(['IP' => $ipArr['IP']])->one();
            $ipObj->IPType = IPObj::OK;
            $ipObj->save();
            self::updateAll(['status' => 3], 'IPID = ' . $ipObj->id);
        } else {
            self::updateAll(['status' => 2], 'id = ' . $ipArr['rid']);
        }
    }

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $jsonName = [
            'Detail',
        ];
        $value = parent::__get($name);
        if (in_array($name, $jsonName)) {
            $value = json_decode($value, true);
        }
        return $value;
    }

}
