<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\Group;
use common\models\UserLog;
use common\models\Config;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        if (Config::getLicense()['validLicenseCount'] == 0) {
            $rules = [
                ['actions' => [], 'allow' => true, 'roles' => ['admin']]
            ];
        } else {
            $rules = [
                ['actions' => [], 'allow' => true, 'roles' => ['?']],
                ['actions' => [], 'allow' => true, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
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

    public $enableCsrfValidation = false;

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
        return Json::encode(return_format('该页面不存在', 404));
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return Json::encode(return_format('已登录', 202));
        }
        $admin = User::find()->where(['role' => 'admin'])->one();
        if (empty($admin)) {
            $model = new SignupForm();
            if ($model->load(json_decode(Yii::$app->request->getRawBody(), true), 'LoginForm')) {
                if ($user = $model->signup()) {
                    if (Yii::$app->getUser()->login($user)) {
                        //return $this->goHome();
                        return Json::encode(return_format('已登录', 202));
                    }
                }
            }
            return Json::encode(return_format('未注册', 207));
        } else {
            $model = new LoginForm();
            //如果没有参数
            if (!json_decode(Yii::$app->request->getRawBody(), true)) {
                return Json::encode(return_format('未登录', 204));
            }
            if ($model->load(json_decode(Yii::$app->request->getRawBody(), true)) && $model->login()) {
                return Json::encode(return_format(true));
            } else {
                return Json::encode(return_format($model->getErrors(), 1));
            }
        }
    }

    /**
     * Logs out the current user.
     * 退出登录
     * @return mixed
     */
    public function actionLogout() {
        $userLog = new UserLog();
        $userLog->type = UserLog::Type_Logput;
        $userLog->status = UserLog::Success;
        $userLog->info = 'Successful logout';
        $userLog->save();
        Yii::$app->user->logout();
        return Json::encode(return_format(true));
    }

    /**
     * Signs user up.
     * 注册用户(应该是没有用的)
     * @return mixed
     */
//    public function actionSignup() {
//        $model = new SignupForm();
//        if ($model->load(Yii::$app->request->post())) {
//            if ($user = $model->signup()) {
//                if (Yii::$app->getUser()->login($user)) {
//                    return $this->goHome();
//                }
//            }
//        }
//        return $this->render('signup', ['model' => $model]);
//    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
//    public function actionRequestPasswordReset() {
//        $model = new PasswordResetRequestForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail()) {
//                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
//                return $this->goHome();
//            } else {
//                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
//            }
//        }
//        return $this->render('requestPasswordResetToken', ['model' => $model]);
//    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
//    public function actionResetPassword($token) {
//        try {
//            $model = new ResetPasswordForm($token);
//        } catch (InvalidParamException $e) {
//            throw new BadRequestHttpException($e->getMessage());
//        }
//        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
//            Yii::$app->session->setFlash('success', 'New password saved.');
//            return $this->goHome();
//        }
//        return $this->render('resetPassword', ['model' => $model]);
//    }
}
