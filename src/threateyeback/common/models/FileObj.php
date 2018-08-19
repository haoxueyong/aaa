<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class FileObj extends ActiveRecord {

    /**
     * File model
     *
     * @property integer $id
     */
    const IsBad = 1;
    const IsWhite = 2;
    const WhiteList = 3;
    const BlackList = 4;

    public static function tableName() {
        return '{{%file}}';
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

    public function save($runValidation = true, $attributeNames = null) {
        $ret = parent::save();
        Yii::$app->cache->delete("config");
        return $ret;
    }

    public function delete() {
        $ret = parent::delete();
        Yii::$app->cache->delete("config");
        return $ret;
    }

}
