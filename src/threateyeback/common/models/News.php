<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class News extends ActiveRecord {

    /**
     * News model
     *
     * @property integer $id
     */
    const STATUS_DEL = 0;
    const STATUS_UNREAD = 1;
    const STATUS_READ = 2;
    const TYPE_OVERDUE = 1;
    const TYPE_OVERRUN = 2;

    public static function tableName() {
        return '{{%news}}';
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

    public function send($userList) {
        foreach ($userList as $user) {
            $oldNews = self::find()->where(['type' => $this->type, 'title' => $this->title, 'content' => $this->content, 'uid' => $user->id])->one();
            if (empty($oldNews)) {
                $news = new News();
                $news->type = $this->type;
                $news->title = $this->title;
                $news->content = $this->content;
                $news->uid = $user->id;
                $news->save();
            }
        }
    }

}
