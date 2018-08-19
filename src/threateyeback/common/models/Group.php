<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\GroupSensor;

class Group extends ActiveRecord {

    /**
     * Group model
     *
     * @property integer $id
     */
    const TYPE_NONAUTO = 0;
    const TYPE_AUTO = 1;

    public static function tableName() {
        return '{{%group}}';
    }

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $jsonName = ['FilterList',];
        $value = parent::__get($name);
        if (in_array($name, $jsonName)) {
            $value = json_decode($value, true);
        }
        return $value;
    }

    public function setLevel() {
        $pGroup = self::findOne($this->pid);
        if (isset($pGroup)) {
            $this->level = $pGroup->level + 1;
        } else {
            $this->level = 0;
        }
    }

    public function getSensorList() {
        switch ($this->type) {
            case self::TYPE_NONAUTO:
                return $this->hasMany(Sensor::className(), ['id' => 'sid'])->viaTable('group_sensor', ['gid' => 'id'])->all();
            case self::TYPE_AUTO:
                $query = Sensor::find();
                $whereList = $this->FilterList;
                if (count($whereList) > 0) {
                    foreach ($whereList as $key => $item) {
                        if (stripos($item['value'], '*') === false) {
                            $where = [$item['key'] => $item['value']];
                        } else {
                            $like_str = str_replace("*", "%", $item['value']);
                            $where = ['like', $item['key'], $like_str, false];
                        }
                        if ($key == 0) {
                            $query = $query->where($where);
                        } else {
                            $query = $query->andWhere($where);
                        }
                    }
                }
                return $query->all();
        }
    }

    public function getSensorIDList() {
        return array_column($this->getSensorList(), 'SensorID');
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
