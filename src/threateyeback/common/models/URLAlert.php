<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\URLObj;
use common\models\Alert;

class URLAlert extends ActiveRecord {

    /**
     * URLAlert model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%URL_alert}}';
    }

    public static function addURLList($alert) {
        foreach ($alert->AlertURLList as $key => $URLArr) {
            if ($URLArr['URLType'] == URLObj::OK) {
                continue;
            }

            $urlObj = URLObj::find()->where(['URL' => $URLArr['URL']])->one();
            if (empty($urlObj)) {
                $urlObj = new URLObj();
                $urlObj->URL = $URLArr['URL'];
            }
            $urlObj->URLType = $URLArr['URLType'];
            $urlObj->save();

            $url_alert = URLAlert::find()->where(['URLID' => $urlObj->id, 'AlertID' => $alert->id])->one();
            if (empty($url_alert)) {
                $url_alert = new URLAlert();
                $url_alert->URLID = $urlObj->id;
                $url_alert->AlertID = $alert->id;
            }
            if ($alert->IsSolveBy3rd == 1) {
                $url_alert->IsSolveBy3rd = 1;
                $url_alert->status = 2;
            }
            $url_alert->EventID = $URLArr['EventID'];
            $url_alert->Detail = $URLArr['Detail'];
            $url_alert->save();
        }
    }

    public static function change($type, $URLArr) {
        if ($type == 'setWhite') {
            $URLObj = URLObj::find()->where(['URL' => $URLArr['URL']])->one();
            $URLObj->URLType = URLObj::OK;
            $URLObj->save();
            self::updateAll(['status' => 3], 'URLID = ' . $URLObj->id);
        } else {
            self::updateAll(['status' => 2], 'id = ' . $URLArr['rid']);
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
