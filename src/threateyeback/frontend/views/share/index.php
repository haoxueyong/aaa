<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '共享';

?>
<!-- Main content -->
<section class="content share-content" ng-app="myApp" ng-controller="shareCtrl" style=" margin-top:19px;"> 
  <div class="row">
    <div class="form-group col-md-12" style="text-align: center;">
      <div class="input-group" style="max-width: 460px; ">
        <div class="seach-input">
          <input type="text" class="form-control" ng-model="searchWd" ng-keyup="myKeyup($event)" style="min-height:42px;">
          <a class="upload-icon" href="/share/add" data-toggle="tooltip" title="创建共享">
            <div>
              <i class="fa fa-upload"></i>
            </div>
          </a>
        </div>
        <div class="input-group-btn">
          <button type="button" class="btn btn-primary share-search" ng-click="search()" >搜索</button>
        </div>
      </div>
    </div>
  </div>
  <!--alert end -->
  <!--alert -->
  <div class="row ng-cloak">  
    <div class="col-md-12" ng-if="list.length == 0 && onload">
      <div class="no-identify col-md-7 col-md-offset-2  " style="margin-top:110px;">
        <div style="width:726px; margin:0 auto;">
          <img src="/images/no-identify.png" height="314" width="322" class="no-identify-img" >
          <p class="no-message text-light-blue">没有找到相关数据</p>
          <ul class="no-message-ul">
            <li>请检查输入是否有误</li>
            <li><a href="/site/index" style="text-decoration:underline;">返回首页</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-12" ng-if="list.length > 0">
      <div class="box content" style="padding:28px 20px 0px 20px;" ng-repeat="item in list">
        <div class="box-body " style="overflow: hidden; width: auto; ">
          <div class="item">
            <div class="share-prise-box">
              <div class="share-praise">
                <i class="fa fa-thumbs-up prise-icon"></i>
                <p style="margin-top:3px;"><i class="text-light-blue" ng-bind="item.lq"></i></p>
              </div>
              <a href="javascript:void(0);" class="praise-box" ng-click="like(item)" ng-bind="item.liked ? '取消点赞' : '点赞'"></a>

            </div>
            <div class="share-message" style="margin-bottom:5px;">
              <div class="share-title-h3 ">
                <a href="/share/{{item.id}}" class="name" ng-bind="item.name"></a>
                <?php if (Yii::$app->user->identity->role == 'admin') { ?>
                <button class="btn btn-xs btn-default" title="删除" style="margin-left: 10px;" ng-click="del(item,$index);"><i class="fa fa-trash-o"></i></button>
                <?php } ?>
                <span class="share-target"><span ng-bind="item.data.length"></span>条指标</span>
              </div>
            </div>
            <p class="share-message" style="margin-bottom:15px;">
              <span class="tag-box">
                <i class="fa fa-users" style="color:#ff8328;"></i>
                用户组：<span ng-bind="item.groupName"></span>
              </span>
              <span class="tag-box">
                <i class="fa fa-clock-o"></i>
                <span ng-bind="item.timeString"></span>
              </span>
              <span class="tag-box">
                <i class="fa fa-eye"></i>
                <span ng-bind="item.uv"></span>人浏览
              </span>
              <span class="tag-box">
                <i class="fa fa-commenting-o"></i>
                <span ng-bind="item.cq"></span>条评论
              </span>
            </p>
            <p class="share-message text-muted" style="text-indent:2em;" ng-bind="item.describe"></p>
            <p class="share-message" style="margin-bottom: 0px;">
              <span class="main-label tag-box" ng-repeat="tag in item.tagNames" ng-bind="tag"></span>
            </p>
          </div>
        </div> 
      </div> 


    </div>
  </div>
</section>
<!-- /.content -->


<script src="/js/controllers/share.js"></script>















































