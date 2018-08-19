<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%safety_score}}".
 *
 * @property integer $id
 * @property string $statistics_time
 * @property integer $alert_count
 * @property integer $score
 * @property integer $created_at
 * @property integer $updated_at
 */
class Report extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%report}}';
    }

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

    //保存报表的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
