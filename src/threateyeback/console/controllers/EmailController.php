<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Logger;
use common\models\Email;
use common\models\Alert;

/**
 * Email controller
 */
class EmailController extends Controller {

    /**
     * index Email
     *
     * @return 0
     */
    public function actionIndex() {
        $time = time();
        while (time() < $time + 60) {
            Email::read();
            sleep(1);
        }
        return 0;
    }

    //发送告警邮件的功能
    public function actionSendAlertEmail() {
        $email_info = Yii::$app->cache->get('Email');
        $last_alert = Alert::find()->select('id')->orderBy('id DESC')->limit(1)->asArray()->one();
        $cache_alert_id = Yii::$app->cache->get('AlertId');
        if ($last_alert['id'] == $cache_alert_id) {
            return 0;
        }
        if ($email_info['send']) {
            Email::send();
            Yii::$app->cache->set('AlertId', $last_alert['id']);
        }
        return 0;
    }

}
