<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%system_state}}".
 *
 */
class SystemState extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%system_state}}';
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

    //获取所有设备的状态
    public static function getSystemState() {
        $sql = 'SELECT statistics_time,dev_ip,dev_type,cpu,mem,disk,status FROM `system_state` main WHERE (SELECT COUNT(1) FROM `system_state` sub WHERE main.dev_ip = sub.dev_ip AND main.id < sub.id) < 2';
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        //按照ip分组
        $grouped = [];
        foreach ($data as $value) {
            $grouped[$value['dev_ip']][] = $value;
        }
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $parms);
            }
        }
        //默认初始值都为0,声明$dev_info，放设备
        $warning_count = 0;
        $healthy_count = 0;
        $offline_count = 0;
        $dev_info = [];
        //分析状态，（半小时内）
        foreach ($grouped as $k => $v) {
            $cpu_info = [];
            $mem_info = [];
            $disk_info = [];
            $status = [];
            foreach ($v as $vv) {
                array_push($status, $vv['status']);
                array_push($cpu_info, $vv['mem']);
                array_push($mem_info, $vv['cpu']);
                array_push($disk_info, $vv['disk']);
            }
            //分析是否在线
            if (end($status) == 0) {
                $offline_count += 1;
                $dev_info[$k]['dev_ip'] = $k;
                $dev_info[$k]['dev_type'] = $v[0]['dev_type'];
                $dev_info[$k]['status'] = 0;
                continue;
            } else {
                $dev_info[$k]['dev_ip'] = $k;
                $dev_info[$k]['dev_type'] = $v[0]['dev_type'];
                $dev_info[$k]['status'] = 1;
            }
            //分析CPU是否超过85%
            $c = 0;
            foreach ($cpu_info as $vvv) {
                if ($vvv > 85) {
                    $c += 1;
                }
            }
            if (count($cpu_info) == $c) {
                $warning_count += 1;
            } else {
                $healthy_count += 1;
            }
            //分析内存是否超过85%
            $m = 0;
            foreach ($mem_info as $vvv) {
                if ($vvv > 85) {
                    $m += 1;
                }
            }
            if (count($mem_info) == $m) {
                $warning_count += 1;
            } else {
                $healthy_count += 1;
            }
            //分析磁盘是否超过80%
            $d = 0;
            foreach ($disk_info as $vvv) {
                if ($vvv > 80) {
                    $d += 1;
                }
            }
            if ($d > 0) {
                $warning_count += 1;
            } else {
                $healthy_count += 1;
            }
        }
        return ['warning_count' => $warning_count, 'healthy_count' => $healthy_count, 'offline_count' => $offline_count, 'dev_info' => array_values($dev_info)];
    }

    //单个设备的运行状态
    public static function getDevState($dev_ip, $start_time, $end_time) {
        $preData = self::find()->orderBy(['statistics_time' => SORT_DESC])->select('statistics_time,cpu,mem,disk,flow,status')->where(['=', 'dev_ip', $dev_ip])->andWhere(['and', ['>=', 'statistics_time', $start_time], ['<=', 'statistics_time', $end_time]])->asArray()->all();
        //相邻相减
        for ($i = 0; $i < count($preData) - 1; $i++) {
            $change = $preData[$i]['flow'] - $preData[$i + 1]['flow'];
            $preData[$i]['flow'] = $change < 0 ? 0 : $change;
            $preData[$i]['statistics_time'] = substr($preData[$i]['statistics_time'], -5, 5);
        }
        array_pop($preData);
        return $preData;
    }

    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
