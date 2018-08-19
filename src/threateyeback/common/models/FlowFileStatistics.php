<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%flow_file_statistics}}".
 * F
 */
class FlowFileStatistics extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%flow_file_statistics}}';
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

    //流量文件的统计
    public function getFlowFile($start_time, $end_time) {
        $connection = \Yii::$app->db;
        $sql = 'SELECT a.statistics_time + 5 as statistics_time,FORMAT(b.flow - a.flow,2) AS flow_diff,b.file_count - a.file_count AS file_count_diff FROM (SELECT * FROM flow_file_statistics) AS a INNER JOIN (SELECT * FROM flow_file_statistics) AS b ON a.id = b.id - 1 WHERE a.statistics_time <=' . $end_time . ' AND a.statistics_time >=' . $start_time;
        $command = $connection->createCommand($sql);
        $flow_file_statistics = $command->queryAll();
//        $flow_file_statistics = self::find()->where(['and', ['<=', 'statistics_time', $end_time], ['>=', 'statistics_time', $start_time]])->select('flow,file_count,statistics_time')->orderBy('id DESC')->asArray()->all();
        //修改数据
        $statistics_time = [];
        $flow_diff = [];
        $file_count_diff = [];
        foreach ($flow_file_statistics as $value) {
            array_push($statistics_time, date('H:i:s', $value['statistics_time']));
            array_push($flow_diff, $value['flow_diff']);
            array_push($file_count_diff, $value['file_count_diff']);
        }
        return ['statistics_time' => $statistics_time, 'flow_diff' => $flow_diff, 'file_count_diff' => $file_count_diff];
    }

    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
