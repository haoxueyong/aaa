<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '设置';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="myCtrl">


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">


          <!-- base -->
          <div class="tab-pane active" id="base">

            <section class="seting-section">
              <h4 class="seting-header">文件哈希</h4>

              <div class="row seting-body">

                <div class="col-sm-12">
                  <span class="text-blue pull-left">文件哈希算法</span>
                  <div class="pull-right">
                    <input name='FileHashing' type='radio' id="isMD5" ng-checked="item.FileHashing == 'MD5'" ng-click="setHashing('MD5');" style="margin: 4px 4px 4px 15px" />
                    <label for="isMD5">MD5</label>
                    <input name='FileHashing' type='radio' id="isSHA256" ng-checked="item.FileHashing == 'MD5/SHA256'" ng-click="setHashing('MD5/SHA256');" style="margin: 4px 4px 4px 15px" />
                    <label for="isSHA256">MD5+SHA256</label>
                  </div>
              </div>
            </section>

            <section class="seting-section">
              <h4 class="seting-header">一键隔离</h4>
              <div class="row">
                <div class="col-sm-12">
                  <b class="margin">提醒用户计算机被隔离</b>
                  <div class="margin">
                    <em>当计算机被隔离时，显示下面的信息(最多255个字节)</em>
                    <textarea class="form-control" rows="3" maxlength="255" style="background-color: #f6f6f6;resize: none;" ng-model="item.PromptMessage"></textarea>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <b class="margin">设置隔离的IP白名单</b>
                  <div class="margin" style="background-color: #fbf6e1;color: #c09853;padding: 15px;border-radius: 5px;">
                    <b><i class="fa fa-exclamation-triangle"></i>请注意：</b><span>计算机被隔离后，该计算机只能和下面IP列表中的设备通讯，请确保设置的IP地址中包含了所有需要的设备的IP地址，比如和您指定的计算机完成通讯需要的VPN、代理、DHCP以及DNS的IP地址等。</span>
                  </div>
                  <div class="margin">
                    <em>IP地址(用逗号隔开)</em>
                    <input class="form-control" style="background-color: #f6f6f6;resize: none;" ng-model="item.WhiteListStr" />
                    <div class="pull-right margin">
                      <button class="btn btn-primary" <?= Yii::$app->user->identity->role == 'admin' ? '' : 'disabled'?> ng-click="setIsolate();">保存</button>
                    </div>
                  </div>
                </div>
              </div>
            </section>


            <section class="seting-section">
              <h4 class="seting-header">探针升级IP设置</h4>
              <div class="row">
                <div class="col-sm-12">
                  <b class="margin">管理端IP</b>
                  <div class="margin" style="background-color: #fbf6e1;color: #c09853;padding: 15px;border-radius: 5px;">
                    <b><i class="fa fa-exclamation-triangle"></i>请注意：</b><span>请确保探针所在设备与管理端IP链接畅通。</span>
                  </div>
                  <div class="margin">
                    <input class="form-control" style="background-color: #f6f6f6;resize: none;" ng-model="params.selfIP" />
                  </div>
                  <b class="margin">引擎IP</b>
                  <div class="margin" style="background-color: #fbf6e1;color: #c09853;padding: 15px;border-radius: 5px;">
                    <b><i class="fa fa-exclamation-triangle"></i>请注意：</b><span>请确保管理端与引擎IP链接畅通，如果管理端和引擎部署在同一台设备时可填写[127.0.0.1]。</span>
                  </div>
                  <div class="margin">
                    <input class="form-control" style="background-color: #f6f6f6;resize: none;" ng-model="params.engineIP" />
                    <div class="pull-right margin">
                      <button class="btn btn-primary" <?= Yii::$app->user->identity->role == 'admin' ? '' : 'disabled'?> ng-click="setIP();">保存</button>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- ./base -->

        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>

</section>

<script type="text/javascript">
function isIP(ip)   
{   
    var re =  /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/   
    return re.test(ip);   
}
var myApp = angular.module('myApp', []);
myApp.controller('myCtrl', function($scope, $http,$filter) {
  $http.get('/config/getbase').then(function success(rsp){
    if(rsp.data.status == 'success'){
      $scope.item = rsp.data;
      $scope.item.WhiteListStr = $scope.item.WhiteList.join(",");
    }
  },function err(rsp){
  });
  $scope.getParams = function(){
    $http.get('/seting/get-params').then(function success(rsp){
      if(rsp.data.status == 'success'){
        if($scope.params){
          if($scope.params.selfIP == rsp.data.params.selfIP && $scope.params.engineIP == rsp.data.params.engineIP){
            zeroModal.success('服务器IP设置成功！');
            $scope.params = rsp.data.params;
          }else{
            zeroModal.error('服务器IP设置失败！');
          }
        }else{
          $scope.params = rsp.data.params;
        }
      }
    },function err(rsp){
    });
  }

  $scope.GetSetIpRes = function(CommandID){
    setTimeout(function(){
      $http.post('/seting/get-set-ip-res?CommandID='+CommandID+'&t='+(new Date()).getTime(),$scope.params).then(function success(rsp){
        console.log(rsp.data);
        if(rsp.data.status == 'success' && rsp.data.CommandStatus == 1){
          $scope.getParams();
        }else{
          $scope.GetSetIpRes(CommandID);
        }

      },function err(rsp){
        $scope.GetSetIpRes(CommandID);
      });
    },1000);
  }

  $scope.setIP = function(){
    if(isIP($scope.params.selfIP) && isIP($scope.params.engineIP)){
      zeroModal.confirm({
        content: '您确定要保存服务器IP吗？',
        contentDetail: '保存成功后管理端和引擎会短暂断开连接！',
        okFn: function() {
          $http.post('/seting/set-ip',$scope.params).then(function success(rsp){
            if(rsp.data.status == 'success'){
              $scope.GetSetIpRes(rsp.data.CommandID);
            }else{
              zeroModal.error('服务器IP设置失败！');
            }
          },function err(rsp){
            zeroModal.error('服务器IP设置失败！');
          });
        },
        cancelFn: function() {
        }
      });
    }else{
      zeroModal.error('请输入正确的IP地址!');
    }
  }
  $scope.getParams();
  

  $scope.setIsolate = function(){
    var IPerror = false;
    $scope.item.WhiteListStr = $scope.item.WhiteListStr.replace(/，/g, ",");
    $scope.item.WhiteList = $scope.item.WhiteListStr.split(',');
    var ipList = [];
    for (var i = $scope.item.WhiteList.length - 1; i >= 0; i--) {
      var IP = $scope.item.WhiteList[i] = $scope.item.WhiteList[i].trim();
      if(ipList.indexOf(IP) != -1 || IP == ''){
        continue;
      }
      ipList.unshift(IP);
      if(!isIP(IP))
      {
        IPerror = true;
      }
    }
    $scope.item.WhiteList = ipList;
    if(IPerror){
      zeroModal.error('请输入正确的IP地址!');
      return;
    }
    var post_data = {
      WhiteList:$scope.item.WhiteList,
      PromptMessage:$scope.item.PromptMessage
    };
    var loading = zeroModal.loading(4);
    $http.post('/config/setbase',post_data).then(function success(rsp){
      if(rsp.data.status == 'success'){
        $scope.item.WhiteList = rsp.data.WhiteList;
        $scope.item.PromptMessage = rsp.data.PromptMessage;
        $scope.item.WhiteListStr = $scope.item.WhiteList.join(",");
        zeroModal.success('修改成功!');
      }else{
        zeroModal.error('修改失败!');
      }
      zeroModal.close(loading);
    },function err(rsp){
      zeroModal.close(loading);
      zeroModal.error('修改失败!');
    });
  }


  $scope.setHashing = function(value){
    if($scope.item.FileHashing == value){
      return;
    }
    $scope.item.FileHashing = value;
    var post_data = {
      FileHashing:$scope.item.FileHashing
    };
    $http.post('/config/setbase',post_data).then(function success(rsp){
      if(rsp.data.status == 'success'){
        $scope.item.FileHashing = rsp.data.FileHashing;
      }
    },function err(rsp){
    });
  }
});
</script>
<!-- /.content -->












































