<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '共享详情';
?>
<section class="content" ng-app="myApp" ng-controller="myCtrl">
  <style type="text/css">
    .table>tbody>tr>th{
      border-top: 0px solid;
    }
    .table {
        margin-bottom: 0;
    }
    .nav-tabs-custom>.nav-tabs>li{
      cursor: pointer;
    }
    .table>tbody>tr>td.value{
      padding: 3px;
    }
    .list-group-item:first-child,.list-group-item:last-child{
      border-radius:0px;
    }
    .form-control {
      border-radius: 5px;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">
            <i class="fa fa-share-alt"></i> 
            <span ng-bind="share.name"></span>
          </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6 border-right">
              <ul class="nav nav-stacked sensor-detail">
                <li>
                  <span class="sensor-detail-title">共享者</span>
                  <span ng-bind="share.username"></span>
                </li>
                <li>
                  <span class="sensor-detail-title">用户组</span>
                  <span ng-bind="share.groupName"></span>
                </li>
              </ul>
            </div>
            <div class="col-md-6 border-right">
              <ul class="nav nav-stacked sensor-detail">
                <li>
                  <span class="sensor-detail-title">创建时间</span>
                  <span ng-bind="share.created_at*1000 | date : 'yyyy-MM-dd HH:mm'"></span>
                </li>
                <li>
                  <span class="sensor-detail-title">修改时间</span>
                  <span ng-bind="share.updated_at*1000 | date : 'yyyy-MM-dd HH:mm'"></span>
                </li>
              </ul>
            </div>
          </div>


          <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
              <ul class="nav nav-stacked sensor-detail" style="border-top: 1px solid #f4f4f4;">
                <div>
                  <span class="sensor-detail-title">标签</span>
                  <span>
                    <div class="alert alert-info alert-dismissible group-lable ng-cloak" ng-repeat="item in share.tagNames">
                      <span ng-bind="item"></span>
                    </div>
                  </span>
                </div>
              </ul>
              <ul class="nav nav-stacked sensor-detail" style="border-top: 1px solid #f4f4f4;">
                <div class="margin" style="text-indent:2em;">
                  <span class="text-muted" ng-bind="share.describe"></span>
                </div>
              </ul>
              <div id="hide_box" style="display: none;">
                <div id="groupTree"></div>
              </div>
            </div>
          </div>


        </div>
      </div>

      <div class="box box-solid collapsed-box">
        <div class="box-header with-border">
          <h3 class="box-title">
            <i class="fa fa-random"></i> 
            <span>指标信息</span>
          </h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-plus"></i>
            </button>
            <a href="<?= Yii::$app->params['staticUrl'].$share['filePath'] ?>" download class="btn btn-box-tool">
              <i class="fa fa-download" ></i>
            </a>
            <button type="button" class="btn btn-box-tool" title="保存修改" ng-if="changed" ng-click="save()">
              <i class="fa fa-save"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div class="row margin">
            <table class="table table-hover ng-cloak">
              <tr >
                  <th>序号</th>
                  <th>指标值</th>
                  <th>指标类型</th>
                  <th>威胁度</th>
                  <th>置信度</th>
              </tr>

              <tr style="cursor: pointer;" ng-repeat="item in share.data">
                  <td ng-bind="$index+1"></td>
                  <td ng-bind="item.indicators"></td>
                  <td ng-bind="item.type"></td>
                <?php if (Yii::$app->user->identity->id == $share['uid']) { ?>
                  <td class="value">
                    <input class="form-control input-sm" type="number" max="5" min="0" ng-model="item.threat"/>
                  </td>
                  <td class="value">
                    <input class="form-control input-sm" type="number" max="100" min="0" ng-model="item.confidence"/>
                  </td>
                <?php }else{ ?>
                  <td ng-bind="item.threat"></td>
                  <td ng-bind="item.confidence"></td>
                <?php }?>

              </tr>
            </table>
          </div>
            
        </div>
      </div>

    </div>
  </div>

  <style type="text/css">
    .comment-renderer-text-content {
      overflow: hidden;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
  </style>
  <div class="row" style="margin-right: 10px">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-body">
          <div class="form-group">
            <textarea class="form-control" rows="3" id="comment" placeholder="请输入评论内容..." style="border-radius: 0" ng-model="newComment.content"></textarea>
          </div>
          <div style="text-align: right;">
            <button class="btn btn-success btn-sm" ng-class="newComment.content ? '' : 'disabled'" ng-click="addComment();">发送评论</button> 
          </div>
        </div>
      </div>
    </div>
  </div>



  <div class="row ng-cloak" ng-if="commentList.length > 0">
        <div class="col-md-12">
          <ul class="timeline">

            <li ng-repeat="item in commentList" ng-class="item.type == 'label' ? 'time-label' : ''">
              <span ng-class="item.color" ng-if="item.type == 'label'" ng-bind="item.timeString"></span>
              <i class="fa fa-comments bg-aqua" ng-if="item.type != 'label'"></i>
              <div class="timeline-item" ng-if="item.type != 'label'">
                <span class="time"><i class="fa fa-clock-o"></i> <span ng-bind="item.timeString"></span></span>

                <h3 class="timeline-header"><a href="javascript:void(0);" ng-bind="item.username"></a></h3>

                <div class="timeline-body comment-renderer-text-content" ng-bind="item.content" ></div>
                <?php if (Yii::$app->user->identity->role == 'admin') { ?>
                <div class="timeline-footer" style="text-align: right;">
                  <a class="btn btn-danger btn-xs" ng-click="delComment(item);">删除</a>
                </div>
                <?php } ?>
              </div>
            </li>
            
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>
        </div>
        <!-- /.col -->
      </div>
</section>

<script>
  var share_old = <?= json_encode($share) ?>;
  var share = <?= json_encode($share) ?>;
</script>
<script src="/js/controllers/share-detail.js"></script>
