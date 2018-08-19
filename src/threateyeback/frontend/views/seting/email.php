<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '邮件通知';
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
              <h4 class="seting-header">邮件服务器</h4>
              <div class="row">
                <div class="box-body">
                  <div class="form-group">
                    <label for="host" class="col-sm-4 control-label">SMTP服务器</label>
                    <div class="col-sm-6">
                      <input class="form-control" id="host" placeholder="smtp.163.com" ng-model="item.host">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="port" class="col-sm-4 control-label">SMTP端口</label>
                    <div class="col-sm-6">
                      <input type="number" class="form-control" id="port" placeholder="25" ng-model="item.port">
                    </div>
                  </div>


                  <div class="form-group">
                    <label class="col-sm-4 control-label">SMTP启用安全连接SSL</label>
                    <div class="col-sm-6">
                      <div class="pull-left">
                        <input class="tgl tgl-ios" id="encryption" type="checkbox" ng-checked="item.encryption == 'ssl'" ng-click="item.encryption = (item.encryption=='ssl'?'':'ssl')" >
                        <label class="tgl-btn" for="encryption"></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>



            <section class="seting-section">
              <h4 class="seting-header">发件邮箱账号</h4>
              <div class="row">
                <div class="box-body">
                  <div class="form-group">
                    <label for="username" class="col-sm-4 control-label">邮箱地址</label>
                    <div class="col-sm-6">
                      <input type="email" class="form-control" id="username" placeholder="test@163.com" ng-model="item.username">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="password" class="col-sm-4 control-label">邮箱密码</label>
                    <div class="col-sm-6">
                      <input type="password" class="form-control" id="password" ng-model="item.password">
                    </div>
                  </div>
                </div>
              </div>
            </section>


            <section class="seting-section">
              <h4 class="seting-header">收件邮箱账号</h4>
              <div class="row">
                <div class="box-body">
                  <div class="form-group">
                    <label for="alertEmail" class="col-sm-4 control-label">邮箱地址</label>
                    <div class="col-sm-6">
                      <input type="email" class="form-control" id="alertEmail" placeholder="test@163.com" ng-model="item.alertEmail">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-4 control-label">发生告警时给此邮件发送通知邮件</label>
                    <div class="col-sm-6">
                      <div class="pull-left">
                        <input class="tgl tgl-ios" id="send" type="checkbox" ng-checked="item.send" ng-click="item.send=(item.send?false:true)" >
                        <label class="tgl-btn" for="send" ></label>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </section>


            <section class="seting-section">
              
              <div class="row">
                <div class="col-sm-12">
                  <div class="pull-right margin">
                    <button class="btn btn-primary" ng-click="save()" <?= Yii::$app->user->identity->role == 'admin' ? '' : 'disabled'?> >保存</button>
                  </div>
                  <div class="pull-right margin">
                    <button class="btn btn-default" ng-click="test()" <?= Yii::$app->user->identity->role == 'admin' ? '' : 'disabled'?> >发送测试邮件</button>
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
  $http.get('/email/get').then(function success(rsp){
    if(rsp.data.status == 'success'){
      $scope.item = rsp.data;
      delete $scope.item.status;
    }
  },function err(rsp){
  });

  $scope.validate = function(type){
    var flag = false;
    if(type == 'test'){
      flag = ($scope.item.username && $scope.item.alertEmail);
    }else if(type == 'save'){
      flag = ($scope.item.username && (!$scope.item.send || $scope.item.alertEmail));
    }
    return flag;
  }
  $scope.test = function(){
    if(!$scope.validate('test')){
      zeroModal.error('请输入有效的邮箱!');
      return;
    }
    rqs_data = $scope.item;
    var loading = zeroModal.loading(4);
    $http.post("/email/test",rqs_data).then(function success(rsp){
      zeroModal.close(loading);
      if(rsp.data.status == 'success')
      {
        zeroModal.success('邮件发送成功!');
      }else{
        zeroModal.error('邮件发送失败!');
      }
    },function err(rsp){
      zeroModal.close(loading);
      zeroModal.error('邮件发送失败!');
    });
  }
  $scope.save = function(){
    if(!$scope.validate('save')){
      zeroModal.error('请输入有效的邮箱!');
      return;
    }
    rqs_data = $scope.item;
    var loading = zeroModal.loading(4);
    $http.post("/email/save",rqs_data).then(function success(rsp){
      zeroModal.close(loading);
      if(rsp.data.status == 'success')
      {
        zeroModal.success('保存成功!');
      }else{
        zeroModal.error('保存失败!');
      }
    },function err(rsp){
      zeroModal.close(loading);
      zeroModal.error('保存失败!');
    });
  }
});
</script>












































