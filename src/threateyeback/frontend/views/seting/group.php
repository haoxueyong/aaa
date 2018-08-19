<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '计算机组';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="group">


  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <?php include 'nav.php';?>
        
        <div class="tab-content">

          <!-- group-->
          <div class="tab-pane active" id="group">

            <div class="row">
              <div class="col-xs-3">
                <div class="box-header with-border">
                  <h3 class="box-title">计算机组</h3>
                  <div class="box-tools pull-right">
                    <div class="btn-group">
                      <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-navicon"></i>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="javascript:void(0);" ng-click="add()">新建组</a></li>
                        <li ng-show="nowGroup"><a href="javascript:void(0);" ng-click="add('children')">新建下级组</a></li>
                        <li class="divider" ng-show="nowGroup"></li>
                        <li ng-show="nowGroup"><a href="javascript:void(0);" ng-click="del()">删除组</a></li>
                      </ul>
                    </div>
                  </div>

                </div>
                <div class="box-body" id="groupTree"></div>
              </div>
              <div class="col-xs-9 ng-cloak" ng-show="nowGroup">
                <div class="box-header with-border">
                  <h3 class="box-title">编辑计算机组</h3>
                </div>
                <div class="box-body">
                  <div class="row margin">
                    <div class="form-group col-md-8">
                        <label>分组名称</label>
                        <input type="text" class="form-control" ng-model="nowGroup.text" />
                    </div>
                    <div class="form-group col-md-4">
                        <label>分组类型</label>
                        <select class="form-control" ng-model="nowGroup.type">
                          <option value="0" selected="selected">手动分组</option>
                          <option value="1">自动分组</option>
                        </select>
                    </div>
                  </div>
                  <div class="row margin"  ng-show="nowGroup.type == '1'">
                    <div class="col-md-12">

                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span>添加规则</span>
                        <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="javascript:addFilter('ComputerName');">计算机名称</a></li>
                          <li><a href="javascript:addFilter('OSType');">操作系统</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="row margin" ng-show="nowGroup.type == '1'">
                    <div class="col-md-10">
                      <div class="alert alert-success group-lable" ng-repeat="item in nowGroup.FilterList">
                        <span ng-bind="titles[item.key]+'='+item.value"></span>
                        <span class="close" ng-click="nowGroup.FilterList.splice($index,1);">×</span>
                      </div>
                    </div>
                  </div>

                  <div class="row margin">
                    <div class="form-group col-md-12">
                      <button style="float: right;" type="button" class="btn btn-sm btn-primary" ng-click="save()" ng-disabled="!nowGroup.text">保&nbsp;&nbsp;存</button>
                    </div>
                  </div>

                  <div id="hideenBox" style="display: none;">
                    <div id="newFilter" class="form-group">
                      <label id="newFilterTitle"></label>
                      <input type="text" class="form-control" ng-model="newFilter.value">
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <!-- ./group -->

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
var GroupList = <?= json_encode($GroupList) ?>;
</script>
<script type="text/javascript" src="/js/controllers/group.js"></script>

