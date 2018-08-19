<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%protocol_flow_statistics}}".
 *
 * @property integer $id
 * @property integer $host
 * @property string $info
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class ProtocolFlowStatistics extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%protocol_flow_statistics}}';
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
        //初始化一个数组,进行分组
        $result = [];
        foreach ($protocalStatistics as $v) {
            $result[$v['pro_type']][] = $v;  //根据initial 进行数组重新赋值
        }
        //相邻相减
        foreach ($result as $k => $v) {
            for ($i = 0; $i < count($v) - 1; $i++) {
                $change = $v[$i]['flow'] - $v[$i + 1]['flow'];
                $result[$k][$i]['flow'] = $change < 0 ? 0 : $change;
            }
            array_pop($result[$k]);
        }
        return $result;
    }

    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
