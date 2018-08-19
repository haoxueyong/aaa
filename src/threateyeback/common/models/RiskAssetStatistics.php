<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%risk_asset_statistics}}".
 *
 */
class RiskAssetStatistics extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%risk_asset_statistics}}';
    }

    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ]
            ]
        ];
    }

    //获取风险资产top5
    public static function getRiskAssetTop5() {
        $RiskAssetTop5 = self::find()->asArray()->limit(5)->orderBy('count DESC')->select('id,asset_ip,count')->all();
        if (empty($RiskAssetTop5)) {
            return [];
        }
        if (count($RiskAssetTop5) == 1) {
            $RiskAssetTop5[0]['count'] = 100;
            return $RiskAssetTop5;
        }
        if (count($RiskAssetTop5) == 2) {
            $RiskAssetTop5[0]['count'] = 100;
            $RiskAssetTop5[1]['count'] = 60;
            return $RiskAssetTop5;
        }
        if (count($RiskAssetTop5) == 3) {
            $RiskAssetTop5[0]['count'] = 100;
            $RiskAssetTop5[1]['count'] = 80;
            $RiskAssetTop5[2]['count'] = 60;
            return $RiskAssetTop5;
        }
        if (count($RiskAssetTop5) == 4) {
            $RiskAssetTop5[0]['count'] = 100;
            $RiskAssetTop5[1]['count'] = 90;
            $RiskAssetTop5[2]['count'] = 70;
            $RiskAssetTop5[3]['count'] = 60;
            return $RiskAssetTop5;
        }
        //计算分数
        $RiskAssetTop5[0]['count'] = 100;
        if (($RiskAssetTop5[0]['count'] + $RiskAssetTop5[4]['count']) / 2 > $RiskAssetTop5[2]['count']) {
            $RiskAssetTop5[2]['count'] = 75;
            if (($RiskAssetTop5[0]['count'] + $RiskAssetTop5[2]['count']) / 2 > $RiskAssetTop5[1]['count']) {
                $RiskAssetTop5[1]['count'] = 79;
            } else {
                $RiskAssetTop5[1]['count'] = 85;
            }
            if (($RiskAssetTop5[2]['count'] + $RiskAssetTop5[4]['count']) / 2 > $RiskAssetTop5[3]['count']) {
                $RiskAssetTop5[3]['count'] = 71;
            } else {
                $RiskAssetTop5[3]['count'] = 64;
            }
        } else {
            $RiskAssetTop5[2]['count'] = 85;
            if (($RiskAssetTop5[0]['count'] + $RiskAssetTop5[2]['count']) / 2 > $RiskAssetTop5[1]['count']) {
                $RiskAssetTop5[1]['count'] = 89;
            } else {
                $RiskAssetTop5[1]['count'] = 92;
            }
            if (($RiskAssetTop5[2]['count'] + $RiskAssetTop5[4]['count']) / 2 > $RiskAssetTop5[3]['count']) {
                $RiskAssetTop5[3]['count'] = 80;
            } else {
                $RiskAssetTop5[3]['count'] = 77;
            }
        }
        $RiskAssetTop5[4]['count'] = 60;
        return $RiskAssetTop5;
    }

//
//    //设置网络IP段
//    public static function setIpSegment($net_parames) {
//        $conf = self::find()->where(['and', ['=', 'ip_segment', $net_parames['ip_segment']], ['=', 'net_mask', $net_parames['net_mask']]])->asArray()->all();
//        if (!empty($conf)) {
//            return '请勿重复添加';
//        }
//        //添加
//        $IpSegment = new IpSegment();
//        $IpSegment->ip_segment = $net_parames['ip_segment'];
//        $IpSegment->net_mask = $net_parames['net_mask'];
//        $IpSegment->save();
//        //查询配置完之后的所有的配置，并存入reids
//        $result = self::find()->select('ip_segment,net_mask')->asArray()->all();
//        Yii::$app->cache->set("IpSegment", json_encode($result));
//        return true;
//    }
    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
