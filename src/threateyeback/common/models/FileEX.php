<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class FileEX extends ActiveRecord {

    /**
     * FileEX model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%fileEX}}';
    }

}
