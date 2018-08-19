<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class IPEX extends ActiveRecord {

    /**
     * IPEX model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%IPEX}}';
    }

}
