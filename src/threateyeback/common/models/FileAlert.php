<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\FileObj;

class FileAlert extends ActiveRecord {

    /**
     * FileAlert model
     *
     * @property integer $id
     */
    public static function tableName() {
        return '{{%file_alert}}';
    }

    public static function addFileList($alert) {
        if ($alert->AlertType != Alert::Type_File) {
            return;
        }
        foreach ($alert->AlertFileList as $key => $fileArr) {
            if ($fileArr['status'] == FileObj::IsWhite) {
                continue;
            }
            $fileObj = FileObj::find()->where(['MD5' => $fileArr['MD5'], 'SHA256' => $fileArr['SHA256']])->one();
            if (empty($fileObj)) {
                $fileObj = new FileObj();
                $fileObj->MD5 = $fileArr['MD5'];
                $fileObj->SHA256 = $fileArr['SHA256'];
                $fileObj->status = $fileArr['status'];
                $fileObj->save();
            }
            $file_alert = FileAlert::find()->where(['FileID' => $fileObj->id, 'AlertID' => $alert->id, 'FilePath' => $fileArr['FilePath']])->one();
            if (empty($file_alert)) {
                $file_alert = new FileAlert();
                $file_alert->FileID = $fileObj->id;
                $file_alert->AlertID = $alert->id;
            }
            if ($alert->IsSolveBy3rd == 1) {
                $file_alert->IsSolveBy3rd = 1;
                $file_alert->status = 2;
            }
            $file_alert->FilePath = $fileArr['FilePath'];
            $file_alert->EventID = $fileArr['EventID'];
            $file_alert->Level = $fileArr['Level'];
            $file_alert->Detail = $fileArr['Detail'];
            $file_alert->save();
        }
    }

    public static function change($type, $fileArr) {
        if ($type == 'setWhite') {
            $fileObj = FileObj::find()->where(['MD5' => $fileArr['MD5'], 'SHA256' => $fileArr['SHA256']])->one();
            $fileObj->status = FileObj::IsWhite;
            $fileObj->save();
            self::updateAll(['status' => 3], 'FileID = ' . $fileObj->id);
        } else {
            self::updateAll(['status' => 2], 'id = ' . $fileArr['rid']);
        }
    }

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        parent::__set($name, $value);
    }

    public function __get($name) {
        $jsonName = ['Detail',];
        $value = parent::__get($name);
        if (in_array($name, $jsonName)) {
            $value = json_decode($value, true);
        }
        return $value;
    }

}
