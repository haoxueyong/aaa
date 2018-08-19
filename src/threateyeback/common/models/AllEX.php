<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class AllEX extends ActiveRecord {

    /**
     * ALLEX model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%AllEX}}';
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
