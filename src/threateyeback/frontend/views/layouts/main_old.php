<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);


function inUrl($urls)
{
    $url = Yii::$app->request->getUrl();
    if(in_array($url, $urls))
    {
        return $url;
    }else
    {
        return '/null/null';
    }
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <link rel="stylesheet" href="/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/css/zeroModal.css">
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'iCatch',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $ss = ['/site/test1','/site/2','/site/test','/site/3','/site/4',];
    $url = $ss[0];
    
    $menuItems = [
        ['label' => '总览', 'url' => ['/site/index']],
        ['label' => '威胁', 'url' => [inUrl(['/alert/index','/alert/news'])],
            'items' =>  [
                            ['label' => '告警计算机', 'url' => ['/alert/index']],
                            ['label' => '未处理告警', 'url' => ['/alert/news']],
                        ],
        ],
        ['label' => '安全调查', 'url' => ['/site/contact']],
        ['label' => '计算机','url' => ['/sensor/index']],
        ['label' => '设置','url' => [inUrl(['/sensor/version','/user/list'])],
            'items' =>  [
                            ['label' => '探针管理', 'url' => ['/sensor/version']],
                            ['label' => '用户管理', 'url' => ['/user/list']],
                        ],
        ],
    ];
    if (Yii::$app->user->isGuest) {
        // $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '退出 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; HooHooLab <?= date('Y') ?></p>

        <p class="pull-right">技术支持<a href="http://www.hoohoolab.com/" rel="external">虎特信息科技(上海)有限公司</a></p>
    </div>
</footer>

<?php $this->endBody() ?>

<script src="/js/zeroModal.min.js"></script>
<!-- Fileupload -->
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
</body>
</html>
<?php $this->endPage() ?>
