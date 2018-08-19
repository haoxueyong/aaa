<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class BehEX extends ActiveRecord {

    /**
     * BehEX model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%behEX}}';
    }

}
