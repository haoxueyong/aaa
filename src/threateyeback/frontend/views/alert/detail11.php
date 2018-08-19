<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '告警详情';
?>
    <style>
        .tab-title {
            display: inline-block;
            width: 100px;
            cursor: pointer;
            border-right: 1px solid #ddd;
            border-top: 2px solid #3c8dbc;
        }

        .box_centent {
            display: inline-block;
            float: left;
        }

        .box_centent_p {
            margin: 5px 0;
        }
    </style>
    <section class="content" ng-app="myApp" ng-controller="myCtrl" ng-cloak>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-bell-o"></i>
                            <span ng-bind="detail.indicator"></span>
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                        <!-- <div class="row" > -->
                        <!-- 下一个版本添加 -->
                        <div class="row" ng-if="!hoohoolabInfo">
                            <div class="col-md-6 border-right">
                                <ul class="nav nav-stacked sensor-detail">
                                    <li>
                                        <span class="sensor-detail-title">威胁指标</span>
                                        <span ng-bind="detail.indicator"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">风险资产</span>
                                        <span ng-bind="alert.client_ip"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">告警设备IP</span>
                                        <span ng-bind="detail.device_ip"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">信心指数</span>
                                        <span ng-bind="detail.attr.confidence"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">威胁程度</span>
                                        <span class="text-yellow">
                                            <i class="fa {{item}}" ng-repeat="item in detail.attr.threat_arr track by $index"></i>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 border-right">
                                <ul class="nav nav-stacked sensor-detail">
                                    <li>
                                        <span class="sensor-detail-title">告警类型</span>
                                        <span ng-bind="alert.category"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">指标类型</span>
                                        <span ng-bind="detail.type"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">告警时间</span>
                                        <span ng-bind="detail.time*1000 | date : 'yyyy-MM-dd HH:mm'"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">首次出现</span>
                                        <span ng-bind="detail.attr.first_seen | date : 'yyyy-MM-dd HH:mm'"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">最近出现</span>
                                        <span ng-bind="detail.attr.last_seen | date : 'yyyy-MM-dd HH:mm'"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- 新增/ -->
                        <div class="row" ng-if="hoohoolabInfo">

                            <div class="col-md-6 border-right">
                                <ul class="nav nav-stacked sensor-detail">
                                    <li>
                                        <span class="sensor-detail-title">威胁类型</span>
                                        <span ng-bind="hoohoolabType.threatType"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">告警设备</span>
                                        <span ng-bind="alert.device_ip"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">首次发现时间</span>
                                        <span ng-bind="hoohoolabType.first_seen | date : 'yyyy-MM-dd HH:mm'"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">主要受影响地区</span>
                                        <span ng-bind="hoohoolabType.geo"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 border-right">
                                <ul class="nav nav-stacked sensor-detail">
                                    <li>
                                        <span class="sensor-detail-title">风险资产</span>
                                        <span ng-bind="alert.client_ip"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">告警时间</span>
                                        <span ng-bind="detail.time*1000*1000 | date : 'yyyy-MM-dd HH:mm'"></span>
                                    </li>
                                    <li>
                                        <span class="sensor-detail-title">流行度</span>
                                        <span ng-bind="hoohoolabType.popularity"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- 新增/ -->




                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-12">
                                <ul class="nav nav-stacked sensor-detail" style="border-top: 1px solid #f4f4f4;">
                                    <div>
                                        <span class="sensor-detail-title">情报来源</span>

                                        <span>

                                            <div class="alert alert-info alert-dismissible group-lable ng-cloak" ng-repeat="item in detail.attr.sources">
                                                <span ng-bind="item"></span>
                                            </div>
                                            <!-- 下一个版本添加 -->
                                            <!-- <div ng-if="!hoohoolabInfo" class="alert alert-info alert-dismissible group-lable ng-cloak" ng-repeat="item in detail.attr.sources">
                                                <span ng-bind="item"></span>
                                            </div> -->
                                            <!-- <div ng-if="hoohoolabInfo" class="alert alert-info alert-dismissible group-lable ng-cloak" ng-repeat="item in hoohoolabSpan">
                                                <span ng-bind="item"></span>
                                            </div> -->
                                        </span>
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="row" style="display: none;">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-bell-o"></i>
                            <span>威胁情报详情</span>
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <pre class="code" ng-bind-html="json"></pre>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <ul id="myTab" class="nav nav-tabs">
                            <li class="active">
                                <a href="#home" data-toggle="tab">
                                    <i class="fa fa-bell-o "></i>
                                    <span>当前受威胁的资产</span>
                                </a>
                            </li>

                            <li>
                                <a href="#ios" data-toggle="tab">
                                    <i class="fa fa-history "></i>
                                    <span>历史受威胁的资产</span>
                                </a>
                            </li>
                            <li>
                                <a href="#detail" data-toggle="tab">
                                    <i class="fa fa-info-circle "></i>
                                    <span>告警日志信息</span>
                                </a>
                            </li>
                            <li ng-if="hoohoolabInfo" ng-repeat="item in hoohoolabTag">
                                <a href="{{item.href}}" data-toggle="tab">
                                    <i class="fa fa-cubes "></i>
                                    <span ng-bind="item.name"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div id="myTabContent" class="tab-content">
                        <!-- home -->
                        <div class="tab-pane fade in active" id="home">
                            <div class="box-body">
                                <table class="table table-hover ng-cloak">
                                    <tr>
                                        <th style="padding-left: 30px;border-top: 0px;">告警时间</th>
                                        <th style="border-top: 0px;">威胁类型</th>
                                        <th style="border-top: 0px;">威胁等级</th>
                                        <th style="border-top: 0px;">风险资产</th>
                                        <th style="border-top: 0px;">告警信息</th>
                                        <th style="border-top: 0px;">操作</th>
                                    </tr>

                                    <tr style="cursor: pointer;" ng-repeat="item in pages0.data">
                                        <td style="padding-left: 30px;" ng-bind="item.time"></td>
                                        <td ng-bind="item.category"></td>
                                        <td ng-bind="item.degree"></td>
                                        <td ng-bind="item.client_ip"></td>
                                        <td ng-bind="showLength(item.session)" title="{{item.session}}"></td>
                                        <td>
                                            <button class="btn btn-xs btn-default" ng-click="showDetail(item)">
                                                <i class="fa fa-eye"></i> 查看</button>
                                        </td>
                                    </tr>

                                </table>

                                <!-- angularjs分页 -->
                                <div style="border-top: 1px solid #f4f4f4;padding: 10px;">
                                    <em>共有
                                        <span ng-bind="pages0.count"></span>条告警</em>
                                    <!-- angularjs分页 -->
                                    <ul class="pagination pagination-sm no-margin pull-right ng-cloak">
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.pageNow-1)" ng-if="pages0.pageNow>1">上一页</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(1)" ng-if="pages0.pageNow>1">1</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-if="pages0.pageNow>4">...</a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.pageNow-2)" ng-bind="pages0.pageNow-2" ng-if="pages0.pageNow>3"></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.pageNow-1)" ng-bind="pages0.pageNow-1" ng-if="pages0.pageNow>2"></a>
                                        </li>

                                        <li class="active">
                                            <a href="javascript:void(0);" ng-bind="pages0.pageNow"></a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.pageNow+1)" ng-bind="pages0.pageNow+1" ng-if="pages0.pageNow<pages0.maxPage-1"></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.pageNow+2)" ng-bind="pages0.pageNow+2" ng-if="pages0.pageNow<pages0.maxPage-2"></a>
                                        </li>


                                        <li>
                                            <a href="javascript:void(0);" ng-if="pages0.pageNow<pages0.maxPage-3">...</a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.maxPage)" ng-bind="pages0.maxPage" ng-if="pages0.pageNow<pages0.maxPage"></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage0(pages0.pageNow+1)" ng-if="pages0.pageNow<pages0.maxPage">下一页</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.angularjs分页 -->
                            </div>
                        </div>
                        <!-- ios -->
                        <div class="tab-pane fade" id="ios">
                            <div class="box-body">
                                <table class="table table-hover ng-cloak">
                                    <tr>
                                        <th style="padding-left: 30px;border-top: 0px;">告警时间</th>
                                        <th style="border-top: 0px;">威胁类型</th>
                                        <th style="border-top: 0px;">威胁等级</th>
                                        <th style="border-top: 0px;">风险资产</th>
                                        <th style="border-top: 0px;">告警信息</th>
                                        <th style="border-top: 0px;">操作</th>
                                    </tr>

                                    <tr style="cursor: pointer;" ng-repeat="item in pages2.data">
                                        <td style="padding-left: 30px;" ng-bind="item.time"></td>
                                        <td ng-bind="item.category"></td>
                                        <td ng-bind="item.degree"></td>
                                        <td ng-bind="item.client_ip"></td>
                                        <td ng-bind="showLength(item.session)" title="{{item.session}}"></td>
                                        <td>
                                            <button class="btn btn-xs btn-default" ng-click="showDetail(item)">
                                                <i class="fa fa-eye"></i> 查看</button>
                                        </td>
                                    </tr>

                                </table>

                                <!-- angularjs分页 -->
                                <div style="border-top: 1px solid #f4f4f4;padding: 10px;">
                                    <em>共有
                                        <span ng-bind="pages2.count"></span>条告警</em>
                                    <!-- angularjs分页 -->
                                    <ul class="pagination pagination-sm no-margin pull-right ng-cloak">
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages2.pageNow-1)" ng-if="pages2.pageNow>1">上一页</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(1)" ng-if="pages2.pageNow>1">1</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-if="pages2.pageNow>4">...</a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages2.pageNow-2)" ng-bind="pages2.pageNow-2" ng-if="pages2.pageNow>3"></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages2.pageNow-1)" ng-bind="pages2.pageNow-1" ng-if="pages2.pageNow>2"></a>
                                        </li>

                                        <li class="active">
                                            <a href="javascript:void(0);" ng-bind="pages2.pageNow"></a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages.pageNow+1)" ng-bind="pages2.pageNow+1" ng-if="pages.pageNow<pages.maxPage-1"></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages2.pageNow+2)" ng-bind="pages2.pageNow+2" ng-if="pages2.pageNow<pages2.maxPage-2"></a>
                                        </li>


                                        <li>
                                            <a href="javascript:void(0);" ng-if="pages2.pageNow<pages2.maxPage-3">...</a>
                                        </li>

                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages2.maxPage)" ng-bind="pages2.maxPage" ng-if="pages2.pageNow<pages2.maxPage"></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" ng-click="getPage2(pages2.pageNow+1)" ng-if="pages2.pageNow<pages2.maxPage">下一页</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.angularjs分页 -->
                            </div>
                        </div>
                        <!-- detail -->
                        <div class="tab-pane fade" id="detail">
                            <div class="box-body">
                                <div class="box-body" ng-bind-html="logHtml">
                                </div>
                            </div>
                        </div>
                        <!-- hoohoolab 信息 -->
                        <div class="tab-pane fade" ng-if="hoohoolabInfo" ng-repeat="(index,item) in hoohoolabTag" id="{{item.id}}">

                            <div class="box-body" class="">
                                <div style="width:120px" class="box_centent">
                                    <p ng-repeat="key in item.tagName" class="box_centent_p" ng-bind="key">
                                </div>

                                <div style="width:40%" class="box_centent">
                                    <p ng-repeat="key in item.tagValue" class="box_centent_p" ng-bind="key"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </section>


    <script type="text/javascript" src="/plugins/angular/angular-sanitize.min.js"></script>
    <script>
        var alert = <?= json_encode($alert) ?>;
        if (typeof alert.data.geo == 'string') {
            try {
                alert.data.geo = JSON.parse(alert.data.geo);
            } catch (e) {}
        }
        var json = JSON.stringify(alert.data, 1, '\t');
        if (alert.data.attr && alert.data.attr.threat > -1) {
            alert.data.attr.threat_arr = [];
            for (var i = 0; i < 5; i++) {
                if (alert.data.attr.threat > i) {
                    if (alert.data.attr.threat < (i + 1)) {
                        alert.data.attr.threat_arr.push('fa-star-half-o');
                    } else {
                        alert.data.attr.threat_arr.push('fa-star');
                    }
                } else {
                    alert.data.attr.threat_arr.push('fa-star-o');
                }
            }
        }

        function json_highLight(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(
                /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                function (match) {
                    var cls = 'number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'key';
                        } else {
                            cls = 'string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'boolean';
                    } else if (/null/.test(match)) {
                        cls = 'null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                });
        }

        var app = angular.module('myApp', ['ngSanitize']);
        app.controller('myCtrl', function ($scope, $http, $filter) {

            $scope.data = {
                current: "1"
            };
            $scope.actions = {
                setCurrent: function (param) {
                    $scope.data.current = param;
                }
            };



            $scope.detail = alert.data;
            $scope.alert = alert;
            $scope.json = json_highLight(json);
            console.log($scope.alert);
            console.log($scope.detail);
            // 下一个版本增加
            // console.log($scope.detail);
            $scope.hoohoolabInfo = false;
            // console.log( $scope.detail.attr.sources);
            // $scope.hoohoolabObj = {};
            $scope.hoohoolabSpan = [];
            $scope.hoohoolabTag = [];
            $scope.hoohoolabType = {
                threatType: '', // 威胁类型
                popularity: '', // 流行度
                first_seen: '', // 首次发现时间
                geo: '', // 主要受影响地区
            };
            angular.forEach($scope.detail.attr.sources, function (key, value) {
                // key = 'hoohoolab.BotnetCAndCURL';
                if (key.split('.')[0] == 'hoohoolab') {
                    // console.log(key.split('.')[1]);
                    // console.log(2121);
                    // BotnetCAndCURL
                    $scope.BotnetCAndCURLfilesName = [];
                    $scope.BotnetCAndCURLfilesValue = [];
                    $scope.BotnetCAndCURLurlsName = [];
                    $scope.BotnetCAndCURLurlsValue = [];
                    $scope.BotnetCAndCURLinfoName = [];
                    $scope.BotnetCAndCURLinfoValue = [];
                    // IPReputation
                    $scope.IPReputationwhoisName = [];
                    $scope.IPReputationwhoisValue = [];
                    //MaliciousHash
                    $scope.MaliciousHash_MD5_name = [];
                    $scope.MaliciousHash_MD5_value = [];
                    // 文本信息
                    $scope.MaliciousHash_file__total_name = [];
                    $scope.MaliciousHash_file__total_value = [];

                    $scope.MaliciousHash_file_size_name = [];
                    $scope.MaliciousHash_file_size_value = [];
                    $scope.MaliciousHash_file_type_name = [];
                    $scope.MaliciousHash_file_type_value = [];
                    $scope.MaliciousHash_file_names_name = [];
                    $scope.MaliciousHash_file_names_value = [];
                    // 恶意文件下载ip
                    $scope.MaliciousHash_ip_name = [];
                    $scope.MaliciousHash_ip_value = [];
                    // 恶意文件下载URL
                    $scope.MaliciousHash_URLS_name = [];
                    $scope.MaliciousHash_URLS_value = [];
                    // MaliciousURL
                    $scope.MaliciousURL_Files_name = [];
                    $scope.MaliciousURL_Files_value = [];
                    $scope.MaliciousURL_whois_name = [];
                    $scope.MaliciousURL_whois_value = [];
                    // PhishingURL
                    $scope.PhishingURL_Ip_name = [];
                    $scope.PhishingURL_Ip_value = [];
                    $scope.PhishingURL_whois_name = [];
                    $scope.PhishingURL_whois_value = [];
                    // MobileMaliciousHash
                    $scope.MobileMaliciousHash_file_size_name = [];
                    $scope.MobileMaliciousHash_file_size_value = [];

                    switch (key.split('.')[1]) {
                        // 测试用
                        // case 'BotnetCAndCURL':
                        //     console.log(3333);
                        //     $scope.hoohoolabTag = [{
                        //         name: '服务器通信样本',
                        //         href: '#BotnetCAndCURL_files',
                        //         id: 'BotnetCAndCURL_files',
                        //         tagName: ['123', '2', '1213', '33'],
                        //         tagValue: ['sss', 'ds21dsaa', '123dd', '11d3d']
                        //     }, {
                        //         name: '样本下载URL',
                        //         href: '#BotnetCAndCURL_URL',
                        //         id: 'BotnetCAndCURL_URL',
                        //         tagName: ['144', '22222', '211222', '33222'],
                        //         tagValue: ['dsds', 'dsdsaa', '23dd', '1d3d']
                        //     }, {
                        //         name: 'whois信息',
                        //         href: '#BotnetCAndCURL_info',
                        //         id: 'BotnetCAndCURL_info',
                        //         tagName: ['1', '1123', '32222', '2133113'],
                        //         tagValue: ['dsds', 'dsdsaa', '23dd', '1d3d']
                        //     }];
                        //     $scope.hoohoolabType = {
                        //         threatType: '威胁类型', // 威胁类型
                        //         popularity: '流行度', // 流行度
                        //         first_seen: '首次发现时间', // 首次发现时间
                        //         geo: '主要受影响地区' // 主要受影响地区
                        //     };
                        //     break;
                         // 测试用
                            case 'BotnetCAndCURL':
                                for (var key in $scope.detail.attr.hoohoolab_files) {
                                    $scope.BotnetCAndCURLfilesName.push(key);
                                    $scope.BotnetCAndCURLfilesValue.push($scope.detail.attr.hoohoolab_files[key]);
                                };
                                for (var key in $scope.detail.attr.hoohoolab_Urls) {
                                    $scope.BotnetCAndCURLurlsName.push(key);
                                    $scope.BotnetCAndCURLurlsValue.push($scope.detail.attr.hoohoolab_Urls[key]);
                                };
                                for (var key in $scope.detail.attr.hoohoolab_files) {
                                    $scope.BotnetCAndCURLinfoName.push(key);
                                    $scope.BotnetCAndCURLinfoValue.push($scope.detail.attr.hoohoolab_files[key]);
                                };
                                $scope.hoohoolabTag = [{
                                    name: '服务器通信样本',
                                    id:'BotnetCAndCURL_files',
                                    tagName: $scope.BotnetCAndCURLfilesName,
                                    tagValue: $scope.BotnetCAndCURLfilesValue
                                    }, {
                                        name: '样本下载URL',
                                        id:'BotnetCAndCURL_URL',
                                        tagName: $scope.BotnetCAndCURLurlsName,
                                        tagValue: $scope.BotnetCAndCURLurlsValue
                                    }, {
                                        name: 'whois信息',
                                        id:'BotnetCAndCURL_info',
                                        tagName: $scope.BotnetCAndCURLinfoName,
                                        tagValue: $scope.BotnetCAndCURLinfoValue
                                    }];
                                // 获取 威胁等级 影响的国家 首次时间
                                $scope.hoohoolabType = {
                                    threatType:$scope.detail.attr.hoohoolab_threat, // 威胁类型
                                    popularity:$scope.detail.attr.hoohoolab_popularity, // 流行度
                                    first_seen:$scope.detail.attr.hoohoolab_First_seen, // 首次发现时间
                                    geo:$scope.detail.attr.hoohoolab_Geo, // 主要受影响地区
                                };
                                break;
                        case 'IPReputation':
                            for (var key in $scope.detail.attr.hoohoolab_ip_whois) {
                                $scope.IPReputationwhoisName.push(key);
                                $scope.IPReputationwhoisValue.push($scope.detail.attr.hoohoolab_ip_whois[
                                    key]);
                            };
                            $scope.hoohoolabTag = [{
                                name: 'IP_whois信息',
                                id: 'IPReputation_ip_whois',
                                tagName: $scope.IPReputationwhoisName,
                                tagValue: $scope.IPReputationwhoisValue
                            }];
                            $scope.hoohoolabType = {
                                threatType: $scope.detail.attr.hoohoolab_category, // 威胁类型
                                popularity: $scope.detail.attr.hoohoolab_popularity, // 流行度
                                first_seen: $scope.detail.attr.hoohoolab_First_seen, // 首次发现时间
                                geo: $scope.detail.attr.hoohoolab_ip_geo, // 主要受影响地区
                            };
                            break;
                        case 'MaliciousHash':
                            // 样本信息
                            for (var key in $scope.detail.attr.hoohoolab_file_size) {
                                $scope.MaliciousHash_file_size_name.push(key);
                                $scope.MaliciousHash_file_size_value.push($scope.detail.attr.hoohoolab_file_size[
                                    key]);
                            };
                            for (var key in $scope.detail.attr.hoohoolab_file_type) {
                                $scope.MaliciousHash_file_type_name.push(key);
                                $scope.MaliciousHash_file_type_value.push($scope.detail.attr.hoohoolab_file_type[
                                    key]);
                            };
                            for (var key in $scope.detail.attr.hoohoolab_file_names) {
                                $scope.MaliciousHash_file_names_name.push(key);
                                $scope.MaliciousHash_file_names_value.push($scope.detail.attr.hoohoolab_file_names[
                                    key]);
                            };
                            $scope.MaliciousHash_file__total_name.push($scope.MaliciousHash_file_size_name)
                                .push($scope.MaliciousHash_file_type_name).push($scope.MaliciousHash_file_names_name);
                            $scope.MaliciousHash_file__total_value.push($scope.MaliciousHash_file_size_value)
                                .push($scope.MaliciousHash_file_type_value).push($scope.MaliciousHash_file_names_value);
                            //恶意文件下载IP
                            for (var key in $scope.detail.attr.hoohoolab_IP) {
                                $scope.MaliciousHash_ip_name.push(key);
                                $scope.MaliciousHash_ip_value.push($scope.detail.attr.hoohoolab_IP[key]);
                            };
                            // 恶意文件下载URL
                            for (var key in $scope.detail.attr.hoohoolab_URLS) {
                                $scope.MaliciousHash_URLS_name.push(key);
                                $scope.MaliciousHash_URLS_value.push($scope.detail.attr.hoohoolab_URLS[
                                    key]);
                            };
                            $scope.hoohoolabTag = [{
                                name: '样本信息',
                                id: 'MaliciousHash_file',
                                tagName: $scope.MaliciousHash_file__total_name,
                                tagValue: $scope.MaliciousHash_file__total_value
                            }, {
                                name: '恶意文件下载IP',
                                id: 'MaliciousHash_ip',
                                tagName: $scope.MaliciousHash_ip_name,
                                tagValue: $scope.MaliciousHash_ip_value
                            }, {
                                name: '恶意文件下载URL',
                                id: 'MaliciousHash_URLS',
                                tagName: $scope.MaliciousHash_URLS_name,
                                tagValue: $scope.MaliciousHash_URLS_value
                            }];
                            $scope.hoohoolabType = {
                                threatType: $scope.detail.attr.hoohoolab_threat, // 威胁类型
                                popularity: $scope.detail.attr.hoohoolab_popularity, // 流行度
                                first_seen: $scope.detail.attr.hoohoolab_First_seen, // 首次发现时间
                                geo: $scope.detail.attr.hoohoolab_Geo, // 主要受影响地区
                            };
                            break;
                        case 'MaliciousURL':
                            // 该站点存放的恶意文件
                            for (var key in $scope.detail.attr.hoohoolab_Files) {
                                $scope.MaliciousURL_Files_name.push(key);
                                $scope.MaliciousURL_Files_value.push($scope.detail.attr.hoohoolab_Files[
                                    key]);
                            };
                            //whios 信息
                            for (var key in $scope.detail.attr.hoohoolab_whois) {
                                $scope.MaliciousURL_whois_name.push(key);
                                $scope.MaliciousURL_whois_value.push($scope.detail.attr.hoohoolab_whois[
                                    key]);
                            };
                            $scope.hoohoolabTag = [{
                                name: '该站点存放的恶意文件',
                                id: 'MaliciousURL_Files',
                                tagName: $scope.MaliciousURL_Files_name,
                                tagValue: $scope.MaliciousURL_Files_value
                            }, {
                                name: 'whios信息',
                                id: 'MaliciousURL_whois',
                                tagName: $scope.MaliciousURL_whois_name,
                                tagValue: $scope.MaliciousURL_whois_value
                            }];
                            if ($scope.detail.attr.hoohoolab_category == 'Malware') {
                                $scope.hoohoolab_category_cn = '恶意软件';
                            } else if ($scope.detail.attr.hoohoolab_category == 'BotC&C') {
                                $scope.hoohoolab_category_cn = '僵尸网络';
                            } else if ($scope.detail.attr.hoohoolab_category == 'Malware') {
                                $scope.hoohoolab_category_cn = '网络诈骗';
                            } else if ($scope.detail.attr.hoohoolab_category == 'MobileMalware') {
                                $scope.hoohoolab_category_cn = '移动恶意软件';
                            }else if ($scope.detail.attr.hoohoolab_category == 'Maliciousredirect') {
                                $scope.hoohoolab_category_cn = '恶意重定向';
                            };
                            $scope.hoohoolabType = {
                                threatType: $scope.hoohoolab_category_cn, // 威胁类型
                                popularity: $scope.detail.attr.hoohoolab_popularity, // 流行度
                                first_seen: $scope.detail.attr.hoohoolab_First_seen, // 首次发现时间
                                geo: $scope.detail.attr.hoohoolab_Geo, // 主要受影响地区
                            };
                            break;
                        case 'PhishingURL':
                            // 被钓鱼IP
                            for (var key in $scope.detail.attr.hoohoolab_Ip) {
                                $scope.PhishingURL_Ip_name.push(key);
                                $scope.PhishingURL_Ip_value.push($scope.detail.attr.hoohoolab_Ip[key]);
                            };
                            //whios信息
                            for (var key in $scope.detail.attr.hoohoolab_whois) {
                                $scope.PhishingURL_whois_name.push(key);
                                $scope.PhishingURL_whois_value.push($scope.detail.attr.hoohoolab_whois[
                                    key]);
                            };
                            $scope.hoohoolabTag = [{
                                name: '被钓鱼IP',
                                id: 'PhishingURL_Ip',
                                tagName: $scope.PhishingURL_Ip_name,
                                tagValue: $scope.PhishingURL_Ip_value
                            }, {
                                name: 'whios信息',
                                id: 'MaliciousHash_file',
                                tagName: $scope.PhishingURL_whois_name,
                                tagValue: $scope.PhishingURL_whois_value
                            }];

                            $scope.hoohoolabType = {
                                threatType: '钓鱼网站', // 威胁类型
                                popularity: $scope.detail.attr.hoohoolab_popularity, // 流行度
                                first_seen: $scope.detail.attr.hoohoolab_First_seen, // 首次发现时间
                                geo: $scope.detail.attr.hoohoolab_Geo, // 主要受影响地区
                            };
                            break;
                        case 'MobileMaliciousHash':
                            // 样本信息
                            for (var key in $scope.detail.attr.hoohoolab_file_size) {
                                $scope.MobileMaliciousHash_file_size_name.push(key);
                                $scope.MobileMaliciousHash_file_size_value.push($scope.detail.attr.hoohoolab_file_size[
                                    key]);
                            };
                            $scope.hoohoolabTag = [{
                                name: '样本信息',
                                id: 'MobileMaliciousHash_file_size',
                                tagName: $scope.MobileMaliciousHash_file_size_name,
                                tagValue: $scope.MobileMaliciousHash_file_size_value
                            }];
                            $scope.hoohoolabType = {
                                threatType: $scope.detail.attr.hoohoolab_threat, // 威胁类型
                                popularity: $scope.detail.attr.hoohoolab_popularity, // 流行度
                                first_seen: $scope.detail.attr.hoohoolab_First_seen, // 首次发现时间
                                geo: $scope.detail.attr.hoohoolab_Geo, // 主要受影响地区
                            };
                            break;
                        default:
                            break;
                    }
                    $scope.hoohoolabInfo = true;
                    //   console.log($scope.hoohoolabSpan);

                } else {
                    $scope.hoohoolabInfo = false;
                }
            });



            if (alert.data.matched) {
                var re = new RegExp(alert.data.matched, 'g');
                // console.log(re);

                var span = '<span class="highlight">' + alert.data.matched + '</span>';
            }

            console.log(alert.data.session);


            // $scope.logHtml = alert.data.session.raw.replace(re, span);
            console.log('4444');
            $scope.showLength = function (str, length) {
                if (!length) {
                    length = 60;
                }
                return str.substr(0, length) + '...';
            }
            $scope.showDetail = function (item) {
                window.location.href = '/alert/' + item.id;
            }
            $scope.getPage0 = function (pageNow) {
                pageNow = pageNow ? pageNow : 1;
                $scope.pageGeting = true;
                //    当前受到威胁的资产 未处理的  0
                var postData0 = {
                    indicator: $scope.detail.indicator,
                    is_deal: 0,
                    rows: '10'
                };
                postData0['page'] = pageNow;
                $http({
                    method: 'GET',
                    url: '/alert/get-same-indicator-alert',
                    params: postData0
                }).then(function (rsp, status, headers, config) {
                    $scope.setPage0(rsp.data);
                    // console.log(rsp.data);
                    // 当相应准备就绪时调用
                }, function (error, status, headers, config) {
                    console.log(error);
                })
            }
            $scope.getPage2 = function (pageNow) {
                pageNow = pageNow ? pageNow : 1;
                $scope.pageGeting = true;
                //    历史受到威胁的资产 已处理的  2
                var postData2 = {
                    indicator: $scope.detail.indicator,
                    is_deal: 2,
                    rows: 10
                };
                postData2['page'] = pageNow;
                $http({
                    method: 'GET',
                    url: '/alert/get-same-indicator-alert',
                    params: postData2
                }).then(function (rsp, status, headers, config) {

                    // console.log(rsp.data);

                    $scope.setPage2(rsp.data);
                    // 当相应准备就绪时调用
                }, function (error, status, headers, config) {
                    console.log(error);
                })
            }
            $scope.setPage0 = function (data) {
                $scope.pages0 = data;
                sessionStorage.setItem('alertPage', $scope.pages0.pageNow);
            }
            $scope.setPage2 = function (data) {
                $scope.pages2 = data;
                sessionStorage.setItem('alertPage', $scope.pages2.pageNow);
            }
            $scope.getPage0();
            $scope.getPage2();


        });
    </script>