<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

class Alert extends ActiveRecord {

    /**
     * Alert model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%alert}}';
    }

    /**
     * @inheritdoc
     */
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

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $jsonName = [
            'data',
        ];
        $value = parent::__get($name);
        if (in_array($name, $jsonName)) {
            $value = json_decode($value, true);
        }
        return $value;
    }

    //按ID查询告警
    public function getAlertById($alert_id) {
        $alert_info = self::findOne(['=', 'id', $alert_id])->toArray();
        return $alert_info;
    }

    //导出告警
    public function ExportAlerts($report_info = []) {
        if (empty($report_info)) {
            $get = Yii::$app->request->get();
            $whereList = [];
            if (isset($get['src_ip']) && $get['src_ip'] != '') {
                $whereList[] = ['=', 'src_ip', $get['src_ip']];
            }
            if (isset($get['dest_ip']) && $get['dest_ip'] != '') {
                $whereList[] = ['=', 'dest_ip', $get['dest_ip']];
            }
            if (isset($get['start_time']) && $get['start_time'] != '') {
                $whereList[] = ['>', 'alert_time', $get['start_time']];
            }
            if (isset($get['end_time']) && $get['end_time'] != '') {
                $whereList[] = ['<', 'alert_time', $get['end_time']];
            }
            if (isset($get['status'])) {
                if ($get['status'] == 0) {
                    $whereList[] = ['IN', 'status', [0, 1]];
                } else if ($get['status'] == 2) {
                    $whereList[] = ['=', 'status', 2];
                } else if ($get['status'] == 3) {
                    $whereList[] = ['IN', 'status', [0, 1, 2]];
                }
            }
            $preData = Alert::find()->orderBy(['alert_time' => SORT_DESC, 'id' => SORT_DESC])->select('id,src_ip,dest_ip,alert_type,category,application,degree,alert_time,detect_engine,status,processing_person,alert_description,updated_at')->asArray()->all();
            //$preData = self::find()->where(['AND', ['>=', 'alert_time', $report_info['stime']], ['<=', 'alert_time', $report_info['etime']]])->select('id,src_ip,dest_ip,alert_type,category,application,degree,alert_time,detect_engine,status,processing_person,alert_description,updated_at')->orderBy('id DESC')->asArray()->all();
        } else {
            $preData = self::find()->where(['AND', ['>=', 'alert_time', $report_info['stime']], ['<=', 'alert_time', $report_info['etime']]])->select('id,src_ip,dest_ip,alert_type,category,application,degree,alert_time,detect_engine,status,processing_person,alert_description,updated_at')->orderBy('id DESC')->asArray()->all();
        }
        //            $downloadData = Alert::changeCategory($preData);
        $EXCEL_OUT = iconv('UTF-8', 'GBK', "源IP,目的IP,告警类型,威胁类型,应用,威胁等级,检测引擎,告警状态,处理人,处理时间,告警时间,告警信息\n");
        foreach ($preData as $item) {
//            p($item);die;
            $status = '';
            $processing_time = '';
            $processing_person = '';
            if ($item['status'] == 0) {
                $status = '新告警';
            } else if ($item['status'] == 1) {
                $status = '未解决';
            } else if ($item['status'] == 2) {
                $status = '已解决';
                $processing_time = date('Y-m-d H:i:s', $item['updated_at']);
                $processing_person = $item['processing_person'];
            }
            try {
                $line = iconv('UTF-8', 'GBK//IGNORE', $item['src_ip'] . ',' .
                        "\"" . $item['dest_ip'] . "\"" . ',' .
                        $item['alert_type'] . ',' .
                        $item['category'] . ',' .
                        $item['application'] . ',' .
                        $item['degree'] . ',' .
                        $item['detect_engine'] . ',' .
                        $status . ',' .
                        "\"" . $processing_person . "\"" . ',' .
                        $processing_time . ',' .
                        date('Y-m-d H:i:s', $item['alert_time']) . ',' .
                        "\"" . str_replace("\"", "'", str_replace(PHP_EOL, '', $item['alert_description'])) . "\"" .
                        "\n"
                );
            } catch (Exception $e) {
                break;
            }
            $EXCEL_OUT .= $line;
        }
        return $EXCEL_OUT;
    }

    //总览页面最上方的tab标签
    public function showTabs() {
        //查询未处理告警总数，存在告警的风险资产总数，历史所有的告警
        $untreated_alarm_count_total = self::find()->where(['IN', 'status', [0, 1]])->count('id');
        $risk_dev_count_total = self::find()->groupBy('client_ip')->where(['IN', 'status', [0, 1]])->count('id');
//        $alarm_count_last_24_hours = self::find()->where(['and', ['<', 'time', time()], ['>', 'time', time() - 86400]])->count('id');
//        $risk_dev_count_last_24_hours = self::find()->where(['and', ['<', 'time', time()], ['>', 'time', time() - 86400]])->count('id');
        return ['untreated_alarm_count_total' => $untreated_alarm_count_total, 'risk_dev_count_total' => $risk_dev_count_total];
    }

    //威胁类型TOP5
    public function threatType() {
        $threat_type = self::find()->groupBy('alert_type')->select(['COUNT(id) as total_count,alert_type'])->asArray()->orderBy('total_count DESC')->all();
        foreach ($threat_type as $key => $value) {
            //转换威胁类型
            $threat_type[$key]['alert_type'] = self ::changeAlertType($value['alert_type']);
        }
        return $threat_type;
    }

    //修改威胁类型分类
    public function changeAlertType($type) {
        error_reporting(E_ERROR);
        $type_list = ['hash' => '可疑文件', 'domain' => '可疑域名', 'URL' => '可疑URL', 'IPv4' => '可疑IP'];
        return $type_list[$type] ? $type_list[$type] : '未知';
    }

    //未处理告警分类
    public function untreatedAlarmType() {
        $threat_type = self::find()->groupBy('degree')->where(['IN', 'status', [0, 1]])->select(['COUNT(id) as total_count,degree'])->asArray()->orderBy('total_count DESC')->all();
        return $threat_type;
    }

    //获取最近7天的告警
    public function Last7DaysAlarm() {
        $current_time = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
        //获取当前的时间
        $current_time_now = strtotime(date('Y-m-d H:i', time()) . ':00');
        $sql = "SELECT SUBSTRING(statistics_time,1,11) statistics_time,alert_count_details FROM alert_statistics  WHERE `statistics_time` >= '" . date('Y-m-d H:i', $current_time - 86400 * 7) . "' AND `statistics_time` <= '" . date('Y-m-d H:i', $current_time) . "' GROUP BY SUBSTRING(statistics_time,1,11)";
        $alarms = Yii::$app->db->createCommand($sql)->query();
        $alarms = ArrayHelper::toArray($alarms);
        //转换格式
        foreach ($alarms as $key => $value) {
            $alarms[$key]['alert_count_details'] = json_decode($value['alert_count_details'], true);
        }
        //获取过去24小时的所有告警
//        $last24_alarm_count = 0;
//        foreach ($alarms[0] as $value) {
//            $last24_alarm_count += $value;
//        }
        return $alarms;
    }

    //威胁top5
    public function ThreatTop5() {
        $sql = 'SELECT `id`, `src_ip`, `dest_ip`, `alert_type`, `category`, `degree` FROM `alert` ORDER BY FIELD(degree, "high", "medium", "low") LIMIT 5';
        $threat_type = Yii::$app->db->createCommand($sql)->query();
        $threat_type = ArrayHelper::toArray($threat_type);
        return $threat_type;
    }

    //和底层通信的方法
    public function bottomCommunication($path) {
        //        $path = $path . '?' . $this->getRueryString();
        $ResultClient = Yii::$app->ResultClient;
        if (Yii::$app->request->isPost) {
            $postdata = Yii::$app->request->getRawBody();
            $response = $ResultClient->post($path, $postdata, $_FILES);
        } elseif (Yii::$app->request->isPut) {
            $putdata = Yii::$app->request->getRawBody();
            $response = $ResultClient->put($path, $putdata);
        } elseif (Yii::$app->request->isDelete) {
            $response = $ResultClient->delete($path);
        } else {
            $response = $ResultClient->get($path);
        }
        self::setHeader($response);
        return $response->getContent();
    }

    //被bottomCommunication调用的方法 
    private function setHeader($response) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/json');
    }

    //风险资产top5
    public function riskAssetsSort() {
        $risk_assets = self::find()->where(['IN', 'status', [0, 1]])->groupBy('client_ip')->select('count(id) as risk_assets_count,client_ip')->orderBy('risk_assets_count DESC')->limit(5)->asArray()->all();
        //$risk_assets = array('0'=>array('risk_assets_count'=>'12','client_ip'=>999),'1'=>array('risk_assets_count'=>'13241','client_ip'=>9888));
        if (empty($risk_assets)) {
            return null;
        }
        if (count($risk_assets) == 1) {
            $risk_assets[0]['risk_assets_count'] = 100;
            return $risk_assets;
        }
        if (count($risk_assets) == 2) {
            $risk_assets[0]['risk_assets_count'] = 100;
            $risk_assets[1]['risk_assets_count'] = 60;
            return $risk_assets;
        }
        if (count($risk_assets) == 3) {
            $risk_assets[0]['risk_assets_count'] = 100;
            $risk_assets[1]['risk_assets_count'] = 80;
            $risk_assets[2]['risk_assets_count'] = 60;
            return $risk_assets;
        }
        if (count($risk_assets) == 4) {
            $risk_assets[0]['risk_assets_count'] = 100;
            $risk_assets[1]['risk_assets_count'] = 90;
            $risk_assets[2]['risk_assets_count'] = 70;
            $risk_assets[3]['risk_assets_count'] = 60;
            return $risk_assets;
        }
        //计算分数
        $risk_assets[0]['risk_assets_count'] = 100;
        if (($risk_assets[0]['risk_assets_count'] + $risk_assets[4]['risk_assets_count']) / 2 > $risk_assets[2]['risk_assets_count']) {
            $risk_assets[2]['risk_assets_count'] = 75;
            if (($risk_assets[0]['risk_assets_count'] + $risk_assets[2]['risk_assets_count']) / 2 > $risk_assets[1]['risk_assets_count']) {
                $risk_assets[1]['risk_assets_count'] = 79;
            } else {
                $risk_assets[1]['risk_assets_count'] = 85;
            }
            if (($risk_assets[2]['risk_assets_count'] + $risk_assets[4]['risk_assets_count']) / 2 > $risk_assets[3]['risk_assets_count']) {
                $risk_assets[3]['risk_assets_count'] = 71;
            } else {
                $risk_assets[3]['risk_assets_count'] = 64;
            }
        } else {
            $risk_assets[2]['risk_assets_count'] = 85;
            if (($risk_assets[0]['risk_assets_count'] + $risk_assets[2]['risk_assets_count']) / 2 > $risk_assets[1]['risk_assets_count']) {
                $risk_assets[1]['risk_assets_count'] = 89;
            } else {
                $risk_assets[1]['risk_assets_count'] = 92;
            }
            if (($risk_assets[2]['risk_assets_count'] + $risk_assets[4]['risk_assets_count']) / 2 > $risk_assets[3]['risk_assets_count']) {
                $risk_assets[3]['risk_assets_count'] = 80;
            } else {
                $risk_assets[3]['risk_assets_count'] = 77;
            }
        }
        $risk_assets[4]['risk_assets_count'] = 60;
        return $risk_assets;
    }

    //保存告警的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

    //修改告警的category
    public function changeCategory($category) {
        switch (strtolower(strtr($category, array(' ' => '')))) {
            case 'malware':
                $changed_category = '恶意地址';
                break;
            case 'tor_exit_node':
                $changed_category = 'tor出口节点';
                break;
            case 'malware,spam':
                $changed_category = '恶意地址,垃圾邮件';
                break;
            case 'tor_exit_node,spam':
                $changed_category = 'tor出口节点,垃圾邮件';
                break;
            case 'tor_exit_node, malware':
                $changed_category = 'tor出口节点,恶意地址';
                break;
            case 'spam':
                $changed_category = '垃圾邮件';
                break;
            case 'spam,malware':
                $changed_category = '垃圾邮件,恶意地址';
                break;
            case 'botc&c':
                $changed_category = '僵尸网络';
                break;
            case 'mobilemalware':
                $changed_category = '移动恶意软件';
                break;
            case 'fraud':
                $changed_category = '网络诈骗';
                break;
            case 'botc&c,mobilemalware':
                $changed_category = '僵尸网络,移动恶意软件';
                break;
            case 'exploit':
                $changed_category = '漏洞利用';
                break;
            case 'fraud,maliciousredirect':
                $changed_category = '网络诈骗,恶意重定向';
                break;
            case 'maliciousredirect':
                $changed_category = '恶意重定向';
                break;
            case 'botc&c,malware':
                $changed_category = '僵尸网络,恶意地址';
                break;
            case 'botc&c,exploit':
                $changed_category = '僵尸网络,漏洞利用';
                break;
            case 'proxy':
                $changed_category = '网络代理';
                break;
            case 'tor_node':
                $changed_category = 'tor入口节点';
                break;
            case 'phishing':
                $changed_category = '钓鱼网站';
                break;
            case 'phishingurl':
                $changed_category = '钓鱼网站';
                break;
            default:
                $changed_category = '其他';
                break;
        }
        return $changed_category;
    }

    //获取内网受害IP的方法
    public function checkIPInNetworkSegment($ip, $network_segment, $net_mask) {
        $arr = explode('.', $net_mask);
        foreach ($arr as $k => $v) {
            $arr[$k] = decbin($v);
        }
        $bin = implode('', $arr);
        $mask = strlen(str_replace(0, '', $bin));
        $mask1 = 32 - $mask;
        return ((ip2long($ip) >> $mask1) == (ip2long($network_segment) >> $mask1));
    }

}
