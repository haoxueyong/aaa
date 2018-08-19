<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models;

/**
 * Description of UploadForm
 *
 * @author yong
 */
use Yii;
use yii\web\UploadedFile;

class Upload extends \yii\db\ActiveRecord {

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [["file"], "file",],
        ];
    }

}
