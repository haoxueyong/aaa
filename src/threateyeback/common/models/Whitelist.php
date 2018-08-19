<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%white_list}}".
 *
 * @property integer $created_at
 * @property integer $updated_at
 */
class Whitelist extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%white_list}}';
    }

    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ]
            ]
        ];
    }

    //添加白名单（单个）
    public function whiteListAdd($post_data) {
        $data = redisCommunication('WhitelistAdd', $post_data);
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        $model = new self();
        $exist = $model::find()->where(['and', ['=', 'indicator', $post_data['indicator']], ['=', 'alert_type', $post_data['alert_type']]])->exists();
        if ($exist) {
            return '请勿重复添加';
        }
        $model->indicator = $post_data['indicator'];
        $model->alert_type = $post_data['alert_type'];
        $model->create_time = date('Y-m-d H:i:s', time());
        $model->save();
        return true;
    }

    //添加白名单（多个）
    public function whiteListAdds($post_data) {
        $data = redisCommunication('WhitelistAdd', json_encode($post_data));
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        $un_exist = [];
        $exist = [];
        $model = new self();
        $timestamp = time();
        $time = date('Y-m-d H:i:s', time());
        foreach ($post_data as $value) {
            $exist = $model::find()->where(['and', ['=', 'indicator', $value['indicator']], ['=', 'alert_type', $value['alert_type']]])->exists();
            if (!$exist) {
                $value['create_time'] = $time;
                $value['created_at'] = $timestamp;
                $value['updated_at'] = $timestamp;
                array_push($un_exist, $value);
            }
        }
        //数据批量入库
        if ($un_exist) {
            $connection = \Yii::$app->db;
            $connection->createCommand()->batchInsert('white_list', ['indicator', 'alert_type', 'create_time', 'created_at', 'updated_at'], $un_exist)->execute();
        }
        return true;
    }

    //导入白名单（IOC）
    public function whiteListIOCImport($file_contents_arr) {
        $analysis_data = [];
        //解析文件内容
        foreach ($file_contents_arr as $value) {
            $alert_type = '';
            switch ($value['Context']['@attributes']['search']) {
                case 'FileItem/Md5sum':
                    $alert_type = 'MD5';
                    break;
                case 'RouteEntryItem/Destination':
                    $alert_type = 'IP';
                    break;
                case 'UrlHistoryItem/URL':
                    $alert_type = 'URL';
                    break;
                case 'Network/DNS':
                    $alert_type = 'URL';
                    break;
                default:
                    goto cont;
            }
            array_push($analysis_data, ['indicator' => $value['Content'], 'alert_type' => $alert_type]);
            cont:
        }
        $return = self::whiteListAdds($analysis_data);
        return $return;
    }

    //删除白名单（单个）
    public function whiteListDel($del_data) {
        $data = redisCommunication('WhitelistDel', $del_data);
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        Whitelist::deleteAll(['id' => $del_data['id']]);
        return true;
    }

    //保存的方法
    public function save($runValidation = true, $attributeNames = null) {
        return parent::save();
    }

}
