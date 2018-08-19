<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ProFile extends ActiveRecord {

    /**
     * @var UploadedFile file attribute 
     */
    public $file;

    /**
     * @return array the validation rules. 
     */
    public function rules() {
        return [
            [['file'], 'file'],
        ];
    }

    /**
     * ProFile model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%profile}}';
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

}
