<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ShareTag extends ActiveRecord {

    /**
     * ShareTag model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%share_tag}}';
    }

}
