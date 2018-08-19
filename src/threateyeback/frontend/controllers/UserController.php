<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use frontend\models\ResetPasswordForm;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use common\models\UserLog;
use yii\filters\Cors;
use yii\helpers\Json;
use common\models\Config;

/**
 * Site controller
 */
class UserController extends Controller {

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
                ['actions' => ['get-self-password-reset-token', 'reset-self-password'], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['page', 'user-add', 'user-del', 'get-self-password-reset-token', 'get-password-reset-token', 'reset-self-password', 'reset-password'],
                'rules' => $rules
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'logout' => ['post'],
                // 'test' => ['post'],
                ]
            ],
            'corsFilter' => [
                'class' => Cors::className(),
//                'cors' => [
//                    'Origin' => ['*'],
//                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
//                    'Access-Control-Request-Headers' => ['*'],
//                    'Access-Control-Allow-Origin' => ['*'],
//                    'Access-Control-Allow-Credentials' => true,
//                    'Access-Control-Max-Age' => 86400,
//                    'Access-Control-Expose-Headers' => [],
//                ],
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

    //获取所有用户
    public function actionPage($page = 1, $rows = 15) {
        isAPI();
        if (Yii::$app->request->isGet) {
            $get = json_decode(Yii::$app->request->getRawBody(), true);
            //$get = Yii::$app->request->get();
            $page = empty($get['page']) ? $page : $get['page'];
            $rows = empty($get['rows']) ? $rows : $get['rows'];
        }
        $page = (int) $page;
        $rows = (int) $rows;
        $query = User::find()->orderBy(['id' => SORT_DESC,]);
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
            'rows' => $rows
        ];
        return return_format($data);
    }

    //新增用户
    public function actionUserAdd() {
        isAPI();
        $userLog = new UserLog();
        $userLog->type = UserLog::Type_AddUser;
        if (!Yii::$app->request->isPost) {
            $userLog->status = UserLog::Fail;
            $userLog->info = 'The add user failed because it was not a post request';
            $userLog->save();
            return Json::encode(return_format('请求失败'));
        }
        if (Yii::$app->request->isPost) {
            $post = json_decode(Yii::$app->request->getRawBody(), true);
            //$post = Yii::$app->request->post();
        }
        if (empty($post)) {
            $userLog->status = UserLog::Fail;
            $userLog->info = 'The add user failed because the request parameter is not valid';
            $userLog->save();
            return return_format('参数错误');
        }
        $user = User::find()->where(['username' => $post['username']])->one();
        if (empty($user)) {
            $user = new User();
            $user->username = $post['username'];
            $user->setPassword($post['password']);
            $user->generateAuthKey();
            $user->email = uniqid();
            $user->role = $post['role'];
            $user->creator = Yii::$app->user->identity->id;
            $user->creatorname = Yii::$app->user->identity->username;
            $user->save();
            $userLog->status = UserLog::Success;
            $userLog->info = 'User \'' . $user->username . '\' was added';
            $userLog->save();
            return $this->actionPage($post['page']);
        } else {
            return return_format('该用户已存在');
        }
    }

    //删除用户
    public function actionUserDel() {
        isAPI();
        $userLog = new UserLog();
        $userLog->type = UserLog::Type_DelUser;
        if (!Yii::$app->request->isDelete) {
            $userLog->status = UserLog::Fail;
            $userLog->info = 'Deleting a user failed because it was not a delete request';
            $userLog->save();
            return return_format('请求失败');
        }
        if (Yii::$app->request->isDelete) {
            $post = json_decode(Yii::$app->request->getRawBody(), true);
            //$post = Yii::$app->request->getBodyParams();
        }
        if (empty($post)) {
            $userLog->status = UserLog::Fail;
            $userLog->info = 'Deleting the user failed because the request parameter is not valid';
            $userLog->save();
            return return_format('参数错误');
        }
        $user = User::findOne($post['id']);
        if (isset($user)) {
            $user->delete();
            $userLog->status = UserLog::Success;
            $userLog->info = 'User \'' . $user->username . '\' was deleted';
            $userLog->save();
        }
        return return_format(true);
    }

    public function actionGetSelfPasswordResetToken() {
        return return_format($this->actionGetPasswordResetToken(Yii::$app->user->identity->id));
        //return $this->actionGetPasswordResetToken(Yii::$app->user->identity->id);
    }

    //用户管理修改密码前获取秘钥
    public function actionGetPasswordResetToken($id) {
        isAPI();
        $user = User::findOne($id);
        if (empty($user)) {
            return return_format('该用户不存在');
        }
        $user->generatePasswordResetToken();
        $user->save();
        $data = [
            'uid' => $user->id,
            'token' => $user->password_reset_token,
        ];
        return return_format($data);
    }

    //重置自己的密码
    public function actionResetSelfPassword($token) {
        isAPI();
        $post = json_decode(Yii::$app->request->getRawBody(), true);
        $old_password = $post['old_password'];
        if (!Yii::$app->user->identity->validatePassword($old_password)) {
            return return_format('原密码错误');
        } else {
            return $this->resetPassword($token);
        }
    }

    //用户管理修改密码
    public function actionResetPassword($token) {
        isAPI();
        $user = User::findByPasswordResetToken($token);
        if ($user == null) {
            $username = 'unknown';
        } else {
            $username = $user->username;
        }
        $data = $this->resetPassword($token);
        $userLog = New UserLog();
        $userLog->type = UserLog::Type_resetPassword;
        if ($data['status'] == 0) {
            $userLog->status = UserLog::Success;
            $userLog->info = 'User ' . $username . '\'s password reset success';
        } else {
            $userLog->status = UserLog::Fail;
            $userLog->info = 'User ' . $username . '\'s password reset failure';
        }
        $userLog->save();
        return $data;
    }

    //改密码
    private function resetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            return return_format('密码重置失败');
            //throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(json_decode(Yii::$app->request->getRawBody(), true)) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return return_format(true);
        }
        return return_format('密码重置失败');
    }

}
