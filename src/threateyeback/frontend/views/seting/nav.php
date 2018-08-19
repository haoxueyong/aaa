<?php
function isActiveNav($path)
{
    $url = explode('?',Yii::$app->request->getUrl())[0];
    $url = rtrim($url, '/'); 
    if($url == $path)
    {
        return 'active';
    }else
    {
        return '';
    }
}
function getPathNav($path)
{
    $url = explode('?',Yii::$app->request->getUrl())[0];
    $url = rtrim($url, '/'); 
    if($url == $path)
    {
        return 'javascript:void(0);';
    }else
    {
        return $path;
    }

}
?>
<ul class="nav nav-tabs">
          <li class="<?= isActiveNav('/seting/index') ?>">
            <a href="<?= getPathNav('/seting/index') ?>">基本设置</a>
          </li>
          <li class="<?= isActiveNav('/seting/filelist') ?>">
            <a href="<?= getPathNav('/seting/filelist') ?>">黑白名单</a>
          </li>
          <li class="<?= isActiveNav('/seting/profile') ?>">
            <a href="<?= getPathNav('/seting/profile') ?>">配置文件</a>
          </li>
          <li class="<?= isActiveNav('/seting/sensor') ?>">
            <a href="<?= getPathNav('/seting/sensor') ?>">探针文件</a>
          </li>
          <li class="<?= isActiveNav('/seting/user') ?>">
            <a href="<?= getPathNav('/seting/user') ?>">用户管理</a>
          </li>
          <li class="<?= isActiveNav('/seting/group') ?>">
            <a href="<?= getPathNav('/seting/group') ?>">组管理</a>
          </li>
          <li class="<?= isActiveNav('/seting/loophole') ?>">
            <a href="<?= getPathNav('/seting/loophole') ?>">防御设置</a>
          </li>
          <li class="<?= isActiveNav('/seting/email') ?>">
            <a href="<?= getPathNav('/seting/email') ?>">邮件通知</a>
          </li>
          <li class="<?= isActiveNav('/seting/network') ?>">
            <a href="<?= getPathNav('/seting/network') ?>">网络配置</a>
          </li>
          <li class="<?= isActiveNav('/seting/license') ?>">
            <a href="<?= getPathNav('/seting/license') ?>">许可证</a>
          </li>
          <li class="<?= isActiveNav('/seting/log') ?>">
            <a href="<?= getPathNav('/seting/log') ?>">系统日志</a>
          </li>
        </ul>