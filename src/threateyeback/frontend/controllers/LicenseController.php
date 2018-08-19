<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\License;
use common\models\Config;
use yii\httpclient\Client;

/**
 * License controller
 */
class LicenseController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['import', 'get', 'online'],
                'rules' => [
                    ['actions' => [], 'allow' => false, 'roles' => ['?']],
                    ['actions' => [], 'allow' => true, 'roles' => ['admin']],
                    ['actions' => ['get'], 'allow' => true, 'roles' => ['@']]
                ]
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
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
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

    //导入许可证
    public function actionImport() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        $post = json_decode(Yii::$app->request->getRawBody(), true);
        //$post = Yii::$app->request->post();
        if (empty($post['bin'])) {
            return return_format('参数错误');
        }
        $bin = $post['bin'];
        $license = new License($bin);
        $details = $license->details;
        if (empty($details) || (array_key_exists('product', $details) && $details['product'] != 'ThreatEye')) {
            return return_format('证书错误');
        } else if (array_key_exists('endTime', $details) && ($details['endTime'] > time() * 1000)) {
            $data['SN'] = $details['SN'];
            $data['license'] = $license->import();
            return return_format($data);
        } else {
            return return_format('证书过期');
        }
    }

    //获取机器码
    public function actionGet() {
        isAPI();
        $data['license'] = Config::getLicense();
        $data['key'] = Yii::$app->cache->get('MachineCode');
        return return_format($data);
    }

    //在线激活
    public function actionOnline() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        $post = json_decode(Yii::$app->request->getRawBody(), true);
        //$post = Yii::$app->request->post();
        $httpclient = new Client([
            'baseUrl' => 'https://license.hoohoolab.com',
            'transport' => 'yii\httpclient\CurlTransport',
        ]);
        $url = '/api/license?SN=' . $post['SN'] . '&key=' . $post['key'];
        $request = $httpclient->createRequest()->setMethod('get')->setUrl($url);
        $request->addOptions(['sslVerifyPeer' => false]);
        $request->addHeaders(['accept' => 'application/json']);
        $response = $request->send();
        $content = json_decode($response->content, true);
        return $content;
    }

}
