<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */

$this->title = '报表';
// $this->params['chartVersion'] = '1.1.1';
?>

    <link rel="stylesheet" href="/css/report.css">
    <!-- Main content -->
    <section class="content" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <div class="head">
            <div class="head-top" ng-if="datafalse">
                <label class="i-checks m-b-none">
                    <input type="checkbox" ng-model="riskAssets">
                    <i></i>
                    <span class="item-span-content">风险资产</span>
                </label>
                <label class="i-checks m-b-none">
                    <input type="checkbox" ng-model="safetyEquipment">
                    <i></i>
                    <span class="item-span-content">安全设备</span>
                </label>
            </div>
            <div class="head-mid">
                <span class="head-mid-title">请选择时间范围:</span>
                <label class="i-checks m-b-none">
                    <input type="radio" ng-model="choosetime" ng-change="changeTime(value)" value="day">
                    <i></i>
                    <span class="item-span-content">日报</span>
                </label>
                <label class="i-checks m-b-none">
                    <input type="radio" ng-model="choosetime" ng-change="changeTime(value)" value="week">
                    <i></i>
                    <span class="item-span-content">周报</span>
                </label>
                <label class="i-checks m-b-none">
                    <input type="radio" ng-model="choosetime" ng-change="changeTime(value)" value="month">
                    <i></i>
                    <span class="item-span-content">月报</span>
                </label>
                &nbsp;

                <span class="item-span-content">自定义时间:</span>
                <label for="reservationtime">
                    <input type="text" ng-click="custom()" class="form-control" id="reservationtime" readonly style="background-color: #fff;">
                </label>


            </div>
            <div class="head-name">

                <span class="head-mid-title">报表名称:</span>
                <label>
                    <input type="text" placeholder="请输入报表名称(必填)" ng-model="reportName" class="form-control" style="background-color: #fff;border-radius: 5px;"
                    ng-focus="inputfocus()" ng-class="reportNameInput ? '': 'bordercolor'">
                </label>
                <!-- <span class="alertInfo" ng-if="alertMsg">报表名称不能为空。</span> -->
            </div>
            <div class="head-name">

                <span class="head-mid-title">报表格式:</span>
                <label>
                    <select class="form-control" style="background-color: #fff;" ng-model="selectedName" ng-options="x.value as x.name for x in datatype"></select>
                </label>
            </div>

            <div class="head-bom">
                <button class="form-control btn btn-success " style="max-width: 80px;" ng-click="generate()">生成报表</button>
            </div>

        </div>
        <div class="main">
            <table class="table table-striped ng-cloak">
                <thead>
                    <tr>
                        <!-- <th>序号</th> -->
                        <th style="width:20%">日期</th>
                        <th style="width:25%">名称</th>
                        <th style="width:25%">时间范围</th>
                        <th style="width:10%">格式</th>
                        <th style="width:10%">下载</th>
                        <th style="width:10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="item in dataInfo">
                        <td ng-bind="item.create_time">2018-05-09</td>
                        <td ng-bind="item.report_name">月报</td>
                        <td>
                            <span ng-bind="item.stime"></span> -
                            <span ng-bind="item.etime"></span>
                        </td>
                        <td ng-bind="item.report_type"></td>

                        <td class="cursor">&nbsp;&nbsp;
                            <img src="../../images/icos/download.png" ng-click="download(item.id)" width="16" height="16" alt="">
                        </td>
                        <td class="cursor">&nbsp;&nbsp;
                            <img src="../../images/icos/delate.png" ng-click="del(item.id)" width="16" height="16" alt="">
                        </td>
                    </tr>

                </tbody>
            </table>
            <!-- angularjs分页 -->
            <div style="border-top: 1px solid #f4f4f4;padding: 10px;">
                <em>共有
                    <span ng-bind="pages.count"></span>条报表</em>
                <!-- angularjs分页 -->
                <ul class="pagination pagination-sm no-margin pull-right ng-cloak" >
                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.pageNow-1)" ng-if="pages.pageNow>1">上一页</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(1)" ng-if="pages.pageNow>1">1</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" ng-if="pages.pageNow>4">...</a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.pageNow-2)" ng-bind="pages.pageNow-2" ng-if="pages.pageNow>3"></a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.pageNow-1)" ng-bind="pages.pageNow-1" ng-if="pages.pageNow>2"></a>
                    </li>

                    <li class="active">
                        <a href="javascript:void(0);" ng-bind="pages.pageNow"></a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.pageNow+1)" ng-bind="pages.pageNow+1" ng-if="pages.pageNow<pages.maxPage-1"></a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.pageNow+2)" ng-bind="pages.pageNow+2" ng-if="pages.pageNow<pages.maxPage-2"></a>
                    </li>


                    <li>
                        <a href="javascript:void(0);" ng-if="pages.pageNow<pages.maxPage-3">...</a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.maxPage)" ng-bind="pages.maxPage" ng-if="pages.pageNow<pages.maxPage"></a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" ng-click="getPage(pages.pageNow+1)" ng-if="pages.pageNow<pages.maxPage">下一页</a>
                    </li>
                </ul>
            </div>
            <!-- /.angularjs分页 -->
        </div>



    </section>
    <!-- /.content -->

    <script type="text/javascript" src="/js/controllers/report.js"></script>