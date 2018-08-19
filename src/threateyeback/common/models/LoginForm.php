<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\UserLog;

/**
 * Login form
 */
class LoginForm extends Model {

    public $username;
    public $password;
    public $rememberMe = false;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            // username and password are both required
            [['username'], 'required', 'message' => '用户名不能为空'],
            [['password'], 'required', 'message' => '密码不能为空'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['username', 'validateUsername'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误' . $user->fail_num . '次');
            }
        }
    }

    public function validateUsername($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, '此账户不存在。');
            } elseif ($user->unlock_time > time()) {
                $this->addError($attribute, '此账户已被锁定，请在' . date("Y-m-d H:i:s", $user->unlock_time) . '后再试。');
            } elseif ($user->role != 'admin' && Config::getLicense()['validLicenseCount'] == 0) {
                $this->addError($attribute, '无有效许可证，请联系管理员!');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        $userLog = new UserLog();
        $userLog->username = $this->username;
        $userLog->type = UserLog::Type_Login;
        if ($this->validate()) {
            $userLog->status = UserLog::Success;
            $userLog->info = 'Successful login from ' . $_SERVER["REMOTE_ADDR"];
            $ret = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            $userLog->status = UserLog::Fail;
            $userLog->info = 'Failed login from ' . $_SERVER["REMOTE_ADDR"];
            $ret = false;
        }
        $user = $this->getUser();
        if ($user) {
            if (!$ret) {
                if ($user->unlock_time <= time()) {
                    $user->fail_num++;
                }
                if ($user->fail_num > 10) {
                    $user->fail_num = 1;
                    // $user->unlock_time = time() + 86400;
                    $user->unlock_time = time() + 30;
                }
            } else {
                $user->fail_num = 1;
            }
            $user->save();
        }
        $userLog->save();
        return $ret;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser() {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

}
