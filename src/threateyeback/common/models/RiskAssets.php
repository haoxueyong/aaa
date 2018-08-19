<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%risk_assets}}".
 *
 * @property integer $created_at
 * @property integer $updated_at
 */
class RiskAssets extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%risk_assets}}';
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

    //风险资产
    public function riskAssets() {
        $data = self:: find()->where(['AND', ['<', 'statistics_time', date("Y-m-d", time())], ['>=', 'statistics_time', date("Y-m-d", strtotime("-7 day"))]])->groupBy('statistics_time')->asArray()->limit(7)->orderBy('id DESC')->all();
        foreach ($data as $key => $value) {
            $data[$key]['statistics_time'] = substr($value['statistics_time'], 5);
            $data[$key]['alert_details'] = json_decode($value['alert_details']);
        }
        $return_data = array_reverse($data);
        return $return_data;
    }

    //保存分析出的风险资产的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
