<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Alert;
use common\models\Config;
use yii\helpers\ArrayHelper;
use common\models\ProtocolFlowStatistics;
use common\models\RiskAssetStatistics;
use common\models\FlowFileStatistics;
use common\models\AlertStatistions;
use common\models\SystemState;

/**
 * Site controller
 */
class AlertController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        if (Config::getLicense()['validLicenseCount'] == 0) {
            $rules = [];
        } else {
            $rules = [
                ['actions' => [], 'allow' => false, 'roles' => ['?']],
                ['actions' => [], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'alert-trend', 'do-alarm', 'alert-details', 'export-alerts', 'system-state', 'dev-state', 'flow-file-statistics', 'protocol-flow-statistics', 'get-last7-days-alarm', 'untreated-alarm-type', 'threat-type', 'threat-top5', 'risk-asset-top5'],
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

    //告警列表
    public function actionList($page = 1, $rows = 15) {
        isAPI();
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $page = empty($get['page']) ? $page : $get['page'];
            $rows = empty($get['rows']) ? $rows : $get['rows'];
        }
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
        $query = Alert::find()->orderBy(['alert_time' => SORT_DESC, 'id' => SORT_DESC])->select('id,alert_id,src_ip,dest_ip,indicator,alert_type,category,application,degree,detect_engine,alert_description,alert_time,status,processing_person');
        foreach ($whereList as $key => $value) {
            $query = $query->andWhere($value);
        }
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) $query->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $pageData = $query->offSet(($page - 1) * $rows)->limit($rows)->asArray()->all();
        //改变显示
        foreach ($pageData as $key => $value) {
            $pageData[$key]['alert_type'] = Alert::changeAlertType($value['alert_type']);
            $pageData[$key]['category'] = Alert::changeCategory($value['category']);
        }
        $data = [
            'data' => $pageData,
            'count' => $count,
            'maxPage' => $maxPage,
            'pageNow' => $page,
        ];
        return return_format($data);
    }

    //告警趋势统计
    public function actionAlertTrend() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $data = AlertStatistions::AlertTrend();
            return return_format($data);
        }
        return return_format('请求失败');
    }

    //操作告警（处理，确认）
    public function actionDoAlarm() {
        isAPI();
        if (Yii::$app->request->isPut) {
            $alarm_info = json_decode(Yii::$app->request->getRawBody(), true);
            $alarm_id = $alarm_info['id'];
            $alarm_status = $alarm_info['status'];
            if (!in_array($alarm_status, [0, 2, 3])) {
                return return_format('告警状态输入错误');
            }
            $alert = Alert::findOne($alarm_id);
            if (empty($alert)) {
                return return_format('告警选择错误');
            }
            $alert->status = $alarm_status;
            //操作人员
            if ($alarm_status == 2) {
                $alert->processing_person = Yii::$app->user->identity->username;
            }
            $alert->save();
            return return_format(true);
        }
        return return_format('请求失败');
    }

    //告警详情
    public function actionAlertDetails() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $alarm_id = Yii::$app->request->get('id');
            if (empty($alarm_id)) {
                return return_format('请选择告警');
            }
            $alert = Alert::findOne($alarm_id);
            //点击详情的时候，如果处于新告警状态，则标记为未解决
            if ($alert->status == 0) {
                $alert->status = 1;
                $alert->save();
            }
            $alert = ArrayHelper::toArray($alert);
            return return_format($alert);
        }
        return return_format('请求失败');
    }

    //导出告警
    public function actionExportAlerts() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $parame = Yii::$app->request->get();
            if (empty($parame)) {
                return return_format('参数选择错误');
            }
            //调用导出告警的方法
            $EXCEL_OUT = Alert::ExportAlerts();
            header("Content-type:text/csv");
            header("Content-Disposition:attachment; filename=告警列表_" . date("Y.m.d", $parame['start_time']) . "-" . date("Y.m.d", $parame['end_time']) . ".csv");
            echo $EXCEL_OUT;
            exit();
        }
        return return_format('请求失败');
    }

    //获取相同指标的告警的方法
    public function actionGetSameIndicatorAlert($page1 = 1, $rows1 = 15) {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        $get = Yii::$app->request->get();
        //获取参数
        $indicator = $get['indicator'];
        $is_deal = $get['is_deal'];
        $page = empty($get['page']) ? $page1 : $get['page'];
        $rows = empty($get['rows']) ? $rows1 : $get['rows'];
        //保证参数存在
        if (empty($indicator) || !in_array($is_deal, [0, 2])) {
            return '参数选择错误';
        }
        //转换值的类型
        if ($is_deal == 0) {
            $is_deal = [0, 1];
        }
        // 声明页码和每页显示数量等参数
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) Alert::find()->where(['AND', ['=', 'indicator', $indicator], ['IN', 'status', $is_deal]])->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $pageData = Alert::find()->select('id,alert_id,src_ip,dest_ip,alert_type,category,application,degree,detect_engine,alert_description,alert_time,status,processing_person,updated_at')->where(['AND', ['=', 'indicator', $indicator], ['IN', 'status', $is_deal]])->offSet(($page - 1) * $rows)->limit($rows)->asArray()->all();
        //转换日期时间
        foreach ($pageData as $k => $v) {
            $pageData[$k]['alert_type'] = Alert::changeAlertType($v['alert_type']);
            $pageData[$k]['category'] = Alert::changeCategory($v['category']);
            $pageData[$k]['alert_time'] = date('Y-m-d H:i:s', $v['alert_time']);
            $pageData[$k]['updated_at'] = date('Y-m-d H:i:s', $v['updated_at']);
        }
        $data = ['data' => $pageData, 'count' => $count, 'maxPage' => $maxPage, 'pageNow' => $page];
        return return_format($data);
    }

    //系统运行状态
    public function actionSystemState() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $data = SystemState::getSystemState();
            return return_format($data);
        }
        return return_format('请求失败');
    }

    //单个设备的运行状态
    public function actionDevState() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $dev_ip = Yii::$app->request->get('ip');
            if (!$dev_ip) {
                return return_format('参数错误');
            }
            $start_time = date('Y-m-d H:i:s', time() - 43200); //☆☆☆☆☆修改此值时需要修改计划任务
            $end_time = date('Y-m-d H:i:s', time());
            $data = SystemState::getDevState($dev_ip, $start_time, $end_time);
            return return_format($data);
        }
        return return_format('请求失败');
    }

    //流量文件统计
    public function actionFlowFileStatistics() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $three_hours_ago_timestamp = mktime(date('H') - 3, 0, 0, date('m'), date('d'), date('Y'));
            $current_timestamp = time();
            $data = FlowFileStatistics::getFlowFile($three_hours_ago_timestamp, $current_timestamp);
            return return_format($data);
        }
        return return_format('请求失败');
    }

    //协议流量统计
    public function actionProtocolFlowStatistics() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $three_hours_ago_timestamp = mktime(date('H') - 4, 59, 55, date('m'), date('d'), date('Y'));
            $data = ProtocolFlowStatistics::protocalStatistics(date('Y-m-d H:i:s', $three_hours_ago_timestamp), date('Y-m-d H:i:s', time()));
            return return_format($data);
        }
        return return_format('请求失败');
    }

//获取最近7天的告警情况
    public function actionGetLast7DaysAlarm() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $alarm = Alert::Last7DaysAlarm();
            return return_format($alarm);
        }
        return return_format('请求失败');
    }

    //未处理告警分类
    public function actionUntreatedAlarmType() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $untreatedAlarmType = Alert::untreatedAlarmType();
            return return_format($untreatedAlarmType);
        }
        return return_format('请求失败');
    }

    //威胁类型
    public function actionThreatType() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $threat_type = Alert::threatType();
            return return_format($threat_type);
        }
        return return_format('请求失败');
    }

    //威胁top5
    public function actionThreatTop5() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $threat_top5 = Alert::ThreatTop5();
            return return_format($threat_top5);
        }
        return return_format('请求失败');
    }

    //风险资产top5
    public function actionRiskAssetTop5() {
        isAPI();
        if (Yii::$app->request->isGet) {
            $risk_asset_top5 = RiskAssetStatistics::getRiskAssetTop5();
            return return_format($risk_asset_top5);
        }
        return return_format('请求失败');
    }

}
