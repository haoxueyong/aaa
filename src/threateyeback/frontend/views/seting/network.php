<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '网络配置';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="myCtrl">


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">


          <!-- email -->
          <div class="tab-pane active" id="email">
            <section class="seting-section">
              
              <div class="row seting-header">
                <h4 class="col-sm-2">网络设备：</h4>
                <div class="form-group col-sm-3">
                  <select ng-model="nowDevice" class="form-control" ng-options="item as devices[item].DEVICE for item in devicesKeys">
                  </select>
                </div>
              </div>

              <div class="row ng-cloak" ng-if="nowDevice">
                <div class="box-body">

                  <div class="form-group">
                    <label class="col-sm-4 control-label">是否启用</label>
                    <div class="col-sm-6">
                      <div class="pull-left">
                        <input class="tgl tgl-ios" id="ONBOOT" type="checkbox" ng-checked="devices[nowDevice].ONBOOT == 'yes'" ng-click="devices[nowDevice].ONBOOT = (devices[nowDevice].ONBOOT=='yes'?'no':'yes')" >
                        <label class="tgl-btn" for="ONBOOT"></label>
                      </div>
                    </div>
                  </div>

                  <div ng-if="devices[nowDevice].ONBOOT=='yes'">
                    <div class="form-group">
                      <label for="BOOTPROTO" class="col-sm-4 control-label">获取IP方式</label>
                      <div class="col-sm-4">
                        <select id="BOOTPROTO" ng-model="devices[nowDevice].BOOTPROTO" class="form-control" ng-options="item.value as item.text for item in [{value:'dhcp',text:'自动获取IP'},{value:'static',text:'手动设置IP'}]">
                        </select>
                      </div>
                      <div class="help-block text-red col-sm-4" ng-bind="!devices[nowDevice].BOOTPROTO ? '获取IP方式不为空' : ''"></div>
                    </div>

                    <div class="form-group" ng-if="devices[nowDevice].BOOTPROTO == 'static'">
                      <label for="IPADDR" class="col-sm-4 control-label">IP地址</label>
                      <div class="col-sm-4">
                        <input class="form-control" id="IPADDR" ng-model="devices[nowDevice].IPADDR">
                      </div>
                      <div class="help-block text-red col-sm-4" ng-bind="!isIPv4(devices[nowDevice].IPADDR) ? '请输入有效的IP地址' : ''"></div>
                    </div>

                    <div class="form-group" ng-if="devices[nowDevice].BOOTPROTO == 'static'">
                      <label for="NETMASK" class="col-sm-4 control-label">子网掩码</label>
                      <div class="col-sm-4">
                        <input class="form-control" id="NETMASK" ng-model="devices[nowDevice].NETMASK">
                      </div>
                      <div class="help-block text-red col-sm-4" ng-bind="!isIPv4(devices[nowDevice].NETMASK) ? '请输入有效的子网掩码' : ''"></div>
                    </div>

                    <div class="form-group" ng-if="devices[nowDevice].BOOTPROTO == 'static'">
                      <label for="GATEWAY" class="col-sm-4 control-label">默认网关</label>
                      <div class="col-sm-4">
                        <input class="form-control" id="GATEWAY" ng-model="devices[nowDevice].GATEWAY">
                      </div>
                      <div class="help-block text-red col-sm-4" ng-bind="!isIPv4(devices[nowDevice].GATEWAY) ? '请输入有效的网关' : ''"></div>
                    </div>

                    <div class="form-group" ng-if="devices[nowDevice].BOOTPROTO == 'static'">
                      <label for="DNS1" class="col-sm-4 control-label">首选DNS服务器</label>
                      <div class="col-sm-4">
                        <input class="form-control" id="DNS1" ng-model="devices[nowDevice].DNS1">
                      </div>
                      <div class="help-block text-red col-sm-4" ng-bind="devices[nowDevice].DNS1 && !isIPv4(devices[nowDevice].DNS1) ? '请输入有效的DNS服务器地址' : ''"></div>
                    </div>

                    <div class="form-group" ng-if="devices[nowDevice].BOOTPROTO == 'static'">
                      <label for="DNS2" class="col-sm-4 control-label">备用DNS服务器</label>
                      <div class="col-sm-4">
                        <input class="form-control" id="DNS2" ng-model="devices[nowDevice].DNS2">
                      </div>
                      <div class="help-block text-red col-sm-4" ng-bind="devices[nowDevice].DNS2 && !isIPv4(devices[nowDevice].DNS2) ? '请输入有效的DNS服务器地址' : ''"></div>
                    </div>

                  </div>

                </div>
              </div>
            </section>

            <section class="seting-section ng-cloak" ng-if="nowDevice && changed">
              
              <div class="row">
                <div class="col-sm-12">
                  <div class="pull-right margin">
                    <button class="btn btn-primary" ng-click="save()">保存</button>
                  </div>
                </div>
              </div>
            </section>
            
          </div>

          <!-- ./email -->

        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>

</section>
           
<!-- /.content -->
<script type="text/javascript">
var myApp = angular.module('myApp', []);
myApp.controller('myCtrl', function($scope, $http,$filter) {
  
  var isIPv4 = $scope.isIPv4 = function(ipv4){
    return /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/.test(ipv4);
  }

  $scope.validate = function(){
    var devices = $scope.devices;
    var nowDevice = $scope.nowDevice;
    if(devices[nowDevice].ONBOOT == 'no'){
      return true;
    }
    if(!devices[nowDevice].BOOTPROTO){
      return false;
    }
    if(devices[nowDevice].BOOTPROTO == 'static'){
      if(!isIPv4(devices[nowDevice].IPADDR)){
        return false;
      }
      if(!isIPv4(devices[nowDevice].NETMASK)){
        return false;
      }
      if(!isIPv4(devices[nowDevice].GATEWAY)){
        return false;
      }
      if(devices[nowDevice].DNS1 && !isIPv4(devices[nowDevice].DNS1)){
        return false;
      }
      if(devices[nowDevice].DNS2 && !isIPv4(devices[nowDevice].DNS2)){
        return false;
      }
    }
    return true;
  }

  $scope.save = function(){
    if(!$scope.validate()){
      return;
    }
    function doSave(){
      var staticKeys = ['IPADDR','NETMASK','GATEWAY','DNS1','DNS2'];
      var rqs_data = {};
      if($scope.devices[$scope.nowDevice].BOOTPROTO == 'dhcp'){
        rqs_data[$scope.nowDevice] = {};
        for (key in $scope.devices[$scope.nowDevice]) {
          var value = $scope.devices[$scope.nowDevice][key];
          if(staticKeys.indexOf(key) == -1){
            rqs_data[$scope.nowDevice][key] = value;
          }
        }
      }else{
        rqs_data[$scope.nowDevice] = $scope.devices[$scope.nowDevice];
      }
      var loading = zeroModal.loading(4);
      $http.post("/seting/set-network",rqs_data).then(function success(rsp){
        zeroModal.close(loading);
        if(rsp.data.status == 'success')
        {
          zeroModal.success('保存成功!');
          $scope.oldDevices[$scope.nowDevice]= angular.copy($scope.devices[$scope.nowDevice]);
          $scope.changed = false;
        }else{
          zeroModal.error('保存失败!');
        }
      },function err(rsp){
        zeroModal.close(loading);
        zeroModal.error('保存失败!');
      });
    }
    zeroModal.confirm({
      content: '确定保存网络配置吗？',
      okFn: function() {
        doSave();
      },
      cancelFn: function() {
      }
    });
  }
  var watching = false;
  $scope.init = function(){
    $scope.changed = false;
    $http.get('/seting/get-network').then(function success(rsp){
      if(rsp.data.status == 'success'){
        $scope.oldDevices = rsp.data.data;
        $scope.devices = angular.copy(rsp.data.data);
        $scope.devicesKeys = Object.keys($scope.devices);
        $scope.nowDevice = $scope.devicesKeys[0];

        if(!watching){
          $scope.$watch('nowDevice',function(newValue,oldValue, scope){
            $scope.devices[oldValue] = angular.copy($scope.oldDevices[oldValue]);
          });
          $scope.$watch('devices',function(newValue,oldValue, scope){
            var device = newValue[$scope.nowDevice];
            var oldDevice = $scope.oldDevices[$scope.nowDevice];
            for (key in device) {
              if(device[key] != oldDevice[key]){
                $scope.changed = true;
                return;
              }
            }
            $scope.changed = false;
          },true);
          watching = true;
        }
      }
    },function err(rsp){

    });
  }
  $scope.init();
});
</script>












































