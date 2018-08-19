<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\News;

/**
 * News controller
 */
class NewsController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'update'],
                'rules' => [
                    ['actions' => [], 'allow' => false, 'roles' => ['?']],
                    ['actions' => [], 'allow' => true, 'roles' => ['@']]
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

    //消息盒子
    public function actionList() {
        isAPI();
        $uid = Yii::$app->user->identity->id;
        $newsList = News::find()->where(['uid' => $uid, 'status' => News::STATUS_UNREAD])->orderBy(['created_at' => SORT_DESC,])->asArray()->all();
        $return = return_format($newsList);
        $return['user_name'] = Yii::$app->user->identity->username;
        $return['role'] = Yii::$app->user->identity->role;
        return $return;
    }

    public function actionUpdate() {
        isAPI();
//        $userLog = new UserLog();
//        $userLog->type = UserLog::Type_DelUser;
        if (!Yii::$app->request->isPost) {
//            $data['status'] = 'fail';
//            $data['errorMessage'] = 'Not post request';
//            $userLog->status = UserLog::Fail;
//            $userLog->info = 'check a news failed because it was not a post request';
//            $userLog->save();
            return return_format('请求失败');
            //return $data;
        }
        if (Yii::$app->request->isPost) {
            $post = json_decode(Yii::$app->request->getRawBody(), true);
            //$post = Yii::$app->request->post();
        }
        if (empty($post)) {
            return return_format('参数错误');
            //return $data;
        }
        $news = News::findOne($post['id']);
        if (isset($news)) {
            $news->status = 2;
            $news->save();
        }
        return return_format(true);
    }

}
