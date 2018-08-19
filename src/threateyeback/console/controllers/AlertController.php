<?php

namespace console\controllers;

//use Yii;
use yii\console\Controller;
//use common\models\Logger;
//use common\models\UserLog;
use common\models\Alert;
//use yii\helpers\Json;
use common\models\AlertStatistions;
use common\models\IpSegment;
use common\models\RiskAssetStatistics;

/**
 * Alert controller
 */
class AlertController extends Controller {

    /**
     * 每小时一次的计划任务,每小时的第一分钟
     *
     * @return 0
     */
    public function actionEveryHour() {
        //每小时统计一下告警,用于告警曲线绘制
        $this->statisticAlertCount();
        //每小时统计一下告警,用于风险资产统计
        $this->statisticRiskAsset();
        return 0;
    }

    //每小时统计一次告警数量，放入到alert_statistions表中，用于告警曲线绘制
    private function statisticAlertCount() {
        session_write_close();
        //获取当前时间整点的时间戳
        $current_hour_timestamp = strtotime(date("Y-m-d H", time()) . ":00:00");
//        $current_hour_timestamp = 1532090329;
//        $alert_count = Alert::find()->select("count(id) as alert_count")->where(['AND', ['<=', 'alert_time', $current_hour_timestamp], ['>', 'alert_time', $current_hour_timestamp - 3600]])->groupBy('degree')->asArray()->one();
        $alert_counts = Alert::find()->select("degree,count(degree) alert_count")->where(['AND', ['<=', 'alert_time', $current_hour_timestamp], ['>', 'alert_time', $current_hour_timestamp - 3600]])->groupBy('degree')->asArray()->all();
        //转化你告警数的格式
        $alert_count = 0;
        $alert_count_info['high'] = 0;
        $alert_count_info['medium'] = 0;
        $alert_count_info['low'] = 0;
        foreach ($alert_counts as $key => $value) {
            //统计总量
            $alert_count += $value['alert_count'];
            if ($value['degree'] == 'high') {
                $alert_count_info['high'] = $value['alert_count'];
            }
            if ($value['degree'] == 'medium') {
                $alert_count_info['medium'] = $value['alert_count'];
            }
            if ($value['degree'] == 'low') {
                $alert_count_info['low'] = $value['alert_count'];
            }
        }
        //将统计结果放入到alert_statistions表中
        $alertstatistions = new AlertStatistions();
        $alertstatistions->statistics_time = date("Y-m-d H") . ':00';
        $alertstatistions->alert_count = $alert_count;
        $alertstatistions->alert_count_details = json_encode($alert_count_info, true);
        $alertstatistions->save();
        return 0;
    }

    //每小时统计一次告警数量，放入到risk_asset_statistics表中，用于风险资产统计
    private function statisticRiskAsset() {
        session_write_close();
        //获取当前时间整点的时间戳
        $current_hour_timestamp = strtotime(date("Y-m-d H", time()) . ":00:00");
        //$current_hour_timestamp = 1532028761;
        $last_hour_alert = Alert::find()->select("src_ip,dest_ip,degree")->where(['AND', ['<=', 'alert_time', $current_hour_timestamp], ['>', 'alert_time', $current_hour_timestamp - 3600]])->asArray()->all();
        //$last_hour_alert = Alert::find()->select("src_ip,dest_ip,degree")->where(['AND', ['<=', 'alert_time', 1532093929], ['>', 'alert_time', 1532076929]])->asArray()->all();
        //将最近一小时的告警按Ip进行处理
        $risk_degree_arr = [];
        foreach ($last_hour_alert as $v) {
            if (isset($risk_degree_arr[$v['src_ip']])) {
                $risk_degree_arr[$v['src_ip']][$v['degree']] += 1;
            } else {
                $risk_degree_arr[$v['src_ip']]['high'] = 0;
                $risk_degree_arr[$v['src_ip']]['medium'] = 0;
                $risk_degree_arr[$v['src_ip']]['low'] = 0;
                $risk_degree_arr[$v['src_ip']][$v['degree']] += 1;
            }
            if (isset($risk_degree_arr[$v['dest_ip']])) {
                $risk_degree_arr[$v['dest_ip']][$v['degree']] += 1;
            } else {
                $risk_degree_arr[$v['dest_ip']]['high'] = 0;
                $risk_degree_arr[$v['dest_ip']]['medium'] = 0;
                $risk_degree_arr[$v['dest_ip']]['low'] = 0;
                $risk_degree_arr[$v['dest_ip']][$v['degree']] += 1;
            }
        }
        //获取所有的网段配置,判断是否设置了网段
        $ip_segment = IpSegment::getIpSegment();
        $result = [];
        if (empty($ip_segment)) {
            foreach ($risk_degree_arr as $k => $v) {
                $if_in = filter_var($k, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                if (!$if_in) {
                    $added = $risk_degree_arr[$k];
                    $added['asset_ip'] = $k;
                    array_push($result, $added);
                }
            }
        } else {
            foreach ($ip_segment as $k => $v) {
                foreach ($risk_degree_arr as $kk => $vv) {
                    //判断产生告警的设备是否在配置的网段内
                    if (Alert::checkIPInNetworkSegment($kk, $v['ip_segment'], $v['net_mask'])) {
                        $added = $risk_degree_arr[$kk];
                        $added['asset_ip'] = $kk;
                        array_push($result, $added);
                    }
                }
            }
        }
        //获取所有已经存在的资产
        $exist_risk_asset = RiskAssetStatistics::find()->select('id,asset_ip,high_count,medium_count,low_count,count,created_at')->asArray()->all();
        $update_data = [];
        //将结果写入数据库，或者更新
        foreach ($result as $k => $v) {
            foreach ($exist_risk_asset as $kk => $vv) {
                if ($v['asset_ip'] == $vv['asset_ip']) {
                    $temp = $result[$k];
                    $temp['id'] = $vv['id'];
                    $temp['high_count'] = $vv['high_count'] + $v['high'];
                    $temp['medium_count'] = $vv['medium_count'] + $v['medium'];
                    $temp['low_count'] = $vv['low_count'] + $v['low'];
                    $temp['count'] = $temp['high_count'] * 5 + $temp['medium_count'] * 3 + $temp['low_count'] * 1;
                    unset($temp['high']);
                    unset($temp['medium']);
                    unset($temp['low']);
                    array_push($update_data, $temp);
                    unset($result[$k]);
                    break;
                }
            }
        }
        //将需要写入的值进行字段转换
        foreach ($result as $k => $v) {
            $result[$k]['high_count'] = $v['high'];
            $result[$k]['medium_count'] = $v['medium'];
            $result[$k]['low_count'] = $v['low'];
            $result[$k]['count'] = $v['high'] * 5 + $v['medium'] * 3 + $v['low'] * 1;
            $result[$k]['created_at'] = time();
            $result[$k]['updated_at'] = time();
            unset($result[$k]['high']);
            unset($result[$k]['medium']);
            unset($result[$k]['low']);
        }
        //初始化数据库
        $connection = \Yii::$app->db;
        $risk_asset_statistics_table = RiskAssetStatistics::tableName();
        //数据批量入库
        $connection->createCommand()->batchInsert($risk_asset_statistics_table, ['asset_ip', 'high_count', 'medium_count', 'low_count', 'count', 'created_at', 'updated_at'], $result)->execute();
        //数据批量更新
        foreach ($update_data as $value) {
            $command = $connection->createCommand('UPDATE ' . $risk_asset_statistics_table . ' SET high_count= ' . $value['high_count'] . ' ,medium_count= ' . $value['medium_count'] . ' ,low_count= ' . $value['low_count'] . ' ,count= ' . $value['count'] . ' ,updated_at= ' . time() . ' WHERE id=' . $value['id']);
            $command->execute();
        }
        return 0;
    }

//    //被statisticRiskAsset调用
//    private function checkIPInNetworkSegment($ip, $network_segment, $net_mask) {
//        $arr = explode('.', $net_mask);
//        foreach ($arr as $k => $v) {
//            $arr[$k] = decbin($v);
//        }
//        $bin = implode('', $arr);
//        $mask = strlen(str_replace(0, '', $bin));
//        $mask1 = 32 - $mask;
//        return ((ip2long($ip) >> $mask1) == (ip2long($network_segment) >> $mask1));
//    }
}
