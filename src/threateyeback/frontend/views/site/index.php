<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '总览';
?>
                <!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-laptop"></i> 可疑计算机</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <canvas id="sensorTopChart"></canvas>
            </div>
            <div class="col-md-6">
              <canvas id="sensorChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>



    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-file-o"></i> 可疑文件</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <canvas id="fileTopChart"></canvas>
            </div>
            <div class="col-md-6">
              <canvas id="fileChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-object-group"></i> 分类统计</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            
            <div class="col-md-6" >
              <div id="OSData" style="min-width:400px;height:400px"></div>
            </div>

            <div class="col-md-6" >
              <a href="javascript:changeGroup();" style="position: absolute;z-index: 10;right: 20px;"><i class="fa fa-filter"></i> 选择分组</a>
              <div id="GroupData" style="min-width:400px;height:400px"></div>
            </div>

            
          </div>
          <div id="hide_box" style="display: none;">
            <div id="groupTree"></div>
          </div>
        </div>
      </div>
    </div>

  </div>

</section>
<script type="text/javascript">
var GroupList = <?= json_encode($GroupList) ?>;
</script>
<script src="/js/controllers/index.js"></script>















































