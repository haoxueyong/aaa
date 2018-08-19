<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '探针管理';
?>
                <!-- Main content -->
            <section class="content"  ng-app="myApp" ng-controller="myCtrl">
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-primary">
                    <div class="box-header">
                      <h3 class="box-title">计算机</h3>
                      <div class="box-tools">
                        <button type="button" ng-click="createdVersion()" class="btn btn-primary btn-sm">上传新版本</button>
                      </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                      <table class="table table-hover" ng-show="pages.data.length>0">
                        <tr>
                          <th style="text-align:center;">序号</th>
                          <th>版本</th>
                          <th>文件名</th>
                          
                          <th>上传时间</th>
                          <th>下载</th>
                          <th style="text-align:center;">操作</th>
                        </tr>

                        <tr style="cursor: pointer;" ng-repeat="version in pages.data" ng-click="detail($index)">
                            <td style="text-align: center;" ng-bind="$index+1"></td>
                            <td>
                                <span ng-bind="version.Version"></span>
                                
                            </td>
                            <td ng-bind="version.FileName"></td>
                            
                            <td ng-bind="version.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td>
                                <a ng-click="$event.stopPropagation();" href="<?= Yii::$app->params['staticUrl']?>{{version.Path}}" download="{{version.Path}}">点击下载</pre>
                            </td>
                            <td style="text-align:center;">
                                 <button ng-if="version.status != 1" type="button" ng-click="setDefault(version.id,$event)" class="btn btn-success btn-xs">设为默认</button>
                                 <span ng-if="version.status == 1">
                                     <i class="glyphicon glyphicon-star-empty text-yellow"></i>
                                     <em>默认版本</em>
                                 </span>
                            </td>
                        </tr>

                      </table>
                    </div>
                    <div class="box-footer clearfix">
                        <em>共有<span ng-bind="pages.count"></span>个版本</em>
                        <!-- angularjs分页 -->
                        <ul class="pagination pagination-sm no-margin pull-right" ng-if="pages.count>0">
                            <li><a href="javascript:void(0);" ng-click="getPage(pages.pageNow-1)" ng-if="pages.pageNow>1">上一页</a></li>
                            <li><a href="javascript:void(0);" ng-click="getPage(1)" ng-if="pages.pageNow>1">1</a></li>
                            <li><a href="javascript:void(0);" ng-if="pages.pageNow>4">...</a></li>

                            <li><a href="javascript:void(0);" ng-click="getPage(pages.pageNow-2)" ng-bind="pages.pageNow-2" ng-if="pages.pageNow>3"></a></li>
                            <li><a href="javascript:void(0);" ng-click="getPage(pages.pageNow-1)" ng-bind="pages.pageNow-1" ng-if="pages.pageNow>2"></a></li>
                            
                            <li class="active"><a href="javascript:void(0);" ng-bind="pages.pageNow"></a></li>

                            <li><a href="javascript:void(0);" ng-click="getPage(pages.pageNow+1)" ng-bind="pages.pageNow+1" ng-if="pages.pageNow<pages.maxPage-1"></a></li>
                            <li><a href="javascript:void(0);" ng-click="getPage(pages.pageNow+2)" ng-bind="pages.pageNow+2" ng-if="pages.pageNow<pages.maxPage-2"></a></li>


                            <li><a href="javascript:void(0);" ng-if="pages.pageNow<pages.maxPage-3">...</a></li>

                            <li><a href="javascript:void(0);" ng-click="getPage(pages.maxPage)" ng-bind="pages.maxPage" ng-if="pages.pageNow<pages.maxPage"></a></li>
                            <li><a href="javascript:void(0);" ng-click="getPage(pages.pageNow+1)" ng-if="pages.pageNow<pages.maxPage">下一页</a></li>
                        </ul>
                        <!-- /.angularjs分页 -->
                    </div>
                    <!-- /.box-body -->
                  </div>
          <!-- /.box -->
                </div>
                
                

              </div>

                <div style="display: none;" id="hideenBox">
                    <div id="newVersion" >
                         <form id="myform">
                          <div class="box-body">
                            <div class="form-group {{sensorVersion.Version.length == 0 ? 'has-error':''}}">
                              <label for="InputVersion">Sensor版本号：</label>
                              <input class="form-control" id="InputVersion" name="Version" placeholder="1.5.1.0" ng-model="sensorVersion.Version">
                            </div>
                           
                            <div class="form-group {{sensorVersion.nofile ? 'has-error':''}}"">
                              <label for="InputFile">Sensor文件</label>
                              <input type="file" id="InputFile" style="display: none;" accept="application/x-msdownload">
                              <br/>
                              <label class="glyphicon glyphicon-plus-sign text-green" style="font-size: 48px" for="InputFile"></label>
                              <span ng-bind="sensorVersion.FileName" style="font-size: 14px;"></span>

                              <p class="help-block">请选择您要上传的exe执行程序</p>
                            </div>
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="status" ng-click="sensorVersion.status = !sensorVersion.status" ng-checked="sensorVersion.status"> 设为默认
                              </label>
                            </div>
                          </div>
                        </form>
                    </div>
                </div>
            </section>

    <!-- /.content -->

<script>
function getSearch (name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
var $scope = null;
var app = angular.module('myApp', []);
angular.module('myApp').config(function($locationProvider) {
    $locationProvider.html5Mode({
        enabled: true,
        requireBase: false
    });
});
app.controller('myCtrl', function($scope, $http,$filter) {
    $scope = $scope;
    $scope.pages = {
        data : [],
        count : 0,
        maxPage : "...",
        pageNow : 1,
    };

    $scope.getPage = function(pageNow)
    {
        pageNow = pageNow ? pageNow : getSearch("page");
        $http.post("versionpage",{page:pageNow}).then(function success(rsp){
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
                history.replaceState({page: $scope.pages.pageNow}, "versionPage", "?page="+$scope.pages.pageNow);
            }
        },function err(rsp){
        });
    }

    $scope.detail = function(index){
        var Version = $scope.pages.data[index];
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: "探针版本:"+Version.Version,
            content: "<pre>"+JSON.stringify(Version, null, 2)+"</pre>",
            width: W+"px",
            height: H+"px",
            cancel: true,
            overlayClose: true,
            onCleanup: function() {
            }
        });
    }

    $scope.setDefault = function(id,$event){
        $event.stopPropagation();
        pageNow = getSearch("page");
        rqs_data = {
            id : id,
            page:pageNow
        };
        var loading = zeroModal.loading(4);
        $http.post("setdefault",rqs_data).then(function success(rsp){
            zeroModal.close(loading);
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
                history.replaceState({page: $scope.pages.pageNow}, "versionPage", "?page="+$scope.pages.pageNow);
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    }
    $scope.createdVersion = function(){
        var W = $(".content").width()*0.5;
        var H = (W/4)*3;
        var send = null;
        var loading = null;
        var box = null;
        $scope.sensorVersion={
            Version:null,
            status:true
        }
        $('#InputFile').attr("data-url","addversion");
        $('#InputFile').attr("name","file");
        $('#InputFile').fileupload({
            dataType: 'json',
            autoUpload:false,
            progressall:function(e,data){
                // var progress=parseInt(data.loaded/data.total*100,10);
                // // $('#load').html(progress+'%');
                // $scope.sensorVersion.load = progress+'%';
                // $scope.$apply();
            },
            add: function (e, data) {

                console.log(data.files[0].type)
                $scope.sensorVersion.FileName = data.files[0].name;
                $scope.sensorVersion.nofile = false;
                $scope.$apply();
                send = data;
            },
            success:function(data)
            {
                if(data.status == 'success')
                {
                    $scope.pages = data;
                    $scope.$apply();
                    history.replaceState({page: $scope.pages.pageNow}, "versionPage", "?page="+$scope.pages.pageNow);
                }
            },complete:function(data)
            {
                hideenBox.appendChild(newVersion);
                zeroModal.close(loading);
                zeroModal.close(box);
            }
        });
        box = zeroModal.show({
            title: '添加新的探针版本',
            content: newVersion,
            width: W+"px",
            height: H+"px",
            ok: true,
            cancel: true,
            okFn: function() {
                console.log(123);
                var Version = $scope.sensorVersion.Version;
                var flag = true;
                if(send == null)
                {
                    $scope.sensorVersion.nofile = true;
                    flag = false;
                }
                if(Version == null || Version.length==0)
                {
                    $scope.sensorVersion.Version = "";
                    flag = false;
                }
                $scope.$apply();
                if(!flag)
                {
                    return false;
                }
                loading = zeroModal.loading(4);
                send.submit();
                return false;
            },
            onCleanup: function() {
                hideenBox.appendChild(newVersion);
            }
        });
    }
    

    $scope.getPage();

});


</script>
