<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;



class Comment extends ActiveRecord
{
    /**
     * Comment model
     *
     * @property integer $id
     */

    public static function tableName()
    {
        return '{{%comment}}';
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

    public static function add($data){
        $comment = new Comment();
        $comment->uid = Yii::$app->user->identity->id;
        $comment->username = Yii::$app->user->identity->username;
        $comment->sid = $data['sid'];
        $comment->content = $data['content'];
        $comment->save();
        Share::updateShareCQ($comment->sid);
        return $comment;
    }

    public static function del($data){
        Comment::deleteAll(['id' => $data['id']]);
        Share::updateShareCQ($data['sid']);
        return Share::updateShareCQ($data['sid'])->cq;
    }

    public static function list($offset = 0,$limit = 15,$sid){
        $query = Comment::find()
            ->where(['sid' => $sid])
            ->orderBy('id');
        $count = (int)$query->count();
        $commentList = $query->offSet($offset)->limit($limit)->all();
        $data = [
            'data' => ArrayHelper::toArray($commentList),
            'count' => $count,
        ];
        return $data;
    }
}


















