<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\Config;

class Email extends ActiveRecord {

    /**
     * Email model
     *
     * @property integer $id
     */
    const STATUS_UNSENT = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    public static function tableName() {
        return '{{%email}}';
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

    public static function addAlert($alert) {
        $data = Config::getEmail();
        $type_str = ['未知', '可疑文件', '可疑IP', '可疑URL', '可疑行为', '可疑行为', '漏洞利用'];
        $email = new self();
        $email->From = $data['username'];
        $email->To = $data['alertEmail'];
        $email->Subject = '发现一个新的' . $type_str[$alert->AlertType];
        $email->HtmlBody = '请登录<a href="' . Yii::$app->params['frontendUrl'] . '/alert/index">iCatch管理平台</a>查看详情';
        $email->save();
    }

    public static function read() {
        $emailList = self::find()->where(['status' => self::STATUS_UNSENT])->all();
        foreach ($emailList as $key => $email) {
            $email->send();
        }
    }

    public function send() {
        $conf = Config::getEmailPwd();
        $transport = [
            'class' => 'Swift_SmtpTransport',
            'host' => $conf['host'],
            'username' => $conf['username'],
            'password' => $conf['password'],
            'port' => $conf['port'],
            'encryption' => $conf['encryption'],
        ];
        Yii::$app->mailer->setTransport($transport);
        $mail = Yii::$app->mailer->compose();
        $mail->setFrom($conf['username']);
        $mail->setTo($conf['alertEmail']);
        $mail->setSubject("【ThreatEye】新告警通知");
        $mail->setHtmlBody('您有新告警需要处理，请登录  <a href="http://' . explode(' ', $_SERVER['SSH_CONNECTION'])[2] . '">ThreatEye管理地址</a>  查看详情');
        try {
            $flag = $mail->send();
        } catch (Exception $e) {
            echo $e->getMessage();
            $flag = false;
        }
        return true;
    }

}
