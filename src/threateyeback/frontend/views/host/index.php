<?php

use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */

$this->title = '安全设备';
// $this->params['chartVersion'] = '1.1.1';
?>

    <style type="text/css">
        .radio-group {
            white-space: nowrap;
            display: inline-block;
            margin-bottom: 5px;
            text-overflow: ellipsis;
            overflow-x: hidden;
            cursor: pointer;
        }

        .zeromodal-body {
            overflow-x: hidden;
        }

        .box {
            margin-bottom: 0;
        }

        .table {
            margin-bottom: 0;
        }

        #cpuEchart,#memEchart,#diskEchart {
            height: 200px;
            width: 300px;
        }

        .box-row {
            clear: both;
        }

        .form-control {
            border-radius: 4px;
        }

        @charset "UTF-8";
        [ng\:cloak],
        [ng-cloak],
        [data-ng-cloak],
        [x-ng-cloak],
        .ng-cloak,
        .x-ng-cloak,
        .ng-hide {
            display: none !important;
        }

        ng\:form {
            display: block;
        }

        .ng-animate-start {
            clip: rect(0, auto, auto, 0);
            -ms-zoom: 1.0001;
        }

        .ng-animate-active {
            clip: rect(-1px, auto, auto, 0);
            -ms-zoom: 1;
        }
        .bordercolor{
            border:1px solid red;
        }
    </style>

    <!-- Main content -->
    <section class="content" ng-app="myApp" ng-controller="myCtrl" ng-cloak>
        <!--proxyNodeList -->
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">
                            <i class="fa fa-server"></i>
                            <span ng-bind="hostName"></span>
                        </h3>
                        <div class="box-tools">
                            <button class="btn btn-sm btn-success" ng-show="info" ng-click="add()">
                                <i class="fa fa-plus"></i>添加设备</button>
                            <button class="btn btn-sm btn-success" ng-show="!info" ng-click="back()">返回</button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding" ng-show="info">
                        <div class="nav-tabs-custom" style="margin-bottom: 0px">
                            <div class="tab-content" style="padding-top:0px;border-bottom:0px; ">
                                <table class="table table-hover" id="hostTable">
                                    <thead>
                                        <tr>
                                            <th>设备名称</th>
                                            <th>IP</th>
                                            <th>主机类别</th>
                                            <th>创建日期</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div id="hide_box" style="display: none;">
                            <div id="hostBox">
                                <div class="box-row">
                                    <div class="form-group col-md-4">
                                        <label>设备名称</label>
                                        <input class="form-control" ng-model="nowHost.name" placeholder="请输入资产名称"
                                        ng-focus="inputfocus()" ng-class="nameUndefined ? '': 'bordercolor'">
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.name ? '　' : '资产名称不为空'"></div> -->
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>主机类别</label>
                                        <input class="form-control" ng-model="nowHost.family" placeholder="请输入主机类别" ng-focus="inputfocus()" ng-class="familyUndefined ? '': 'bordercolor'">
                                        <!-- <select ng-model="nowHost.family" class="form-control">
                                            <option value="huawei firewall usg6300">huawei firewall usg6300</option>
                                            <option value="topsec firewall 4000">topsec firewall 4000</option>
                                        </select> -->
                                        <!-- <div>{{familyUndefined}}</div> -->
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.family ? '　' : '主机类别不为空'"></div> -->
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>IP地址</label>
                                        <input class="form-control" ng-model="nowHost.protocol.ipv4" placeholder="请输入IP地址"
                                        ng-focus="inputfocus()" ng-class="ipUndefined ? '': 'bordercolor'">
                                        <!-- <div class="help-block text-red" ng-bind="IPv4Error(nowHost.protocol.ipv4)"></div> -->
                                    </div>
                                </div>

                                <div class="box-row">
                                    <div class="zeromodal-line"></div>
                                    <div class="form-group col-md-4">
                                        <label>snmp服务版本</label>
                                        <select ng-model="nowHost.protocol.snmp.version" class="form-control" ng-options="unit.id as unit.text for unit in [{id: 1, text: 'v1'}, {id: 2, text: 'v2'}, {id: 3, text: 'v3'}]">
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>端口</label>
                                        <input class="form-control" type="number" ng-model="nowHost.protocol.snmp.port" max="65535" min="0">
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.port >= 0 && nowHost.protocol.snmp.port <= 65535 ? '　' : '请输入有效端口'"></div> -->
                                    </div>
                                    <div class="form-group col-md-4" ng-if="nowHost.protocol.snmp.version != 3">
                                        <label>字符串</label>
                                        <input class="form-control" type="password" ng-model="nowHost.protocol.snmp.community"
                                        ng-focus="inputfocus()" ng-class="snmpUndefined ? '': 'bordercolor'">
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.community ? '　' : '社区串不为空'"></div> -->
                                    </div>
                                </div>


                                <div class="box-row" ng-if="nowHost.protocol.snmp.version == 3">
                                    <div class="form-group col-md-4">
                                        <label>认证方式</label>
                                        <select ng-model="nowHost.protocol.snmp.auth" class="form-control" 
                                        ng-focus="inputfocus()" ng-class="authUndefined ? '': 'bordercolor'">
                                            <option value="SHA">SHA</option>
                                            <option value="MD5">MD5</option>
                                        </select>
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.auth ? '　' : '认证方式不为空'"></div> -->
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>安全用户名</label>
                                        <input class="form-control" ng-model="nowHost.protocol.snmp.security_user"
                                        ng-focus="inputfocus()" ng-class="userUndefined ? '': 'bordercolor'">
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.security_user ? '　' : '安全用户名不为空'"></div> -->
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>认证密码</label>
                                        <input class="form-control" type="password" ng-model="nowHost.protocol.snmp.auth_pass"
                                        ng-focus="inputfocus()" ng-class="authPassUndefined ? '': 'bordercolor'">
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.auth_pass ? '　' : '认证密码不为空'"></div> -->
                                    </div>
                                </div>

                                <div class="box-row" ng-if="nowHost.protocol.snmp.version == 3">
                                    <div class="form-group col-md-4">
                                        <label>加密方式</label>
                                        <select ng-model="nowHost.protocol.snmp.privacy" class="form-control"
                                        ng-focus="inputfocus()" ng-class="privacyUndefined ? '': 'bordercolor'">
                                            <option value="AES">AES</option>
                                            <option value="DES">DES</option>
                                        </select>
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.privacy ? '　' : '加密方式不为空'"></div> -->
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>加密密码</label>
                                        <input class="form-control" type="password" ng-model="nowHost.protocol.snmp.privacy_pass"
                                        ng-focus="inputfocus()" ng-class="privacyPassUndefined ? '': 'bordercolor'">
                                        <!-- <div class="help-block text-red" ng-bind="nowHost.protocol.snmp.privacy_pass ? '　' : '加密密码不为空'"></div> -->
                                    </div>
                                </div>

                                  <!-- mibs信息添加 -->

                                <div class="box-row">
                                    <div class="zeromodal-line"></div>
                                    <div class="form-group col-md-6">
                                        <label>CPU</label>
                                        <input class="form-control" placeholder="1.3.6.1.4.1.2011.5.23.1.1.1.10.11111" ng-model="mibsinfo.cpu">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Memory</label>
                                        <input class="form-control" placeholder="1.3.6.1.4.1.2011.5.23.1.1.1.10.11111" ng-model="mibsinfo.memory">

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Disk</label>
                                        <input class="form-control" placeholder="1.3.6.1.4.1.2011.5.23.1.1.1.10.11111" ng-model="mibsinfo.disk">
                                    </div>
                                    <!-- <div class="form-group col-md-6">
                                        <label>Interface</label>
                                        <input class="form-control" placeholder="1.3.6.1.4.1.2011.5.23.1.1.1.10.11111" ng-model="mibsinfo.interface">
                                    </div> -->
                                </div>


                            </div>
                        </div>

                    </div>



                </div>
            </div>

        </div>

        <!-- 磁盘占用率的弹框 1-->
        <div ng-show="!info" ng-cloak class="ng-cloak">
            <div class="box-row">
                <div class="form-group col-md-4">
                    <label>CPU使用率</label>
                    <div id="cpuEchart"></div>
                </div>
                <div class="form-group col-md-4">
                    <label>内存占用率</label>
                    <div id="memEchart"></div>
                </div>
                <div class="form-group col-md-4">

                    <label>磁盘占用率</label>
                    <div id="diskEchart"></div>
                </div>
            </div>
            <div class="box-row" ng-if="logPage">
                <div class="zeromodal-line"></div>
                <div class="form-group">
                    <label> &nbsp;&nbsp;&nbsp;&nbsp;设备日志</label>

                    <!--<section class="content" ng-app="myApp" ng-controller="myCtrl">-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body table-responsive no-padding">
                                    <div class="nav-tabs-custom" style="margin-bottom: 0px">
                                        <div class="tab-content" style="padding-top:0px;border-bottom:0px; ">
                                            <table class="table table-hover" id="hostTable">
                                                <thead>
                                                    <tr>
                                                        <th style="width:'10%''">序号</th>
                                                        <th style="width:'20%'">时间</th>
                                                        <th style="width:'70%'">描述</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="(index,item) in devivelogData">
                                                        <td ng-bind="index+1"></td>
                                                        <td ng-bind="item.created_at"></td>
                                                        <td ng-bind="item.info"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        <!-- /.col-md-9 left -->
                    </div>
                    <!--</section>-->
                </div>
            </div>
        </div>
       

    </section>
    <!-- /.content -->

    <script type="text/javascript" src="/js/controllers/host.js"></script>