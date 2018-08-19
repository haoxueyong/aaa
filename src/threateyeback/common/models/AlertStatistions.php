<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%alert_statistions}}".
 *
 * @property integer $created_at
 * @property integer $updated_at
 */
class AlertStatistions extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%alert_statistics}}';
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

    //告警趋势F
    public function alertTrend() {
        $data = self:: find()->asArray()->select('statistics_time,alert_count')->orderBy('id DESC')->all();
        return $data;
    }

    //保存分析出的风险资产的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
