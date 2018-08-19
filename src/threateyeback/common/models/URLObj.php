<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class URLObj extends ActiveRecord {

    /**
     * URL model
     *
     * @property integer $id
     */
    const OK = 1;
    const Botnet = 2;
    const Malicious = 3;
    const Phishing = 4;

    public static function tableName() {
        return '{{%URL}}';
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
