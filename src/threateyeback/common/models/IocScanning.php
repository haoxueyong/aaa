<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ioc_scanning}}".
 *
 */
class IocScanning extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%ioc_scanning}}';
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

    //协议流量的统计
    public function protocalStatistics($start_time, $end_time) {
        $protocalStatistics = self::find()->where(['and', ['<=', 'statistics_time', $end_time], ['>=', 'statistics_time', $start_time]])->select('id,pro_type,statistics_time,flow')->orderBy('id DESC')->asArray()->all();
        //修改数据
        foreach ($protocalStatistics as $key => $value) {
            $protocalStatistics[$key]['statistics_time'] = date('H:i:s', $value['statistics_time']);
        }
        //初始化一个数组,进行分组
        $result = [];
        foreach ($protocalStatistics as $v) {
            $result[$v['pro_type']][] = $v;  //根据initial 进行数组重新赋值
        }
        return $result;
    }

    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
