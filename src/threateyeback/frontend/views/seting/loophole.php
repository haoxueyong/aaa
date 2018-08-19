<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '防御设置';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="myCtrl">


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">



          <!-- loophole -->
          <div class="tab-pane active" id="loophole">
            

            <section class="seting-section">
              <h4 class="seting-header">漏洞防御</h4>

              <div class="row seting-body">

                <div class="col-sm-12">
                    <span class="text-blue pull-left">是否拦截漏洞被利用的行为</span>
                    <div class="pull-right">
                      <input class='tgl tgl-ios' id='Loophole' type='checkbox' ng-checked="item.Loophole" ng-click="setItem('Loophole')" >
                      <label class='tgl-btn' for='Loophole'></label>
                    </div>
                </div>

              </div>
            </section>

            <section class="seting-section">
              <h4 class="seting-header">黑名单</h4>

              <div class="row seting-body">

                <div class="col-sm-12">
                    <span class="text-blue">是否启用黑名单</span>
                    <div class="pull-right">
                      <input class='tgl tgl-ios' id='EnableBlackList' type='checkbox' ng-checked="item.EnableBlackList" ng-click="setItem('EnableBlackList')" >
                      <label class='tgl-btn' for='EnableBlackList'></label>
                    </div>
                </div>

              </div>
            </section>
           
          </div>
          <!-- ./loophole -->


        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>

</section>

<script type="text/javascript">
var myApp = angular.module('myApp', []);
myApp.controller('myCtrl', function($scope, $http,$filter) {
  $http.get('/config/getloophole').then(function success(rsp){
    if(rsp.data.status == 'success'){
      $scope.item = rsp.data;
    }
  },function err(rsp){
  });
  $scope.setItem = function(key){
    $scope.item[key] = !$scope.item[key];
    var post_data = {
      Loophole:$scope.item.Loophole,
      EnableBlackList:$scope.item.EnableBlackList
    };
    $http.post('/config/setloophole',post_data).then(function success(rsp){
      if(rsp.data.status == 'success'){
        $scope.item = rsp.data;
      }
    },function err(rsp){
    });
  }
});
</script>
           
<!-- /.content -->










































