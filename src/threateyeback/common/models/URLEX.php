<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class URLEX extends ActiveRecord {

    /**
     * URLEX model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%URLEX}}';
    }

}
