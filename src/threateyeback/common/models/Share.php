<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;



class Share extends ActiveRecord
{
    /**
     * Share model
     *
     * @property integer $id
     */

    public $liked = 0;
    public $read = 0;
    public $commented = 0;

    public static function tableName()
    {
        return '{{%share}}';
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
            'tagNames',
        ];
        $value = parent::__get($name);
        if(in_array($name, $jsonName))
        {
            $value = json_decode($value,true);
        }
        return $value;
    }

    public static function page($page = 1,$rows = 15,$wds = []){
        $page = (int)$page;
        $rows = (int)$rows;
        $data = self::list(($page-1)*$rows,$rows,$wds);
        $data['maxPage'] = ceil($data['count']/$rows);
        $data['pageNow'] = $page > $data['maxPage'] ? $data['maxPage'] : $page;
        return $data;
    }

    public static function createQuery($wds = []){
        $user = Yii::$app->user->identity;
        if($user->role == 'admin'){
            $groups = Group::find()->select('id')->asArray()->all();
        }else{
            $groups = ArrayHelper::toArray($user->getGroups());
        }
        $gids = array_column($groups,'id');
        $query = Share::find()
            ->where([
                'or',
                ['share.uid' => $user->id],
                ['gid' => $gids],
                ['gid' => 0]
            ]);
        $query
            ->select('share.*,user_share.read,liked,commented')
            ->leftJoin('user_share','user_share.sid = share.id and user_share.uid ='.$user->id);
        if(count($wds)){
            $query->andWhere([
                'or',
                ['or like', 'name', $wds],
                ['or like', 'tagString', $wds],
                ['or like', 'describe', $wds]
            ]);
        }
        $query->orderBy([
            'id' => SORT_DESC,
        ]);
        return $query;
    }

    public static function list($offSet = 0,$limit = 1,$wds = []){
        $query = self::createQuery($wds);
        $count = (int)$query->count();
        $listData = $query->offSet($offSet)->limit($limit)->all();
        $listData = ArrayHelper::toArray($listData, [
            'common\models\Share' => [
                'id',
                'name',
                'uid',
                'username',
                'tagNames',
                'gid',
                'groupName',
                'filePath',
                'describe',
                'pv',
                'uv',
                'cq',
                'lq',
                'data',
                'status',
                'created_at',
                'updated_at',
                'liked' => function ($item) {
                    return (int)$item->liked;
                },
                'read' => function ($item) {
                    return (int)$item->read;
                },
                'commented' => function ($item) {
                    return (int)$item->read;
                },
            ],
        ]);
        $data = [
            'data' => $listData,
            'count' => $count,
            'status' => 'success',
        ];
        return $data;
    }

    public static function add($data){
        $share = new Share();
        $share->name = $data['name'];
        $share->uid = Yii::$app->user->identity->id;
        $share->username = Yii::$app->user->identity->username;
        $share->gid = $data['gid'];
        $share->groupName = $data['groupName'];
        $share->data = $data['data'];
        $share->filePath = $data['filePath'];
        $share->tagNames = $data['tagNames'];
        $share->tagString = implode(",", $data['tagNames']);
        $share->describe = $data['describe'];
        $share->save();
        foreach ($data['tagNames'] as $tagName) {
            $tag = Tag::find()->where(['name' => $tagName])->one();
            if(empty($tag)){
                $tag = new Tag();
                $tag->name = $tagName;
                $tag->save();
            }
            $shareTag = new ShareTag();
            $shareTag->sid = $share->id;
            $shareTag->shareName = $share->name;
            $shareTag->tid = $tag->id;
            $shareTag->tagName = $tag->name;
            $shareTag->save();
        }
        return $share;
    }

    public static function del($data){
        self::deleteAll(['id' => $data['id']]);
        Comment::deleteAll(['sid' => $data['id']]);
        UserShare::deleteAll(['sid' => $data['id']]);
        $query = self::createQuery($data['wds']);
        $count = (int)$query->count();
        return $count;
    }

    public static function updateShareCQ($sid)
    {
        $share = Share::findOne($sid);
        if(empty($share)){
            return null;
        }
        $share->cq = Comment::find()->where(['sid' => $sid])->count();
        $share->save();
        return $share;
    }

    private function updateShareUV()
    {
        $this->uv = (int)UserShare::find()->where([
                    'sid' => $this->id,
                    'read' => 1,
                ])->count();
        $this->save();
        return $this;
    }

    private function updateShareLQ()
    {
        $this->lq = (int)UserShare::find()->where([
                    'sid' => $this->id,
                    'liked' => 1,
                ])->count();
        $this->save();
        return $this;
    }

    public static function readOne($id){
        $share = self::createQuery()->andWhere(['share.id' => $id])->one();
        if(isset($share)){
            $share->pv++;
            $userShare = UserShare::getOne($id);
            $userShare->read = 1;
            $userShare->save();
            $share->updateShareUV();
        }
        return $share;
    }

    public static function like($data){
        $share = Share::findOne($data['id']);
        if(isset($share)){
            $userShare = UserShare::getOne($data['id']);
            $userShare->liked = $data['liked'];
            $userShare->save();
            $share->updateShareLQ();
        }
        $share = ArrayHelper::toArray($share);
        $share['liked'] = $userShare->liked;
        $share['read'] = $userShare->read;
        $share['commented'] = $userShare->commented;
        return $share;
    }
    
}


















