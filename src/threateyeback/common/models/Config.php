<?php

namespace common\models;

use Yii;
//use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

//use common\models\FileAlert;
//use common\models\IPAlert;

class Config extends ActiveRecord {

    /**
     * Config model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%config}}';
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

    public static function getLoophole() {
        $data = Yii::$app->cache->get("Loophole");
        if ($data) {
            return $data;
        }
        $conf = self::find()->where(['key' => 'Loophole'])->one();
        if (empty($conf)) {
            $conf = new Config();
            $conf->key = 'Loophole';
            $conf->value = ['Loophole' => false, 'EnableBlackList' => false,];
            $conf->save();
        }
        $data = $conf->value;
        Yii::$app->cache->set("Loophole", $data);
        return $data;
    }

    public static function setLoophole($value) {
        $conf = self::find()->where(['key' => 'Loophole'])->one();
        $conf->value = $value;
        $conf->save();
    }

    public static function getEmailPwd() {
        $data = Yii::$app->cache->get("Email");
        if ($data) {
            return $data;
        }
        $conf = self::find()->where(['key' => 'Email'])->one();
        if (empty($conf)) {
            $conf = new Config();
            $conf->key = 'Email';
            $conf->value = [
                'encryption' => 'ssl',
                'host' => '',
                'port' => 25,
                'username' => '',
                'password' => '',
                'alertEmail' => '',
                'send' => false,
            ];
            $conf->save();
        }
        $email_data = $conf->value;
        Yii::$app->cache->set("Email", $email_data);
        return $email_data;
    }

    //获取邮箱配置参数
    public static function getEmail() {
        $data = self::getEmailPwd();
        $data['password'] = '';
        return $data;
    }

    //保存配置的邮件参数
    public static function setEmail($value) {
        $conf = self::find()->where(['key' => 'Email'])->one();
        $conf->value = $value;
        $conf->save();
        Yii::$app->cache->set("Email", $value);
    }

    public static function getBase() {
        $data = Yii::$app->cache->get("Base");
        if ($data) {
            return $data;
        }
        $conf = self::find()->where(['key' => 'Base'])->one();
        if (empty($conf)) {
            $conf = new Config();
            $conf->key = 'Base';
            $conf->value = [
                'FileHashing' => 'MD5',
                'WhiteList' => [],
                'PromptMessage' => '本计算机由于安全原因被隔离，如有问题，请联系系统管理员。',
            ];
            $conf->save();
        }
        $data = $conf->value;
        Yii::$app->cache->set("Base", $data);
        return $data;
    }

    public static function setBase($value) {
        $conf = self::find()->where(['key' => 'Base'])->one();
        $conf->value = $value;
        $conf->save();
    }

    private static function addLicenseStatus($data) {
        $data['validLicenseCount'] = 0;
        foreach ($data['list'] as &$value) {
            if ($value['endTime'] > time() * 1000) {
                $value['status'] = '已授权';
                $data['validLicenseCount'] += 1;
            } else {
                $value['status'] = '已过期';
            }
        }
        return $data;
    }

    //获取所有的证书
    public static function getLicense() {
        $data = Yii::$app->cache->get("License");
//         p($data);die;
        if ($data) {
            return self::addLicenseStatus($data);
        }
        $conf = self::find()->where(['key' => 'License'])->one();

        if (empty($conf)) {
            $conf = new Config();
            $conf->key = 'License';
            $conf->value = ['list' => [], 'validLicenseCount' => 0,];
            $conf->save();
        }
        $data = $conf->value;
        Yii::$app->cache->set("License", $data);
        return self::addLicenseStatus($data);
    }

    public static function setLicense($value) {
        $conf = self::find()->where(['key' => 'License'])->one();
        $conf->value = $value;
        $conf->save();
    }

    public static function getConfig() {
        $data = Yii::$app->cache->get("config");
        if ($data) {
            return $data;
        }
        $Loophole = self::getLoophole();
        $Base = self::getBase();
        $data['FileHashing'] = $Base['FileHashing'];
        $data['Loophole'] = $Loophole['Loophole'];
        if ($Loophole['EnableBlackList']) {
            $data['BlackList'] = FileObj::find()->where(['status' => FileObj::BlackList])->asArray()->all();
        } else {
            $data['BlackList'] = [];
        }
        $data['WhiteList'] = FileObj::find()->where(['status' => [FileObj::WhiteList, FileObj::IsWhite]])->asArray()->all();
        Yii::$app->cache->set("config", $data);
        return $data;
    }

    public function __set($name, $value) {
        if ($name == 'value' && is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $value = parent::__get($name);
        if ($name == 'value') {
            $json = json_decode($value, true);
            if (is_array($json)) {
                $value = $json;
            }
        }
        return $value;
    }

    public function save($runValidation = true, $attributeNames = null) {
        $ret = parent::save();
        Yii::$app->cache->delete($this->key);
        Yii::$app->cache->delete('config');
        return $ret;
    }

}
