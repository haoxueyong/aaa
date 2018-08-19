<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ip_segment}}".
 *
 * @property integer $id
 * @property string $ip_segment
 * @property integer $net_mask
 * @property integer $created_at
 * @property integer $updated_at
 */
class IpSegment extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%ip_segment}}';
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

    //获取所有的网段配置
    public static function getIpSegment() {
        $ip_segment = self::find()->asArray()->select('id,ip_segment,net_mask')->all();
        return $ip_segment;
    }

    //设置网络IP段
    public static function setIpSegment($net_parames) {
        $conf = self::find()->where(['and', ['=', 'ip_segment', $net_parames['ip_segment']], ['=', 'net_mask', $net_parames['net_mask']]])->asArray()->all();
        if (!empty($conf)) {
            return '请勿重复添加';
        }
        //添加
        $IpSegment = new IpSegment();
        $IpSegment->ip_segment = $net_parames['ip_segment'];
        $IpSegment->net_mask = $net_parames['net_mask'];
        $IpSegment->save();
        //查询配置完之后的所有的配置，并存入reids
        $result = self::find()->select('ip_segment,net_mask')->asArray()->all();
        Yii::$app->cache->set("IpSegment", json_encode($result));
        return true;
    }

    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
