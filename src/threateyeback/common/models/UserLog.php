<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class UserLog extends ActiveRecord {

    /**
     * UserLog model
     *
     * @property integer $id
     */
    const Type_Login = 0;
    const Type_Signup = 1;
    const Type_Logput = 3;
    const Type_AddUser = 4;
    const Type_DelUser = 5;
    const Type_EngineUP = 6;
    const Type_EngineDown = 7;
    const Type_resetPassword = 8;
    const Fail = 0;
    const Success = 1;

    public static function tableName() {
        return '{{%user_log}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    public function save($runValidation = true, $attributeNames = null) {
        if ($this->username === null) {
            $user = Yii::$app->user->identity;
            if (empty($user)) {
                $this->username = '';
            } else {
                $this->username = $user->username;
            }
        }
        return parent::save();
    }

}
