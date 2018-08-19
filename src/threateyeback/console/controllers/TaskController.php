<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
//use common\models\News;
use common\models\Alert;
use common\models\FlowFileStatistics;
use common\models\ProtocolFlowStatistics;
use common\models\SystemState;

//use common\models\UserLog;

/**
 * Task controller
 */
class TaskController extends Controller {

    /**
     * index Task
     *
     * @return 0
     */
    public function actionIndex() {
        return 0;
    }

    /**
     * 每天凌晨的一点四十五
     *
     * @return 0
     */
    public function actionEveryDay() {
        $this->actionSetMachineCode();
        $this->cleanData();
        return 0;
    }

    /**
     * 每六个小时 的第45分钟
     *
     * @return 0
     */
    public function actionEverySixHour() {
        //$this->actionSetMachineCode();
        $this->cleanStatisticsData();
        return 0;
    }

    //设置机器码
    private function actionSetMachineCode() {
        $shell = 'sudo dmidecode |grep -A10 \'System Information$\' | grep \'Serial Number\'';
        exec($shell, $res);
        $code = md5($res[0]);
        Yii::$app->cache->set('MachineCode', $code);
        return 0;
    }

    //清除一年之前的告警
    private function cleanData() {
        $last_year_timestamp = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
        //删除一年前的告警
        Alert::deleteAll(['<', 'alert_time', $last_year_timestamp]);
        return 0;
    }

    //清除统计表中的数据
    private function cleanStatisticsData() {
        $last_fourhour_time = date('Y-m-d H:i:s', time() - 14400);
        //删除四小时钱的数据
        FlowFileStatistics::deleteAll(['<', 'statistics_time', $last_fourhour_time]);
        ProtocolFlowStatistics::deleteAll(['<', 'statistics_time', $last_fourhour_time]);
        SystemState::deleteAll(['<', 'statistics_time', $last_fourhour_time]);
        return 0;
    }

}
