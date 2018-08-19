<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '配置文件';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" >


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">
          <!-- ProFile-->

          <div class="tab-pane active" id="ProFile" ng-controller="ProFileCtrl">

            
            <section>
              <h4 class="seting-header" style="margin-bottom: -1px;">
                配置文件
                <button type="button" ng-click="createdVersion()" class="btn btn-primary btn-sm pull-right" style="margin: -5px 5px">上传配置文件</button>
              </h4>

              <div class="row">

                <div class="col-sm-12">
                    <table class="table table-hover" ng-show="pages.data.length>0" style="border-bottom: 1px solid #f4f4f4;">
                        <tr>
                          <th style="text-align:center;">序号</th>
                          <th>版本</th>
                          <th>文件名</th>
                          
                          <th>上传时间</th>
                          <th>下载</th>
                          <th>默认版本</th>
                          <th>操作</th>
                        </tr>

                        <tr style="cursor: pointer;" ng-repeat="item in pages.data" ng-click="detail($index)">
                            <td style="text-align: center;" ng-bind="$index+1+((pages.pageNow-1)*pages.rows)"></td>
                            <td>
                                <span ng-bind="item.Version"></span>
                                
                            </td>
                            <td ng-bind="item.FileName"></td>
                            
                            <td ng-bind="item.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td>
                                <a ng-click="$event.stopPropagation();" href="<?= Yii::$app->params['staticUrl']?>{{item.Path}}" download="{{item.Path}}">点击下载</a>
                            </td>
                            <td>
                                 <button ng-if="item.status != 1" type="button" ng-click="setDefault(item.id,$event)" class="btn btn-success btn-xs">设为默认</button>
                                 <span ng-if="item.status == 1">
                                     <i class="glyphicon glyphicon-star-empty text-yellow"></i>
                                     <em>默认版本</em>
                                 </span>
                            </td>
                            <td>
                                <button type="button" ng-click="del(item.id,$event)" class="btn btn-danger btn-xs">删除</button>
                            </td>
                        </tr>

                      </table>
                </div>

              </div>

              <div class="row" >

                <div class="col-sm-12" style="min-height: 20px;">
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

              </div>
            </section>
            <div style="display: none;" id="hideenBox">
              <div id="newVersion" >
                <form id="myform">
                  <div class="box-body">
                    <div class="form-group {{ProFile.Version.length == 0 ? 'has-error':''}}">
                      <label for="InputVersion">配置文件版本号：</label>
                      <input class="form-control" id="InputVersion" name="Version" placeholder="1.5.1.0" ng-model="ProFile.Version">
                    </div>
                   
                    <div class="form-group {{ProFile.nofile ? 'has-error':''}}"">
                      <label for="InputFile">配置文件</label>
                      <input type="file" id="InputFile" style="display: none;">
                      <br/>
                      <label class="glyphicon glyphicon-plus-sign text-green" style="font-size: 48px" for="InputFile"></label>
                      <span ng-bind="ProFile.FileName" style="font-size: 14px;"></span>

                      <p class="help-block">请选择您要上传的json配置文件</p>
                    </div>
                    <div class="checkbox" style="margin-bottom: 0px;">
                      <label>
                        <input type="checkbox" name="status" ng-click="ProFile.status = !ProFile.status" ng-checked="ProFile.status"> 设为默认
                      </label>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- ./ProFile -->

        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>

</section>

           
<!-- /.content -->


<script type="text/javascript" src="/js/controllers/SetingBase.js"></script>
<script type="text/javascript" src="/js/controllers/SetingProFile.js"></script>













































