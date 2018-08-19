<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class UserShare extends ActiveRecord {

    /**
     * UserShare model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%user_share}}';
    }

    public static function getOne($sid) {
        $userShare = UserShare::find()->where(['sid' => $sid, 'uid' => Yii::$app->user->identity->id,])->one();
        if (empty($userShare)) {
            $userShare = new UserShare();
            $userShare->sid = $sid;
            $userShare->uid = Yii::$app->user->identity->id;
        }
        return $userShare;
    }

}
