<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '计算机';
?>

                <!-- Main content -->
<section class="content" ng-app="myApp">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom" style="margin-bottom: 0px">
                <ul class="nav nav-tabs" style="margin-bottom:-1px;">
                    <li class="active"><a href="#protect" data-toggle="tab" aria-expanded="true">受保护的计算机</a></li>
                    <li><a href="#quarantine" data-toggle="tab" aria-expanded="false">被隔离的计算机</a></li>
                </ul>
            
                <div class="tab-content" style="padding-top:0px;border-bottom:0px; ">
                    <!-- protect -->
                    <div class="tab-pane active" id="protect" ng-controller="protectCtrl">
                        <div class="row margin">
                            <div class="form-group col-md-2">
                                <label>计算机</label>
                                <input type="text" class="form-control" ng-model="searchData.ComputerName" ng-change="searchDataChange('ComputerName')" ng-keyup="myKeyup($event)">
                            </div>
                            <div class="form-group col-md-2">
                                <label>配置文件</label>
                                <select class="form-control" ng-model="searchData.ProfileVersion" ng-change="searchDataChange('ProfileVersion')">
                                    <option value="*">所有</option>
                                    <?php foreach ($ProfileVersionList as $key => $value): ?>
                                        <option value="<?= $value->ProfileVersion ?>"><?= $value->ProfileVersion ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>状态</label>
                                <select class="form-control" ng-model="searchData.status" ng-change="searchDataChange('status')">
                                    <option value="*">所有</option>
                                    <option value="1">在线</option>
                                    <option value="2">断线</option>
                                    <option value="0">卸载</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>组</label>
                                <input ng-model="Groups[searchData.group].text" name="" class="form-control" ng-click="searchGroups()" style="cursor: default;" />
                            </div> 
                            <div class="form-group col-md-2">
                                <label>Sensor版本</label>
                                <select class="form-control" ng-model="searchData.SensorVersion" ng-change="searchDataChange('SensorVersion')">
                                    <option value="*">所有</option>
                                    <?php foreach ($SensorVersionList as $key => $value): ?>
                                        <option value="<?= $value->SensorVersion ?>"><?= $value->SensorVersion ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label style="width: 100%;">&nbsp;</label>
                                <button class="form-control btn btn-success" style="max-width: 80px;" ng-click="search()">搜&nbsp;&nbsp;索</button>
                            </div>
                            
                        </div>

                        
                        <div class="row margin">
                            <?php if(Yii::$app->user->identity->role == 'admin'){?>
                            <div class="btn-group">
                                <button type="button" ng-click="sendUpdateProfile()" class="btn btn-default" ng-class="SensorIDList.length ? '' : 'disabled'">赋予计算机配置文件</button>
                                <button type="button" ng-click="addGroups()" class="btn btn-default" ng-class="SensorIDList.length ? '' : 'disabled'">加入计算机组</button>
                                <button type="button" ng-click="sendUpdate()" class="btn btn-default" ng-class="SensorIDList.length ? '' : 'disabled'">升级Sensor</button>
                                <button type="button" ng-click="sendBase('UNINIT')" class="btn btn-default" ng-class="SensorIDList.length ? '' : 'disabled'">卸载Sensor</button>
                            </div>
                            <?php }?>
                            <style type="text/css">
                                .list-group-item:first-child,.list-group-item:last-child{
                                    border-radius:0px;
                                }
                            </style>
                            <div id="hide_box" style="display: none;">
                                <div id="update_list"></div>
                                <div id="profile_list"></div>
                                <div id="groupTree"></div>
                            </div>
                        </div>
                        

                        <div class="row margin">

                            <table class="table table-hover ng-cloak">
                                <tr>
                                    <th><input type="checkbox" ng-checked="SensorIDList.length == pages.data.length" ng-click="selectAll()"></th>
                                    <th>计算机名</th>
                                    <th>状态</th>
                                    <th>IP</th>
                                    <th>操作系统</th>
                                    <th>最近一次通讯时间</th>
                                    <th>受保护起始时间</th>
                                    <th ng-if="rsqType=='ProfileVersion'">配置文件</th>
                                    <th ng-if="rsqType=='group'">组</th>
                                    <th ng-if="rsqType=='SensorVersion'">Sensor版本</th>
                                </tr>

                                <tr style="cursor: pointer;" ng-repeat="sensor in pages.data" ng-click="detail(sensor.SensorID)">
                                    <td><input type="checkbox" ng-checked="SensorIDList.indexOf(sensor.SensorID) != -1" ng-click="selectOne(sensor.SensorID,$event)" ng-disabled="(sensor.status != 1)" ></td>
                                    <td ng-class="sensor.isolate == 1 ? 'text-red' : ''" title="{{sensor.SensorID}}">
                                        <i class="ico fa fa-laptop"></i>
                                        <span ng-bind="sensor.ComputerName"></span>
                                    </td>
                                    <td>
                                        <span class="label label-{{status_str[sensor.status].css}}" ng-bind="status_str[sensor.status].label" ng-if="sensor.work != 1"></span>
                                        <span class="label label-warning" ng-if="sensor.work == 1">请等待...</span>
                                    </td>
                                    <td ng-bind="sensor.IP"></td>
                                    <td ng-bind="sensor.OSTypeShort"></td>
                                    <td ng-bind="sensor.updated_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                                    <td ng-bind="sensor.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                                    <td ng-if="rsqType=='ProfileVersion'" ng-bind="sensor.ProfileVersion"></td>
                                    <td ng-if="rsqType=='group'" ng-bind="groupText"></td>
                                    <td ng-if="rsqType=='SensorVersion'" ng-bind="sensor.SensorVersion"></td>

                                </tr>

                            </table>
                    
                            <!-- angularjs分页 -->
                            <div style="border-top: 1px solid #f4f4f4;padding: 10px;">
                                <em>共有<span ng-bind="pages.count"></span>台计算机</em>
                                <!-- angularjs分页 -->
                                <ul class="pagination pagination-sm no-margin pull-right ng-cloak">
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
                            </div>
                            <!-- /.angularjs分页 -->
                        </div>
                    </div>
                    <!-- /.protect -->

                    <!-- quarantine -->
                    <div class="tab-pane" id="quarantine" ng-controller="quarantineCtrl">
                        <table class="table table-hover">
                            <tr>
                                <th><input type="checkbox" ng-checked="SensorIDList.length == pages.data.length" ng-click="selectAll()"></th>
                                <th>计算机名</th>
                                <th>状态</th>
                                <th>IP</th>
                                <th>操作系统</th>
                                <th>最近一次通讯时间</th>
                                <th>受保护起始时间</th>
                            </tr>

                            <tr style="cursor: pointer;" ng-repeat="sensor in pages.data" ng-click="detail(sensor.SensorID)">
                                <td><input type="checkbox" ng-checked="SensorIDList.indexOf(sensor.SensorID) != -1" ng-click="selectOne(sensor.SensorID,$event)"></td>
                                <td ng-class="sensor.isolate == 1 ? 'text-red' : ''">
                                    <i class="ico fa fa-laptop"></i>
                                    <span ng-bind="sensor.ComputerName"></span>
                                </td>
                                <td>
                                    <span class="label label-{{status_str[sensor.status].css}}" ng-bind="status_str[sensor.status].label"></span>
                                </td>
                                <td ng-bind="sensor.IP"></td>
                                <td ng-bind="sensor.OSTypeShort"></td>
                                <td ng-bind="sensor.updated_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                                <td ng-bind="sensor.created_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>

                                
                            </tr>

                        </table>

                    <!-- /.angularjs分页 -->
                        <div style="border-top: 1px solid #f4f4f4;padding: 10px;">
                            <em>共有<span ng-bind="pages.count"></span>台计算机</em>
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
                        </div>              
                    </div>
                    <!-- /.quarantine -->

                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col-md-12 -->
    </div>
    <!-- /.row -->
</section>

<div style="display: none;" id="whiteBox">
    <div id="white" >
        <span></span>
        <table class="table table-hover">
            <tr>
              <th>计算机名</th>
              <th>操作系统</th>
              <th>IP</th>
              <th>白名单</th>
            </tr>

            <tr ng-repeat="SensorID in SensorIDList">
                <td ng-bind="SensorList[SensorID].ComputerName"></td>
                <td ng-bind="SensorList[SensorID].OSTypeShort"></td>
                <td ng-bind="SensorList[SensorID].IP"></td>
                <td><input ng-model="WhiteList[SensorID]" size="90"></td>
            </tr>
        </table>
    </div>
</div>
    <!-- /.content -->
<script type="text/javascript">
    var SensorFileList = <?= json_encode($SensorFileList) ?>;
    var ProFileList = <?= json_encode($ProFileList) ?>;
    var GroupList = <?= json_encode($GroupList) ?>;
</script>
<script src="/js/controllers/sensor.js"></script>
