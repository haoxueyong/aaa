<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '勒索软件';
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
<section class="content">

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bell-o"></i> 勒索软件</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-4 border-right">

              <div class="user-block">
                <a href="#">
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
                  <span></span>
                </span>
                <span class="description">
                  <span>首次告警时间：</span>
                  <span><?= date('Y-m-d H:i:s',$EX['created_at']) ?></span>
                </span>
                <span class="description">
                  <span>最近告警时间：</span>
                  <span><?= date('Y-m-d H:i:s',$EX['Timestamp']/1000) ?></span>
                </span>
              </div>
             
            </div>
            <div class="col-md-8 border-right">
            <!-- #f4f4f4 -->
              <div class="user-block">
                <span class="description text">
                  <span>进程ID：</span>
                  <span><?= $alertData['EventList'][0]['PID'] ?></span>
                </span>
                <span class="description text">
                  <span>路径：</span>
                  <span><?= $alertData['EventList'][0]['ImagePath'] ?></span>
                </span>
              </div>
            </div>
          </div>
          <pre id="code" style="display: none;"></pre>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  var alertData = <?= json_encode($alertData) ?>;
  code.innerHTML = JSON.stringify(alertData,null,2);
</script>
