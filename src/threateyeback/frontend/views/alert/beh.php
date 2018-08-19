<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '可疑行为';
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
          <h3 class="box-title"><i class="fa fa-bell-o"></i> 可疑行为</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-4 border-right">

              <div class="user-block">
                <a href="/sensor/detail?sid=<?= $EX['SensorID'] ?>">
                  <span class="ico fa fa-windows text-aqua"></span>
                </a>

                <span class="description">
                  <span>计算机：</span>
                  <span><a href="/sensor/detail?sid=<?= $EX['SensorID'] ?>"><?= $EX['ComputerName'] ?></a></span>
                </span>
                <span class="description">
                  <span>IP：</span>
                  <span><?= $EX['SensorIP'] ?></span>
                </span>
                <span class="description">
                  <span>域：</span>
                  <span></span>
                </span>
                <span class="description">
                  <span>组：</span>
                  <span><?= $groupOne['text'] ?></span>
                </span>
                <span class="description">
                  <span>检测时间：</span>
                  <span><?= date('Y-m-d H:i:s',$EX['created_at']) ?></span>
                </span>
                <span class="description">
                  <span>告警ID：</span>
                  <span><?= $EX['AlertID'] ?></span>
                </span>
              </div>
            </div>
            <div class="col-md-4 ng-cloak">
              <div class="user-block">
                <span class="description text">
                  <span>威胁来源：</span>
                  <?php
                    $SrcType_str = ['','邮件','网页','USB','内网',''];
                  ?>
                  <span><?= $SrcType_str[$EX['SrcType']] ?></span>
                </span>
              </div>
              <?php if(Yii::$app->user->identity->role == 'admin'){?>
              <button ng-click="updata('setWhiteBeh')" class="btn btn-success" ng-if="EX.status != 4 && EX.status != 2">例外</button>
              <button ng-click="updata('delWhiteBeh')" class="btn btn-danger" ng-if="EX.status == 4">取消例外</button>
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
      <div class="box">
        <div class="box-header">
          <h2 class="box-title"><i class="fa fa-code-fork"></i> 感染途径</h2>
        </div>
        <!-- /.box-header -->
        <!-- box-body -->
        <div class="box-body table-responsive no-padding">
          <!-- proTree -->
          <div class="tab-pane active" id="proTree" >
            <div style="margin: 10px 0 10px 20px;">
              <button class="btn btn-primary btn-sm" id="plus"><i class="fa fa-search-plus"></i></button>
              <button class="btn btn-primary btn-sm" id="minus"><i class="fa fa-search-minus"></i></button>
              <button class="btn btn-primary btn-sm" id="reset">还原</button>
            </div>
            <div id="tree-container"></div>
            <?php include 'event_detail.php';?>
          </div>
          <!-- /proTree -->
      </div>
    </div>


  </div>
    

  </div>

  <!--eventList -->
  <div class="row">
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
  var EventList = eval(JSON.stringify(alertData.EventList));
  function sensorDetail(SensorID){
      location.href = "/sensor/detail?sid="+SensorID;
  }
</script>
<script src="/plugins/dndTree/d3.v3.min.js"></script>
<script src="/js/tree/eventTree.js"></script>
