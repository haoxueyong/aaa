<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Config;
use common\models\IpSegment;

//use common\models\UserLog;

/**
 * Group controller
 */
class IpsegmentController extends Controller {

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
                ['actions' => ['list'], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'del', 'set-ip-segment'],
                'rules' => $rules
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'logout' => ['post'],
                // 'test' => ['post'],
                ]
            ],
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

    //获取所有的ip段
    public function actionList($page = 1, $rows = 15) {
        isAPI();
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $page = empty($get['page']) ? $page : $get['page'];
            $rows = empty($get['rows']) ? $rows : $get['rows'];
        }
        $page = (int) $page;
        $rows = (int) $rows;
        $query = Ipsegment::find()->orderBy('id DESC');
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) $query->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $list = $query->offSet(($page - 1) * $rows)->limit($rows)->asArray()->select('id,ip_segment,net_mask')->all();
        //存入到redis
//        $this->redisSave();
        return return_format(['data' => $list, 'count' => $count, 'maxPage' => $maxPage, 'pageNow' => $page, 'rows' => $rows]);
    }

    //将配置存入reids
//    public static function redisSave() {
//        $conf = self::find()->select('ip_segment,net_mask')->asArray()->all();
//        Yii::$app->cache->set("IpSegment", json_encode($conf));
//        return true;
//    }
    //删除网络IP段
    public function actionDel() {
        isAPI();
        if (!Yii::$app->request->isDelete) {
            return return_format('请求失败');
        }
        $id = json_decode(Yii::$app->request->getRawBody(), true)['id'];
        //删除
        IpSegment::deleteAll(['=', 'id', $id]);
        //存入到redis
//        $this->redisSave();
        return return_format(true);
    }

    //设置网络IP段
    public function actionSetIpSegment() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isPost) {
            $post = json_decode(trim(Yii::$app->request->getRawBody()), true);
        }
        //正则验证
        if (!preg_match('/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/', $post['ip_segment'])) {
            return return_format('IP格式输入错误');
        }
        if (!preg_match('/^(254|252|248|240|224|192|128|0)\.0\.0\.0|255\.(254|252|248|240|224|192|128|0)\.0\.0|255\.255\.(254|252|248|240|224|192|128|0)\.0|255\.255\.255\.(254|252|248|240|224|192|128|0)$/', $post['net_mask'])) {
            return return_format('子网掩码格式输入错误');
        }
        $data = IpSegment::setIpSegment($post);
        return return_format($data);
    }

}
