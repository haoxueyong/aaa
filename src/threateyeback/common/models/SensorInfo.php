<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\Sensor;
use common\models\Logger;



class SensorInfo extends ActiveRecord
{
    /**
     * SensorInfo model
     *
     * @property integer $id
     */

    public static function tableName()
    {
        return '{{%sensorInfo}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],              
            ],
        ];
    }
   
    public function __set($name, $value)
    {
        if(is_array($value))
        {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name)
    {
        $jsonName = [
            'data',
        ];
        $value = parent::__get($name);
        if(in_array($name, $jsonName))
        {
            $value = json_decode($value,true);
        }
        return $value;
    }
}
