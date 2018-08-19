<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '系统日志';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="UserLogCtrl">


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">

          <!-- userlog-->
          <div class="tab-pane active" id="userlog">
            <div class="row margin">
                <div class="form-group col-md-2">
                    <label>用户标识</label>
                    <input type="text" class="form-control" ng-model="search_data.username">
                </div>
                <div class="form-group col-md-3">
                    <label for="reservationtime">请选择时间范围</label>
                    <input type="text" class="form-control" id="reservationtime" readonly style="background-color: #fff;">
                </div>
                <div class="form-group col-md-2 pull-right">
                    <label style="width: 100%;">&nbsp;</label>
                    <button class="form-control btn btn-success pull-right" style="max-width: 80px;" ng-click="search()">搜&nbsp;&nbsp;索</button>
                </div>
            </div>


            <section>
              <div class="row">
                <div class="col-sm-12">
                    <table class="table table-hover" ng-show="pages.data.length>0" style="border-bottom: 1px solid #f4f4f4;">
                        <tr>
                          <th>时间</th>
                          <th>用户标识</th>
                          <th>描述</th>
                        </tr>

                        <tr style="cursor: pointer;" ng-repeat="item in pages.data">
                            <td ng-bind="item.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                            <td ng-bind="item.username"></td>
                            <td ng-bind="item.info"></td>
                        </tr>
                      </table>
                </div>
              </div>


              <div class="row" >

                <div class="col-sm-12" style="min-height: 20px;">
                    <em>共有<span ng-bind="pages.count"></span>条记录</em>
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
          </div>
          <!-- ./user -->

        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>

</section>

           
<!-- /.content -->


<script type="text/javascript" src="/js/controllers/LogList.js"></script>













































