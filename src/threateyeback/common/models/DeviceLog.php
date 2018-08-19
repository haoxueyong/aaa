<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%device_log}}".
 *
 * @property integer $id
 * @property integer $host
 * @property string $info
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class DeviceLog extends ActiveRecord {

    const STATUS = 1;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%device_log}}';
    }

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

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//                ['host', 'integer', 'message' => '主机'],
//                ['host', 'required', 'message' => 'host必填'],
//                ['info', 'required', 'message' => 'info必填'],
//                ['info', 'string', 'max' => 2, 'message' => '最长为2'],
        ];

        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
//        return [
//            'id' => 'ID',
//            'host' => 'Host',
//            'info' => 'Info',
//            'status' => 'Status',
//            'created_at' => 'Created At',
//            'updated_at' => 'Updated At',
//        ];
    }

    //保存设备日志的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
