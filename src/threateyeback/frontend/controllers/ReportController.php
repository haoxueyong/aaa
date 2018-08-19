<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Alert;
use common\models\Config;
use common\models\Report;
use common\models\IpSegment;

//use common\models\UserLog;

/**
 * Group controller
 */
class ReportController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        if (Config::getLicense()['validLicenseCount'] == 0) {
            $rules = [];
        } else {
            $rules = [
                ['actions' => [], 'allow' => false, 'roles' => ['?']],
                ['actions' => [], 'allow' => true, 'roles' => ['admin']],
                ['actions' => ['list', 'download_report'], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'delete', 'create-echarts-img', 'create-report', 'download_report'],
                'rules' => $rules
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'logout' => ['post'],
                // 'test' => ['post'],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
//            'error' => [
//                'class' => 'yii\web\ErrorAction',
//            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionError() {
        return return_format('该页面不存在！', 404);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public $enableCsrfValidation = false;

    //获取所有的报表
    public function actionList($page = 1, $rows = 15) {
        isAPI();
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $page = empty($get['page']) ? $page : $get['page'];
            $rows = empty($get['rows']) ? $rows : $get['rows'];
        }
        $page = (int) $page;
        $rows = (int) $rows;
        $query = Report::find()->orderBy('id DESC');
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) $query->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $report = $query->offSet(($page - 1) * $rows)->limit($rows)->asArray()->select('id,report_name,report_type,create_time,stime,etime')->all();
        foreach ($report as $k => $v) {
            $report[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $report[$k]['stime'] = date('Y-m-d', $v['stime']);
            $report[$k]['etime'] = date('Y-m-d', $v['etime']);
        }
        return return_format(['data' => $report, 'count' => $count, 'maxPage' => $maxPage, 'pageNow' => $page, 'rows' => $rows]);
    }

    //删除报表
    public function actionDelete() {
        isAPI();
        if (!Yii::$app->request->isDelete) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isDelete) {
            $report_id = json_decode(Yii::$app->request->getRawBody(), true)['id'];
        }
        //删除数据库中的记录
        Report::deleteAll(['id' => $report_id]);
        //删除对应的echarts文件
        $dir = Yii::getAlias('@frontend') . '/web/echarts/' . md5($report_id);
        //判断是否存在文件
        if (file_exists($dir)) {
            $handle = opendir($dir);
            while ($file = readdir($handle)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        deldir($fullpath);
                    }
                }
            }
            closedir($handle);
            //删除当前文件夹：  
            rmdir($dir);
        }
        return return_format(true);
    }

    //生成报表前生成echars图片的base64
    public function actionCreateEchartsImg() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        //获取开始时间和结束时间
        $parames = Yii::$app->request->get();
        if (empty($parames['stime']) || empty($parames['etime'])) {
            return return_format('请准确选择开始时间和结束时间');
        }
        if ($parames['report_type'] != 'docx') {
            return return_format('无效请求');
        }
        $stime = $parames['stime'];
        $etime = $parames['etime'];
        //获取威胁使用应用协议的图片↓↓↓↓↓↓↓↓↓↓↓↓↓
        $data['threat_protocol'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->groupBy('application')->select('application,COUNT(*) count')->orderBy('count DESC')->asArray()->all();
        if (empty($data['threat_protocol'])) {
            $data['threat_protocol'][0] = ['application' => '', 'count' => 0];
        }
        //获取告警趋势的图片↓↓↓↓↓↓↓↓↓↓↓↓↓
        $data['alert_trend'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->groupBy('date_time')->select(["FROM_UNIXTIME(alert_time, '%Y-%m-%d') date_time", "COUNT(*) count"])->orderBy('alert_time ASC')->asArray()->all();
        //获取威胁类型的图片↓↓↓↓↓↓↓↓↓↓↓↓↓
        $data['alert_type'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->groupBy('alert_type')->select(['COUNT(id) as alert_count,alert_type'])->asArray()->orderBy('alert_count DESC')->all();
        foreach ($data['alert_type'] as $k => $v) {
            $data['alert_type'][$k]['alert_type'] = Alert::changeAlertType($v['alert_type']);
        }
        //获取威胁等级分布图片↓↓↓↓↓↓↓↓↓↓↓↓↓
        $data['threat_level'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->groupBy('degree')->select(['COUNT(*) as total_count,degree'])->asArray()->orderBy('total_count DESC')->all();
        return return_format($data);
    }

    //生成报表
    public function actionCreateReport() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        //获取开始时间和结束时间,及报表类型
        //$parames = Yii::$app->request->post();
        $parames = json_decode(Yii::$app->request->getRawBody(), true);
        if (empty($parames['stime']) || empty($parames['etime']) || empty($parames['report_name'])) {
            return return_format('请准确选择开始、结束时间、及报表名称');
        }
        if (!in_array($parames['report_type'], ['docx', 'csv'])) {
            return return_format('报表类型选择错误');
        }
        $stime = $parames['stime'];
        $etime = $parames['etime'];
        //实例化报表类
        $report = new Report();
        if ($parames['report_type'] == 'docx') {
            //判断base64格式
            if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $parames['alert_trend']) || !preg_match('/^(data:\s*image\/(\w+);base64,)/', $parames['threat_level']) || !preg_match('/^(data:\s*image\/(\w+);base64,)/', $parames['alert_type']) || !preg_match('/^(data:\s*image\/(\w+);base64,)/', $parames['threat_protocol'])) {
                return return_format('base64图片格式有误');
            }
            //获取所有的IP段
            $ip_segment = IpSegment::getIpSegment();
            //获取受害主机的数据
            //受害主机IP（每天）↓↓↓↓↓↓↓↓↓↓↓↓↓
            $alert = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->select(["src_ip", "dest_ip", "FROM_UNIXTIME(alert_time, '%Y-%m-%d') date_time"])->asArray()->all();
            $result = [];
            $perday_ip = [];
            foreach ($alert as $value) {
                $result[$value['date_time']][] = $value;
            }
            foreach ($result as $k => $v) {
                $perday_ip[$k] = [];
                if (empty($ip_segment)) {
                    foreach ($v as $vv) {
                        $ip_src = filter_var($vv['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                        $ip_dest = filter_var($vv['dest_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                        if (!$ip_src) {
                            array_push($perday_ip[$k], $vv['src_ip']);
                        } else if ($ip_src && !$ip_dest) {
                            array_push($perday_ip[$k], $vv['dest_ip']);
                        }
                    }
                } else {
                    foreach ($v as $vv) {
                        foreach ($ip_segment as $kkk => $vvv) {
                            //判断告警的哪个ip是内网的
                            $ip_src = Alert::checkIPInNetworkSegment($vv['src_ip'], $vvv['ip_segment'], $vvv['net_mask']);
                            if ($ip_src) {
                                array_push($perday_ip[$k], $vv['src_ip']);
                                goto perday_ip;
                            }
                        }
                        foreach ($ip_segment as $kkk => $vvv) {
                            //判断告警的哪个ip是内网的
                            $ip_dest = Alert::checkIPInNetworkSegment($vv['dest_ip'], $vvv['ip_segment'], $vvv['net_mask']);
                            if ($ip_dest) {
                                array_push($perday_ip[$k], $vv['dest_ip']);
                            }
                            break;
                        }
                        perday_ip:
                    }
                }
            }
            $data['perday_ip'] = [];
            foreach ($perday_ip as $key => $value) {
                $data['perday_ip'][$key] = count(array_unique($value));
            }
            ksort($data['perday_ip']);
            //恶意URL TOP10↓↓↓↓↓↓↓↓↓↓↓↓↓
            $data['url_top10'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->andWhere(['=', 'alert_type', 'URL'])->groupBy('indicator')->select(['COUNT(*) as indicator_count,category,indicator'])->asArray()->orderBy('indicator_count DESC')->limit(10)->all();
            //恶意IP TOP10
            $data['ip_top10'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->andWhere(['=', 'alert_type', 'IPv4'])->groupBy('indicator')->select(['COUNT(*) as indicator_count,category,indicator'])->asArray()->orderBy('indicator_count DESC')->limit(10)->all();
            //恶意文件  TOP10↓↓↓↓↓↓↓↓↓↓↓↓↓
            $data['hash_top10'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->andWhere(['=', 'alert_type', 'hash'])->groupBy('indicator')->select(['COUNT(*) as indicator_count,category,indicator,application'])->asArray()->orderBy('indicator_count DESC')->limit(10)->all();
            //受害主机TOP50↓↓↓↓↓↓↓↓↓↓↓↓↓
            $data['host_top50'] = [];
            if (empty($ip_segment)) {
                foreach ($alert as $k => $v) {
                    $ip_src = filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                    $ip_dest = filter_var($v['dest_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                    if (!$ip_src) {
                        key_exists($v['src_ip'], $data['host_top50']) ? $data['host_top50'][$v['src_ip']] += 1 : $data['host_top50'][$v['src_ip']] = 1;
                    } else if ($ip_src && !$ip_dest) {
                        key_exists($v['dest_ip'], $data['host_top50']) ? $data['host_top50'][$v['dest_ip']] += 1 : $data['host_top50'][$v['dest_ip']] = 1;
                    }
                }
            } else {
                foreach ($alert as $k => $v) {
                    foreach ($ip_segment as $kk => $vv) {
                        //判断告警的哪个ip是内网的
                        $ip_src = Alert::checkIPInNetworkSegment($v['src_ip'], $vv['ip_segment'], $vv['net_mask']);
                        if ($ip_src) {
                            key_exists($v['src_ip'], $data['host_top50']) ? $data['host_top50'][$v['src_ip']] += 1 : $data['host_top50'][$v['src_ip']] = 1;
                            goto host_top50;
                        }
                    }
                    foreach ($ip_segment as $kk => $vv) {
                        //判断告警的哪个ip是内网的
                        $ip_dest = Alert::checkIPInNetworkSegment($v['dest_ip'], $vv['ip_segment'], $vv['net_mask']);
                        if ($ip_dest) {
                            key_exists($v['dest_ip'], $data['host_top50']) ? $data['host_top50'][$v['dest_ip']] += 1 : $data['host_top50'][$v['dest_ip']] = 1;
                        }
                        break;
                    }
                    host_top50:
                }
            }
            //排序
            arsort($data['host_top50']);
            //勒索软件攻击↓↓↓↓↓↓↓↓↓↓↓
            $data['extortion_software'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->andWhere(['like', 'threat', 'trojan-ransom'])->select('src_ip,dest_ip,indicator,application')->asArray()->orderBy('id DESC')->all();
            //钓鱼攻击↓↓↓↓↓↓↓↓↓↓↓
            $data['phishing'] = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->andWhere(['in', 'category', ['phishing', 'PhishingURL']])->select('src_ip,dest_ip,indicator')->asArray()->orderBy('id DESC')->all();
            //僵尸网络访问↓↓↓↓↓↓↓↓↓↓↓
            $botc_c = Alert::find()->where(['and', ['<', 'alert_time', $etime], ['>', 'alert_time', $stime]])->andWhere(['in', 'category', ['Bot C&C', 'Bot C&C,MobileMalware', 'Bot C&C,Malware', 'Bot C&C,Exploit']])->select('src_ip,dest_ip,alert_time,indicator')->asArray()->orderBy('id DESC')->all();
            $data['botc_c'] = [];
            if (empty($ip_segment)) {
                foreach ($botc_c as $k => $v) {
                    $ip_src = filter_var($v['src_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                    $ip_dest = filter_var($v['dest_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                    $data['botc_c'][$key]['botc_c_ip'] = '';
                    if (!$ip_src) {
                        $data['botc_c'][$key]['botc_c_ip'] = $v['src_ip'];
                    } else if ($ip_src && !$ip_dest) {
                        $data['botc_c'][$key]['botc_c_ip'] = $v['dest_ip'];
                    }
                    $data['botc_c'][$key]['visit_time'] = date('Y-m-d H:i:s', $v['alert_time']);
                    $data['botc_c'][$key]['indicator'] = $v['indicator'];
                }
            } else {
                foreach ($botc_c as $kk => $vv) {
                    $data['botc_c'][$kk]['botc_c_ip'] = '';
                    foreach ($ip_segment as $k => $v) {
                        //判断告警的哪个ip是内网的
                        $ip_src = Alert::checkIPInNetworkSegment($vv['src_ip'], $v['ip_segment'], $v['net_mask']);
                        if ($ip_src) {
                            $data['botc_c'][$kk]['botc_c_ip'] = $vv['src_ip'];
                        }
                        goto botc_c;
                    }
                    foreach ($ip_segment as $k => $v) {
                        //判断告警的哪个ip是内网的
                        $ip_dest = Alert::checkIPInNetworkSegment($vv['dest_ip'], $v['ip_segment'], $v['net_mask']);
                        if ($ip_dest) {
                            $data['botc_c'][$key]['botc_c_ip'] = $vv['dest_ip'];
                        }
                        break;
                    }
                    botc_c:
                    $data['botc_c'][$kk]['visit_time'] = date('Y-m-d H:i:s', $vv['alert_time']);
                    $data['botc_c'][$kk]['indicator'] = $vv['indicator'];
                }
            }
            $report->report_name = $parames['report_name'];
            $report->create_time = time();
            $report->stime = $stime;
            $report->etime = $etime;
            $report->report_type = 'docx';
            $report->perday_ip = json_encode($data['perday_ip']);
            $report->url_top10 = json_encode($data['url_top10']);
            $report->ip_top10 = json_encode($data['ip_top10']);
            $report->hash_top10 = json_encode($data['hash_top10']);
            $report->host_top50 = json_encode($data['host_top50']);
            $report->extortion_software = json_encode($data['extortion_software']);
            //$report->extortion_software = 31452345435;
            $report->phishing = json_encode($data['phishing']);
            $report->botc_c = json_encode($data['botc_c']);
            $report->save();
            $report_id = $report->attributes['id'];
            $md5_id = md5($report_id);
            //生成图片并保存
            self::base64_image_content($parames['threat_level'], 'threat_level', $md5_id);
            self::base64_image_content($parames['threat_protocol'], 'threat_protocol', $md5_id);
            self::base64_image_content($parames['alert_trend'], 'alert_trend', $md5_id);
            self::base64_image_content($parames['alert_type'], 'alert_type', $md5_id);
        } else {
            $report->report_name = $parames['report_name'];
            $report->create_time = time();
            $report->stime = $stime;
            $report->etime = $etime;
            $report->report_type = 'csv';
            $report->save();
        }
        return return_format(true);
    }

    //将base64转成图片并保存的方法
    public function base64_image_content($base64_image_content, $file_name, $md5_id) {
        $echarts_path = Yii::getAlias('@frontend') . '/web/echarts/' . $md5_id;
//        p($base64_image_content);
//        $a = stripslashes($base64_image_content);
//        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA9QAAAJECAYAAAD3xWxzAAAgAElEQVR4XuzdCXTU1fn/8ed+JwnJTFiSkARBhQAii5W61BVhJiDCRGttq/1XwbpVTUImU', $result)) {
//            p($result);
//            die;
        $type = 'png';
        //检查是否有该文件夹，如果没有就创建
        if (!file_exists($echarts_path)) {
            mkdir($echarts_path);
        }
        $full_file_name = $file_name . '.' . $type;
        if (file_put_contents($echarts_path . '/' . $full_file_name, base64_decode(str_replace('data:image/png;base64,', '', stripslashes($base64_image_content))))) {
            return $full_file_name;
        } else {
            return false;
        }
//        } else {
//        return false;
//        }
    }

    //下载报表
    public function actionDownloadReport() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        //获取开始时间和结束时间
        $parames = Yii::$app->request->get();
        if (empty($parames['id'])) {
            return return_format('请选择需要下载的报表');
        }
        $report_id = $parames['id'];
        //获取当前服务器地址
        //$server_ip = Yii::$app->request->hostInfo;
        $server_ip = rtrim($_SERVER['HTTP_REFERER'], '/') . ':' . $_SERVER['SERVER_PORT'];
        $report = new Report();
        $report_info = $report->find()->where(['=', 'id', $report_id])->asArray()->one();
        if (empty($report_info)) {
            return return_format('该报告不存在');
        }
        //判断报表类型是docx还是csv
        if ($report_info['report_type'] == 'docx') {
            $md5_id = md5($report_id);
            //生成图片并保存
            //$threat_level = $this->base64_image_content($report_info['threat_level'], 'threat_level', $md5_id);
            $report_info['stime'] = date("Y.m.d", $report_info['stime']);
            $report_info['etime'] = date("Y.m.d", $report_info['etime']);
            $report_info['threat_protocol'] = $md5_id . '/threat_protocol.png';
            $report_info['alert_trend'] = $md5_id . '/alert_trend.png';
            $report_info['alert_type'] = $md5_id . '/alert_type.png';
            $report_info['threat_level'] = $md5_id . '/threat_level.png';
            $report_info['perday_ip'] = json_decode($report_info['perday_ip'], true);
//            $report_info['threat_level'] = json_decode($report_info['threat_level'], true);
            $report_info['url_top10'] = json_decode($report_info['url_top10'], true);
            $report_info['ip_top10'] = json_decode($report_info['ip_top10'], true);
            $report_info['hash_top10'] = json_decode($report_info['hash_top10'], true);
            $report_info['host_top50'] = json_decode($report_info['host_top50'], true);
            $report_info['extortion_software'] = json_decode($report_info['extortion_software'], true);
            $report_info['phishing'] = json_decode($report_info['phishing'], true);
            $report_info['botc_c'] = json_decode($report_info['botc_c'], true);
            $report_info['server_ip'] = $server_ip;
            return $this->render('model', $report_info);
        } else if ($report_info['report_type'] == 'csv') {
            //调用导出告警的方法
            $EXCEL_OUT = Alert::ExportAlerts($report_info);
            header("Content-type:text/csv");
            header("Content-Disposition:attachment; filename=告警列表_" . date("Y.m.d", $report_info['stime']) . "-" . date("Y.m.d", $report_info['etime']) . ".csv");
            echo $EXCEL_OUT;
            exit();
        }
    }

}
