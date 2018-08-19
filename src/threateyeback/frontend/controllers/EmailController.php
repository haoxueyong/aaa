<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Config;

/**
 * Email controller
 */
class EmailController extends Controller {

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
                ['actions' => ['get'], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['get', 'save', 'test'],
                'rules' => $rules,
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

    //获取邮件配置的信息
    public function actionGet() {
        isAPI();
        $data = Config::getEmail();
        return return_format($data);
    }

    //保存邮箱配置参数
    public function actionSave() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isPost) {
            //$post = Yii::$app->request->post();
            $post = json_decode(trim(Yii::$app->request->getRawBody()), true);
        }
        if (empty($post)) {
            return return_format('参数错误');
        }
        //保存到数据库
        Config::setEmail($post);
        return return_format(true);
    }

    //发送测试邮件
    public function actionTest() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isPost) {
            //$post = Yii::$app->request->post();
            $post = json_decode(trim(Yii::$app->request->getRawBody()), true);
        }
        if (empty($post)) {
            return return_format('参数错误');
        }
        $transport = [
            'class' => 'Swift_SmtpTransport',
            'host' => $post['host'],
            'username' => $post['username'],
            'password' => $post['password'],
            'port' => $post['port'],
            'encryption' => $post['encryption'],
        ];
        Yii::$app->mailer->setTransport($transport);
        $mail = Yii::$app->mailer->compose();
        $mail->setFrom($post['username']);
        $mail->setTo($post['alertEmail']);
        $mail->setSubject("测试邮件");
        $mail->setHtmlBody("恭喜您，邮箱配置成功！");
        try {
            $flag = $mail->send();
        } catch (Exception $e) {
            echo $e->getMessage();
            $flag = false;
        }
        if ($flag) {
            return return_format(true);
        } else {
            return return_format('测试邮件发送失败！');
        }
    }

}
