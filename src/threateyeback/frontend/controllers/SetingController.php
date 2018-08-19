<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Config;
use common\models\Network;

/**
 * Seting controller
 */
class SetingController extends Controller {

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
                ['actions' => ['get-network', 'get-proxy-server'], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['get-network', 'set-network', 'get-proxy-server', 'set-proxy-server'],
                'rules' => $rules
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'logout' => ['post'],
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

    //获取网络配置信息
    public function actionGetNetwork() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        $return = redisCommunication('GetNetWork');
        return return_format($return);
    }

    //设置网络配置信息
    public function actionSetNetwork() {
        isAPI();
        if (!Yii::$app->request->isPut) {
            return return_format('请求失败');
        }
        $edit_data = json_decode(Yii::$app->request->getRawBody(), true);
        //验证
        if (strtolower($edit_data['ONBOOT']) == 'yes') {
            if (!in_array(strtolower($edit_data['BOOTPROTO']), ['static', 'dhcp'])) {
                return return_format('请选择获取IP方式');
            }
            if (!preg_match('/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/', $edit_data['IPADDR'])) {
                return return_format('IP地址格式输入错误');
            }
            if (!preg_match('/^(254|252|248|240|224|192|128|0)\.0\.0\.0|255\.(254|252|248|240|224|192|128|0)\.0\.0|255\.255\.(254|252|248|240|224|192|128|0)\.0|255\.255\.255\.(254|252|248|240|224|192|128|0)$/', $edit_data['MASK'])) {
                return return_format('子网掩码格式输入错误');
            }
        }
        if (array_key_exists('GATEWAY', $edit_data)) {
            if (!preg_match('/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/', $edit_data['GATEWAY']) && $edit_data['GATEWAY']) {
                return return_format('默认网关格式输入错误');
            }
        }
        if (array_key_exists('DNS1', $edit_data)) {
            if (!preg_match('/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/', $edit_data['DNS1']) && $edit_data['DNS1']) {
                return return_format('首选DNS服务器格式输入错误');
            }
        }
        if (array_key_exists('DNS2', $edit_data)) {
            if (!preg_match('/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/', $edit_data['DNS2']) && $edit_data['DNS2']) {
                return return_format('备用DNS服务器格式输入错误');
            }
        }
        $return = redisCommunication('SetNetWork', $edit_data);
        return return_format($return);
    }

    //获取代理服务器信息
    public function actionGetProxyServer() {
        isAPI();
        $ResultClient = Yii::$app->ProxyServerClient;
        $response = $ResultClient->get($path);

        //return return_format(Network::getNetWork());
        return [
            'status' => 'success', 'data' => Network::getNetWork()];
    }

    //设置代理服务器信息
    public function actionSetProxyServer() {
        isAPI();
        if (!Yii::$app->request->isPut) {
            return return_format('请求失败');
        }
        $putdata = Yii::$app->request->getRawBody();
        //判断参数的合法性
        if ($a = $this->checkProxyParames($putdata)) {
            return return_format($a);
        }
        p($putdata);
        die;
//        $response = $ResultClient->put($path, $putdata);
//        return $data;
    }

    //检查代理服务器参数
    private function checkProxyParames($putdata) {
        $data = json_decode($putdata, true);
        if (count($data) != 2 || !array_key_exists('HTTP_PROXY', $data) || !array_key_exists('HTTPS_PROXY', $data)) {
            return '参数错误!';
        }
        //验证代理服务器参数参数
        if ((!preg_match('/^(http|https|socks5):\/\/((\w){1,64}:(\w){1,64}@){0,1}((1[0-9][0-9]\.)|(2[0-4][0-9]\.)|(25[0-5]\.)|([1-9][0-9]\.)|([0-9]\.)){3}((1[0-9][0-9])|(2[0-4][0-9])|(25[0-5])|([1-9][0-9])|([0-9]))(:([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-5]{2}[0-3][0-5])){0,1}$/', $data['HTTP_PROXY']) && $data['HTTP_PROXY']) || (!preg_match('/^(http|https|socks5):\/\/((\w){1,64}:(\w){1,64}@){0,1}((1[0-9][0-9]\.)|(2[0-4][0-9]\.)|(25[0-5]\.)|([1-9][0-9]\.)|([0-9]\.)){3}((1[0-9][0-9])|(2[0-4][0-9])|(25[0-5])|([1-9][0-9])|([0-9]))(:([0-9]|[1-9]\d{1,3}|[1-5]\d{4}|6[0-5]{2}[0-3][0-5])){0,1}$/', $data['HTTPS_PROXY']) && $data['HTTPS_PROXY'])) {
            return '代理服务器格式填写有误!';
        }
        return 0;
    }

}
