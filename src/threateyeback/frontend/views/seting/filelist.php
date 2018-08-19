<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '黑白名单';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" >


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">

          <!-- filelist-->
          <div class="tab-pane active" id="filelist">


            <!-- white -->
            <section ng-controller="WhiteCtrl">
              <h4 class="seting-header" style="margin-bottom: -1px;">
                白名单
                <button type="button" ng-click="add('3')" class="btn btn-primary btn-sm pull-right" style="margin: -5px 5px">添加白名单</button>
              </h4>

              <div class="row">

                <div class="col-sm-12">
                    <table class="table table-hover" ng-show="pages.data.length>0" style="border-bottom: 1px solid #f4f4f4;">
                        <tr>
                          <th style="text-align:center;">序号</th>
                          <th>MD5</th>
                          <th style="display: none;">SHA256</th>
                          <th>创建时间</th>
                          <th>更新时间</th>
                          <th style="text-align:center;">操作</th>
                        </tr>

                        <tr style="cursor: pointer;" ng-repeat="item in pages.data">
                            <td style="text-align: center;" ng-bind="$index+1"></td>
                            <td ng-bind="item.MD5"></td>
                            <td ng-bind="item.SHA256" style="display: none;"></td>
                            <td ng-bind="item.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td ng-bind="item.updated_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td style="text-align:center;">
                                 <button ng-if="item.status==3" type="button" ng-click="del([2,3],item.id,$event)" class="btn btn-danger btn-xs">删除</button>
                            </td>
                        </tr>
                      </table>
                </div>
              </div>


              <div class="row" >

                <div class="col-sm-12" style="min-height: 20px;">
                    <em>共有<span ng-bind="pages.count"></span>个白名单文件</em>
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


              <div style="display: none;" id="hideenBox_white">
                <div id="newFile_white" >
                  <form>
                    <div class="box-body">
                      <div class="form-group {{MD5error ? 'has-error':''}}">
                        <label for="InputVersion">MD5：</label>
                        <input class="form-control" ng-model="File.MD5">
                        <p class="help-block">请选择填写符合格式的文件MD5</p>
                      </div>

                      <div class="form-group" style="display: none;">
                        <label for="InputVersion">SHA256</label>
                        <input class="form-control" ng-model="File.SHA256">
                        <p class="help-block">请选择填写符合格式的文件SHA265</p>
                      </div>

                    </div>
                  </form>
                </div>
              </div>
            </section>
            <!-- ./white -->

            <section style="border-bottom: 1px solid #f4f4f4; margin:50px 0px"></section>

            <!-- black -->
            <section ng-controller="BlackCtrl">
              <h4 class="seting-header" style="margin-bottom: -1px;">
                黑名单
                <button type="button" ng-click="add('4')" class="btn btn-primary btn-sm pull-right" style="margin: -5px 5px">添加黑名单</button>
              </h4>

              <div class="row">

                <div class="col-sm-12">
                    <table class="table table-hover" ng-show="pages.data.length>0" style="border-bottom: 1px solid #f4f4f4;">
                        <tr>
                          <th style="text-align:center;">序号</th>
                          <th>MD5</th>
                          <th style="display: none;">SHA256</th>
                          <th>创建时间</th>
                          <th>更新时间</th>
                          <th style="text-align:center;">操作</th>
                        </tr>

                        <tr style="cursor: pointer;" ng-repeat="item in pages.data">
                            <td style="text-align: center;" ng-bind="$index+1"></td>
                            <td ng-bind="item.MD5"></td>
                            <td ng-bind="item.SHA256" style="display: none;"></td>
                            <td ng-bind="item.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td ng-bind="item.updated_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td style="text-align:center;">
                                 <button type="button" ng-click="del('4',item.id,$event)" class="btn btn-danger btn-xs">删除</button>
                            </td>
                        </tr>
                      </table>
                </div>
              </div>


              <div class="row" >

                <div class="col-sm-12" style="min-height: 20px;">
                    <em>共有<span ng-bind="pages.count"></span>个黑名单文件</em>
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


              <div style="display: none;" id="hideenBox_black">
                <div id="newFile_black" >
                  <form>
                    <div class="box-body">
                      <div class="form-group {{MD5error ? 'has-error':''}}">
                        <label for="InputVersion">MD5：</label>
                        <input class="form-control" ng-model="File.MD5">
                        <p class="help-block">请选择填写符合格式的文件MD5</p>
                      </div>

                      <div class="form-group" style="display: none;">
                        <label for="InputVersion">SHA256</label>
                        <input class="form-control" ng-model="File.SHA256">
                        <p class="help-block">请选择填写符合格式的文件SHA265</p>
                      </div>

                    </div>
                  </form>
                </div>
              </div>
            </section>
            <!-- ./black -->
            
          </div>
          <!-- ./filelist -->

        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>

</section>

           
<!-- /.content -->


<script type="text/javascript" src="/js/controllers/FileList.js"></script>













































