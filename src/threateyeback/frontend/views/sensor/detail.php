<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '计算机详情';
?>
<section class="content" ng-app="myApp" ng-controller="myCtrl">

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title" ng-class="sensor.isolate == 1 ? 'text-red' : ''">
            <i class="fa fa-laptop"></i> 
            <span ng-bind="sensor.ComputerName"></span>
          </h3>
          <div class="box-tools">
            <button type="button" style="margin-left: 60px;" class="btn btn-box-tool" ng-click="showSensorInfo()" data-toggle="tooltip" title="查看软件列表">
              <i class="fa fa-list"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6 border-right">
              <ul class="nav nav-stacked sensor-detail">
                <li>
                  <span class="sensor-detail-title">操作系统</span>
                  <span ng-bind="sensor.OSType"></span>
                </li>
                <li>
                  <span class="sensor-detail-title">所在域</span>
                  <span ng-bind="sensor.Domain"></span>
                </li>
                <li>
                  <span class="sensor-detail-title">IP地址</span>
                  <span ng-bind="sensor.IP"></span>
                </li>
                <li>
                  <span class="sensor-detail-title">配置文件</span>
                  <span ng-bind="sensor.ProfileVersion"></span>
                </li>
              </ul>
            </div>
            <div class="col-md-6 border-right">
              <ul class="nav nav-stacked sensor-detail">
                <li>
                  <span class="sensor-detail-title">状态</span>
                  <span class="label label-{{status_str[sensor.status].css}}" ng-bind="status_str[sensor.status].label" ng-if="sensor.work != 1"></span>
                  <span class="label label-warning" ng-if="sensor.work == 1">请等待...</span>
                </li>
                <li>
                  <span class="sensor-detail-title">隔离</span>
                  <span>
                    <?php if(Yii::$app->user->identity->role == 'admin'){?>
                    <input class="tgl tgl-ios" id="isolate" type="checkbox" ng-checked="sensor.isolate == 1" ng-click="isolate();" ng-disabled="sensor.work == 1 || sensor.status != 1">
                    <?php }else{?>
                    <input class="tgl tgl-ios" id="isolate" type="checkbox" ng-checked="sensor.isolate == 1" ng-click="isolate();" ng-disabled="true">
                    <?php }?>
                    <label class="tgl-btn" for="isolate"></label>
                  </span>
                </li>
                <li>
                  <span class="sensor-detail-title">Sensor版本</span>
                  <span ng-bind="sensor.SensorVersion"></span>
                </li>
                <li>
                  <span class="sensor-detail-title">SensorID</span>
                  <span ng-bind="sensor.SensorID"></span>
                </li>
              </ul>
            </div>
          </div>
          <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
              <ul class="nav nav-stacked sensor-detail" style="border-top: 1px solid #f4f4f4;">
                <div>
                  <span class="sensor-detail-title">分组</span>

                  <span>
                    <span class="group-lable">
                      <a href="javascript:void(0);" ng-click="add();">加入分组</a>
                    </span>
                    <div class="alert alert-info alert-dismissible group-lable ng-cloak" ng-repeat="item in sensor.groupList">
                      <span ng-bind="item.text"></span>
                      <span class="close" ng-click="del(item.id)">×</span>
                    </div>
                  </span>
                </div>
              </ul>
              <div id="hide_box" style="display: none;">
                <div id="groupTree"></div>
                <table id="SoftwareInfoList" class="table table-hover selectSensor" >
                  <thead>
                      <tr>
                          <th>软件名称</th>
                          <th>软件版本</th>
                      </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="item in sensor.info.SoftwareInfoList">
                        <td ng-bind="item.DisplayName"></td>
                        <td ng-bind="item.DisplayVersion"></td>
                    </tr>
                    <tr ng-if="!sensor.info.SoftwareInfoList.length">
                        <td colspan="2">未检测到软件信息，请稍后查看</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
var GroupList = <?= json_encode($GroupList) ?>;
var rootScope;
var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope,$http,$filter) {
  rootScope = $scope;
  $scope.status_str = [
      {
          css:'danger',
          label:'卸载'
      },{
          css:'success',
          label:'在线'
      },{
          css:'warning',
          label:'断线'
      }
  ];
  
  $scope.isolate_str = ['text-blue','text-gray'];
  $scope.pause_str = ['glyphicon-play text-green','glyphicon-pause text-yellow'];
  $scope.work_str = ['','glyphicon-hourglass text-yellow','glyphicon-ok text-green','glyphicon-remove text-red'];
  $scope.scan_str = ['text-gray','text-blue'];
  function GetQueryString(name)
  {
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
  }
  $scope.isolate = function(){
    if($scope.sensor.work == 1){
      return;
    }
    $scope.sensor.work = 1;
    $scope.sensor.isolate = $scope.sensor.isolate == 1 ? 0 : 1;
    var types = ['ISOLATE_DOWN','ISOLATE_UP'];
    rqs_data = {
      type : types[$scope.sensor.isolate],
      SensorIDList:[$scope.sensor.SensorID]
    };
    var loading = zeroModal.loading(4);
    $http.post('sendbase',rqs_data).then(function success(rsp){
      zeroModal.close(loading);
    },function err(rsp){
      zeroModal.close(loading);
    });
  }
  $scope.getOne = function(){
    $http.get('getone?sid='+GetQueryString('sid')+'&temp='+(new Date()).getTime()).then(function success(rsp){
      $scope.pageGeting = true;
      if(rsp.data.status!="success")
      {
        return;
      }
      if(rsp.data.sensor.work != 1){
        $scope.sensor = rsp.data.sensor;
      }
      $scope.pageGeting = false;
    },function err(rsp){
      $scope.pageGeting = false;
    });
  }

  $scope.del = function(gid){
    var rqs_data = {
      gid:gid,
      sid:$scope.sensor.id
    };
    var loading = zeroModal.loading(4);
    $http.post('delgroup',rqs_data).then(function success(rsp){
      if(rsp.data.status == "success")
      {
        $scope.sensor.groupList = rsp.data.groupList;
      }else{
        zeroModal.error('分组删除失败!');
      }
      zeroModal.close(loading);
    },function err(rsp){
      zeroModal.error('分组删除失败!');
      zeroModal.close(loading);
    });
  }

  $scope.showSensorInfo = function(){
    var W = $(".content").width()*0.8;
    var H = (W/16)*9;
    zeroModal.show({
        title: $scope.sensor.ComputerName,
        content: SoftwareInfoList,
        width: W+"px",
        height: H+"px",
        cancel: true,
        onCleanup: function() {
            hide_box.appendChild(SoftwareInfoList);
        }
    });
  }

  $scope.add = function(){
    var W = 480;
    var H = 360;
    zeroModal.show({
        title: '请选计算机分组！',
        content: groupTree,
        width: W+"px",
        height: H+"px",
        ok: true,
        cancel: true,
        okFn: function() {
          if(!$scope.nowGroup){
              zeroModal.error('请选计算机分组！');
              return false;
          }
          var rqs_data = {
            gid:$scope.nowGroup.id,
            sid:$scope.sensor.id
          };
          var loading = zeroModal.loading(4);
          $http.post('addgroup',rqs_data).then(function success(rsp){
            if(rsp.data.status == "success")
            {
              $scope.sensor.groupList = rsp.data.groupList;
            }else{
              zeroModal.error('此计算机已经在这个分组中了!');
            }
            zeroModal.close(loading);
          },function err(rsp){
            zeroModal.error('分组加入失败!');
            zeroModal.close(loading);
          });
        },
        onCleanup: function() {
            hide_box.appendChild(groupTree);
        }
    });
  }

  $scope.pageGeting = false;
  $scope.getOne();
  setInterval(function(){
      if(!$scope.pageGeting)
      {
        $scope.getOne();
      }
  },5000);
});


var GroupTree = [];
var Groups = {};

for (var i = 0; i < GroupList.length; i++) {
  var group = GroupList[i];
  Groups[group.id] = group;
  group.type = ''+group.type;
  if(group.pid == 0)
  {
    GroupTree.push(group);
  }else{
    if(!Groups[group.pid].nodes){
      Groups[group.pid].nodes = [];
    }
    Groups[group.pid].nodes.push(group);
  }
}
var treeDom;

function updateTree(){
  treeDom = $('#groupTree').treeview({
    color: "#428bca",
    data: GroupTree,
    onNodeSelected: function(event, node) {
      rootScope.nowGroup = node;
      rootScope.$apply();
    },
    onNodeUnselected: function (event, node) {
      rootScope.nowGroup = null;
      rootScope.$apply();
    }
  });
  var nodes = treeDom.treeview('getUnselected');
  Nodes = {};
  for (var i = nodes.length - 1; i >= 0; i--) {
    var node = nodes[i];
    Nodes[node.id] = node;
  }
}
window.onload = function(){
  updateTree();
};

</script>













































