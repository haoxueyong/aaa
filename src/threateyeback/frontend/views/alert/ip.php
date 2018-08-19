<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '可疑IP';
?>
<style type="text/css">
  
  .node {
    cursor: pointer;
  }

  .overlay{
      /*background-color:#FFF;*/
  }
   
  .node circle {
    fill: #fff;
    stroke: steelblue;
    stroke-width: 1.5px;
    
  }
   
  .node text {
    font-size:10px; 
    font-family:sans-serif;
  }
   
  .link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
  }

  .templink {
    fill: none;
    stroke: red;
    stroke-width: 3px;
  }

  .ghostCircle.show{
      display:block;
  }

  .ghostCircle, .activeDrag .ghostCircle{
       display: none;
  }

</style>
<section class="content" ng-app="myApp" ng-controller="myCtrl">

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-map-marker"></i> <?= $EX['IP'] ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-4 border-right">
              <div class="user-block">
                <!-- <img class="img-circle img-bordered-sm" src="/images/icos/file_detail.png" alt="user image"/> -->
                  <span class="ico fa fa-map-marker text-gray" ></span>
                  <span class="description">
                    <span>IP：</span>
                    <span><?= $EX['IP'] ?></span>
                  </span>
                  <span class="description">
                    <span>信心指数：</span>
                    <span><?= $EX['Detail']['threat_score'] ?></span>
                  </span>
              </div>
              <div class="user-block">
                <a href="/sensor/detail?sid=<?= $EX['SensorID']?>">
                  <span class="ico fa fa-laptop"></span>
                </a>

                <span class="description">
                  <span>计算机：</span>
                  <span><a href="/sensor/detail?sid=<?= $EX['SensorID']?>"><?= $EX['ComputerName'] ?></a></span>
                </span>
                <span class="description">
                  <span>IP：</span>
                  <span><?= $EX['SensorIP'] ?></span>
                </span>
                <span class="description">
                  <span>域：</span>
                  <span><?= $EX['SensorDomain'] ?></span>
                </span>
                <span class="description">
                  <span>组：</span>
                  <span><?= $groupOne['text'] ?></span>
                </span>
              </div>
              <div class="user-block" style="border-top: 1px solid #f4f4f4;">
                  <span class="ico fa fa-clock-o text-gray"></span>
                  <span class="description">
                    <span>过去一年中第一次发现：</span>
                    <span><?= date('Y-m-d H:i:s',$EX['miniTime']) ?></span>
                  </span>
                  <span class="description">
                    <span>最后一次发现：</span>
                    <span><?= date('Y-m-d H:i:s',$EX['maxTime']) ?></span>
                  </span>
                  <span class="description">
                    <span>告警ID：</span>
                    <span><?= $EX['AlertID'] ?></span>
                  </span>
              </div>
             
            </div>

            <div class="col-md-4 border-right">
            <!-- #f4f4f4 -->
              <div class="user-block">
                  <span class="description text">
                    <span>分类：</span>
                    <span><?= $EX['Detail']['category'] ?></span>
                  </span>
                  <span class="description text">
                    <span>全球第一次见：</span>
                    <span><?= $EX['Detail']['first_seen'] ?></span>
                  </span>
                  <span class="description text">
                    <span>流行度：</span>
                    <span class="text-yellow">
                      <?php for ($i=0; $i < 5; $i++) {
                        if($EX['Detail']['popularity'] > $i){
                          echo '<i class="fa fa-star"></i> ';
                        }else{
                          echo '<i class="fa fa-star-o"></i> ';
                        }
                      } ?>
                    </span>
                  </span>
                  <span class="description text">
                    <span>IP地址所在国家：</span>
                    <span><?= $EX['Detail']['ip_geo'] ?></span>
                  </span>
                  <span class="description text">
                    <span>访问这个IP的用户所在国家：</span>
                    <span><?= $EX['Detail']['users_geo'] ?></span>
                  </span>
                  <span class="description text">
                    <span>威胁来源：</span>
                    <?php
                      $SrcType_str = ['','邮件','网页','USB','内网',''];
                    ?>
                    <span><?= $SrcType_str[$EX['SrcType']] ?></span>
                  </span>
              </div>
            </div>
            <div class="col-md-4 ng-cloak">
              <?php if(Yii::$app->user->identity->role == 'admin'){?>
              <button ng-click="updata('setWhite')" class="btn btn-success" ng-if="EX.status != 4 && EX.status != 2">加入白名单</button>
              <button ng-click="updata('delWhite')" class="btn btn-danger" ng-if="EX.status == 3">移出白名单</button>
              <?php }else{?>
              <span class="btn btn-default disabled" ng-if="EX.status == 1">未解决</span>
              <?php }?>
              <span class="btn btn-default disabled" ng-if="EX.status == 2">已解决</span>
            </div>


          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" style="margin-bottom:-1px;">
          <li class="active">
            <a href="#proTree" data-toggle="tab" aria-expanded="true" ng-click="hideEventList = false">
              <i class="fa fa-code-fork"></i>
              感染途径</a>
            </li>
          <li>
            <a href="#nowSensor" data-toggle="tab" aria-expanded="false" ng-click="hideEventList = true">
              <i class="fa fa-bell"></i>
              目前感染的计算机
            </a>
          </li>
          <li>
            <a href="#oldSensor" data-toggle="tab" aria-expanded="false" ng-click="hideEventList = true">
              <i class="fa fa-clock-o"></i>
              历史感染过的计算机
            </a>
          </li>
        </ul>
        <div class="tab-content" style="padding-top: 0px;border-bottom: 0px;">


          <!-- proTree -->
          <div class="tab-pane active" id="proTree" >
            <div style="margin: 5px;">
              <button class="btn btn-primary btn-sm" id="plus"><i class="fa fa-search-plus"></i></button>
              <button class="btn btn-primary btn-sm" id="minus"><i class="fa fa-search-minus"></i></button>
              <button class="btn btn-primary btn-sm" id="reset">还原</button>
            </div>
            <div id="tree-container"></div>
            <?php include 'event_detail.php';?>
          </div>
          <!-- /proTree -->

          <!-- nowSensor -->
          <div class="tab-pane" id="nowSensor">
              <table class="table table-hover">
                  <tr>
                      <th>计算机名</th>
                      <th>状态</th>
                      <th>IP</th>
                      <th>操作系统</th>
                      <th>最近一次通讯时间</th>
                      <th>受保护起始时间</th>
                  </tr>
                  <?php foreach ($sensorNowList as $sensor): ?>
                  <tr style="cursor: pointer;" onclick="sensorDetail(<?= $sensor->SensorID?>)">
                      <td  class="<?= $sensor->isolate == 1 ? 'text-red' : ''?>"><?= $sensor->ComputerName ?></td>
                      <td>
                          <?php if($sensor->status == 0) { ?>
                          <span class="label label-danger">卸载</span>
                          <?php }if($sensor->status == 1) { ?>
                          <span class="label label-success">在线</span>
                          <?php }if($sensor->status == 2) { ?>
                          <span class="label label-warning">断线</span>
                          <?php } ?>
                      </td>
                      <td><?= $sensor->IP ?></td>
                      <td><?= $sensor->OSTypeShort ?></td>
                      <td><?= date('Y-m-d H:i:s',$sensor->updated_at) ?></td>
                      <td><?= date('Y-m-d H:i:s',$sensor->created_at) ?></td>
                  </tr>  
                  <?php endforeach ?>
              </table>
          </div>
          <!-- /nowSensor -->

          <!-- oldSensor -->
          <div class="tab-pane" id="oldSensor">
            <table class="table table-hover">
              <tr>
                  <th>计算机名</th>
                  <th>状态</th>
                  <th>IP</th>
                  <th>操作系统</th>
                  <th>最近一次通讯时间</th>
                  <th>受保护起始时间</th>
              </tr>
              <?php foreach ($sensorOldList as $sensor): ?>
              <tr style="cursor: pointer;" onclick="sensorDetail(<?= $sensor->SensorID?>)">
                  <td  class="<?= $sensor->isolate == 1 ? 'text-red' : ''?>"><?= $sensor->ComputerName ?></td>
                  <td>
                      <?php if($sensor->status == 0) { ?>
                      <span class="label label-danger">卸载</span>
                      <?php }if($sensor->status == 1) { ?>
                      <span class="label label-success">在线</span>
                      <?php }if($sensor->status == 2) { ?>
                      <span class="label label-warning">断线</span>
                      <?php } ?>
                  </td>
                  <td><?= $sensor->IP ?></td>
                  <td><?= $sensor->OSTypeShort ?></td>
                  <td><?= date('Y-m-d H:i:s',$sensor->updated_at) ?></td>
                  <td><?= date('Y-m-d H:i:s',$sensor->created_at) ?></td>
              </tr>  
              <?php endforeach ?>
            </table>
          </div>
          <!-- oldSensor -->
        </div>
      </div>
    </div>
  </div>

  <!--eventList -->
  <div class="row" ng-show="!hideEventList">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><i class="fa fa-clock-o"></i> 事件顺序</h3>
          <div class="box-tools">

            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="glyphicon glyphicon-download-alt">下载</i> 
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" role="menu" style="min-width: 100%;">
                <li>
                <a target="_black" href="download-json?id=<?= $EX['AlertID'] ?>">
                    JSON
                  </a>
                </li>
                <li>
                  <a target="_black" href="download-csv?id=<?= $EX['AlertID'] ?>">
                    CSV
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        <!-- box-body -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
              <tr>
                  <th></th>
                  <th>时间</th>
                  <th>事件</th>
                  <th>事件类型</th>
                  <th>计算机</th>
              </tr>
              <?php foreach ($downloadData as $data) { ?>
              <tr>
                  <td></td>
                  <td><?= $data['time'] ?></td>
                  <td><?= $data['event'] ?></td>
                  <td><?= $data['type'] ?></td>
                  <td>
                    <a href="/sensor/detail?sid=<?= $EX['SensorID'] ?>">
                      <?= $data['ComputerName'] ?>
                    </a>
                  </td>
              </tr>   
              <?php } ?>
              
            </table>
        </div>
        <div class="box-footer" style="height: 35px;">
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col-md-9 left -->
  </div>
  <!--eventList end -->
</section>
<script type="text/javascript">
  var EX = <?= json_encode($EX) ?>;
  var alertData = <?= json_encode($alertData) ?>;
</script>
<script src="/plugins/dndTree/d3.v3.min.js"></script>
<script src="/js/tree/eventTree.js"></script>
