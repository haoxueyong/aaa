<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use common\models\UserLog;

/**
 * Userlog controller
 */
class UserlogController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['page', 'add', 'del'],
                'rules' => [
                    ['actions' => [], 'allow' => false, 'roles' => ['?']],
                    ['actions' => [], 'allow' => true, 'roles' => ['admin']],
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

    public function actionPage($page = 1, $rows = 15) {
        isAPI();
        if (Yii::$app->request->isPost) {
            $post = json_decode(Yii::$app->request->getRawBody(), true);
            $page = empty($post['page']) ? $page : $post['page'];
            $rows = empty($post['rows']) ? $rows : $post['rows'];
            $StartTime = empty($post['StartTime']) ? null : $post['StartTime'];
            $EndTime = empty($post['EndTime']) ? null : $post['EndTime'];
            $username = empty($post['username']) ? '' : $post['username'];
        }
        $page = (int) $page;
        $rows = (int) $rows;
        $query = UserLog::find()->orderBy(['created_at' => SORT_DESC]);
        $hasWhere = false;
        if ($username != '') {
            $query = $query->where(['username' => $username]);
            $hasWhere = true;
        }
        if ($EndTime != null) {
            $where = ['<', 'created_at', $EndTime];
            if ($hasWhere) {
                $query = $query->AndWhere($where);
            } else {
                $query = $query->where($where);
            }
            $hasWhere = true;
        }
        if ($StartTime != null) {
            $where = ['>', 'created_at', $StartTime];
            if ($hasWhere) {
                $query = $query->AndWhere($where);
            } else {
                $query = $query->where($where);
            }
        }
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) $query->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $sensorList = $query->offSet(($page - 1) * $rows)->limit($rows)->asArray()->all();
        $data = [
            'data' => $sensorList,
            'count' => $count,
            'maxPage' => $maxPage,
            'pageNow' => $page,
            'rows' => $rows,
            'status' => 'success',
        ];
        return $data;
    }

    public function actionAdd() {
        isAPI();
        $userLog = new UserLog();
        $userLog->type = UserLog::Type_AddUser;
        if (!Yii::$app->request->isPost) {
            $data['status'] = 'fail';
            $data['errorMessage'] = 'Not post request';
            $userLog->status = UserLog::Fail;
            $userLog->info = 'The add user failed because it was not a post request';
            $userLog->save();
            return $data;
        }
        if (Yii::$app->request->isPost) {
            $post = json_decode(Yii::$app->request->getRawBody(), true);
        }
        if (empty($post)) {
            $data['status'] = 'fail';
            $data['errorMessage'] = 'Rawbody not JSON';
            $userLog->status = UserLog::Fail;
            $userLog->info = 'The add user failed because the request parameter is not valid';
            $userLog->save();
            return $data;
        }

        $user = User::find()->where(['username' => $post['username']])->one();

        if (empty($user)) {
            $user = new User();
            $user->username = $post['username'];
            $user->setPassword($post['password']);
            $user->email = uniqid();
            $user->role = $post['role'];
            $user->creator = Yii::$app->user->identity->id;
            $user->creatorname = Yii::$app->user->identity->username;
            $user->save();
            $userLog->status = UserLog::Success;
            $userLog->info = 'User \'' . $user->username . '\' was added';
            $userLog->save();
        }
        return $this->actionPage($post['page']);
    }

    public function actionDel() {
        isAPI();
        $userLog = new UserLog();
        $userLog->type = UserLog::Type_DelUser;
        if (!Yii::$app->request->isPost) {
            $data['status'] = 'fail';
            $data['errorMessage'] = 'Not post request';
            $userLog->status = UserLog::Fail;
            $userLog->info = 'Deleting a user failed because it was not a post request';
            $userLog->save();
            return $data;
        }
        if (Yii::$app->request->isPost) {
            $post = json_decode(Yii::$app->request->getRawBody(), true);
        }
        if (empty($post)) {
            $data['status'] = 'fail';
            $data['errorMessage'] = 'Rawbody not JSON';
            $userLog->status = UserLog::Fail;
            $userLog->info = 'Deleting the user failed because the request parameter is not valid';
            $userLog->save();
            return $data;
        }
        $user = User::findOne($post['id']);
        if (isset($user)) {
            $user->delete();
            $userLog->status = UserLog::Success;
            $userLog->info = 'User \'' . $user->username . '\' was deleted';
            $userLog->save();
        }
        return $this->actionPage($post['page']);
    }

}
