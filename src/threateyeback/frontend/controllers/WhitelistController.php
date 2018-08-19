<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Config;
use common\models\Whitelist;

/**
 * Whitelist controller
 */
class WhitelistController extends Controller {

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
                ['actions' => ['list', 'download-ioc-template'], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'add', 'add-import', 'del', 'download-ioc-template'],
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
        $query = Whitelist::find()->orderBy('id DESC');
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) $query->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $list = $query->offSet(($page - 1) * $rows)->limit($rows)->asArray()->select('id,indicator,alert_type,create_time')->all();
        return return_format(['data' => $list, 'count' => $count, 'maxPage' => $maxPage, 'pageNow' => $page, 'rows' => $rows]);
    }

    //添加白名单(单个)
    public function actionAdd() {
        isAPI();
        if (!Yii::$app->request->isPost) {
            return return_format('请求失败');
        }
        $post_data = json_decode(Yii::$app->request->getRawBody(), true);
        if (!in_array($post_data['alert_type'], ['IP', 'URL', 'MD5'])) {
            return return_format('类型选择错误');
        }
        $data = Whitelist::whiteListAdd($post_data);
        return return_format($data);
    }

    //添加白名单(批量)
    public function actionAddImport() {
        isAPI();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //当上传错误时
        if (empty($_FILES['file']['name'])) {
            return return_format('请选择.txt或.ioc的文件，重新上传');
        }
        if (strlen($_FILES['file']['name']) > 255) {
            return return_format('文件名应小于255个字符');
        }
        $tmp_file = $_FILES['file']['tmp_name'];
        if (filesize($tmp_file) / 1024 / 1024 > 10) {
            return return_format('请选择小于10M的文件，重新上传');
        }
        $file_types = explode(".", $_FILES ['file'] ['name']);
        $file_type = $file_types[count($file_types) - 1];
        //判别是不是.txt和.ioc文件
        if (strtolower($file_type) != "txt" && strtolower($file_type) != "ioc") {
            return return_format('请选择.txt或.ioc的文件，重新上');
        }
        //判断用户传的是ioc还是txt
        if (strtolower($file_type) == 'ioc') {
            //获取文件内容
            $file_contents = file_get_contents($tmp_file);
            //解析内容
            $xml = simplexml_load_string($file_contents);
            $xmljson = json_encode($xml);
            $file_contents_arr = json_decode($xmljson, true)['definition']['Indicator']['IndicatorItem'];
            $return = Whitelist::whiteListIOCImport($file_contents_arr);
        } else if (strtolower($file_type) == 'txt') {
            $file = fopen($tmp_file, "r");
            $i = 0;
            $contents = [];
            $head_or_content = '';
            while (!feof($file)) {
                $content = trim(fgets($file));
                if ($content == 'MD5') {
                    $head_or_content = 'MD5';
                    goto aaa;
                } else if ($content == 'IP') {
                    $head_or_content = 'IP';
                    goto aaa;
                } else if ($content == 'URL') {
                    $head_or_content = 'URL';
                    goto aaa;
                }
                //如果不是空格，则写入
                if ($content) {
                    array_push($contents, ['indicator' => $content, 'alert_type' => $head_or_content]);
                }
                aaa:
                $i++;
            }
            fclose($file);
            //去重
            $res = [];
            foreach ($contents as $k => $value) {
                //查看有没有重复项
                if (isset($res[$value['alert_type'] . '_' . $value['indicator']])) {
                    unset($contents[$k]);
                } else {
                    $res[$value['alert_type'] . '_' . $value['indicator']] = 1;
                }
            }
            $return = Whitelist::whiteListAdds($contents);
        }
        return return_format($return);
    }

    //删除白名单
    public function actionDel() {
        isAPI();
        if (!Yii::$app->request->isDelete) {
            return return_format('请求失败');
        }
        $del_data = json_decode(Yii::$app->request->getRawBody(), true);
        //删除
        $data = Whitelist::whiteListDel($del_data);
        return return_format($data);
    }

    //下载ioc模板
    public function actionDownloadIocTemplate() {
        isAPI();
        $frontend_url = Yii::getAlias('@frontend');
        \YII::$app->response->sendFile($frontend_url . '/web/downloadfile/IOC.txt');
    }

}
