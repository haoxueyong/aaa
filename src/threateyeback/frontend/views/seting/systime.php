<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '系统时间';
?>
  <link rel="stylesheet" href="/css/systime.css">
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="systime" >

<div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content" ng-cloak>

          <!-- systime-->
        <div class="head_name">
                <span class="head_mid_title">配置方式:</span>
                <label>
                    <select class="form-control input_style" 
                    ng-change = "typeChange()"
                    ng-model="selectedName"  ng-options="x.value as x.name for x in datatype"></select>
                </label>
        </div>
         <div class="head_name">
                <span class="head_mid_title">日期选择:</span>
                <label>
                   <input type="text" ng-click="custom()" class="form-control input_style" id="reservationtime" readonly 
                   ng-disabled="timedisabled">
                </label>
        </div>
        <div class="head_name">
                <span class="head_mid_title">时区选择:</span>
                <label>
                <select-search  zonedisabled = 'zonedisabled' searchvalue ='zoneresult' datas="datas"></select-search>
                </label>
               
        </div>
         <div class="head_name">
                <span class="head_mid_title">NTP时间:</span>
                <label>
                    <input class="form-control input_style" type="text" ng-model="ntpTime" 
                    ng-disabled="ntpdisabled">
                </label>
        </div>
         <!-- <div class="head_name">
                <span class="head_mid_title">时区选择:</span>
        </div> -->
        <!-- <div id="map"></div> -->
        <div class="button_save">
        <button class="btn btn-primary" ng-click="save()">保存</button>
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
</section>

           

<script type="text/javascript" src="/js/controllers/systime.js"></script>

