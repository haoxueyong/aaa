<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Userlog controller
 */
class RulebaseController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        if (Config::getLicense()['validLicenseCount'] == 0) {
            $rules = [];
        } else {
            $rules = [
                ['actions' => [], 'allow' => false, 'roles' => ['?']],
                ['actions' => [], 'allow' => true, 'roles' => ['admin']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['realtime-update', 'upload-package', 'offline-updating'],
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

    //实时更新
    public function actionRealtimeUpdate() {
        isAPI();
        exec("python /usr/local/bin/rule_update.py 0", $output, $return_var);
        if ($return_var == 0) {
            return return_format(true);
        }
        return return_format('执行失败');
    }

    //上传更新包
    public function actionUploadPackage() {
        isAPI();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        //当上传错误时
        if (empty($_FILES['file']['name'])) {
            return return_format('请选择要上传的文件，重新上传');
        }
        //生成上传目录
        $upload_dir = '/opt/threatEye/update_packges/';
        if (!file_exists($upload_dir)) {
            return return_format('更新包目录不存在');
        }
        $tmp_file = $_FILES['file']['tmp_name'];
        if (filesize($tmp_file) / 1024 / 1024 > 800) {
            return return_format('请选择小于800M的文件，重新上传');
        }
//        $file_types = explode(".", $_FILES ['file'] ['name']);
//        $file_type1 = $file_types[count($file_types) - 2];
//        $file_type2 = $file_types[count($file_types) - 1];
        //判别是不合法的文件
        if (!in_array($_FILES['file']['name'], ['sdk.tgz', 'ips.tgz', ' sandbox.tgz', 'yara.tgz'])) {
            return return_format('请上传名为sdk.tgz、ips.tgz、sandbox.tgz或yara.tgz的文件');
        }
        //是否上传成功
        if (!copy($tmp_file, $upload_dir . $_FILES ['file'] ['name'])) {
            return return_format('文件上传失败');
        }
        return return_format(true);
    }

    //离线更新
    public function actionOfflineUpdating() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        //判断是否存在升级吧
        $update_file = '/opt/threatEye/update_packges/';
        $H = @opendir($update_file);
        $i = 0;
        while ($_file = readdir($H)) {
            $i++;
        }
        closedir($H);
        if ($i <= 2) {
            return return_format('请先上传离线更新包再执行该操作');
        }
        exec("python /usr/local/bin/rule_update.py 1", $output, $return_var);
        if ($return_var == 0) {
            return return_format(true);
        }
        return return_format('执行失败');
    }

}
