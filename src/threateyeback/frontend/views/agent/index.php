<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '聚合';
// $this->params['chartVersion'] = '1.1.1';
?>

<style>
  #chart {
    width: 100%;
    height: 100%;
    overflow:hidden;
  }

  /*.node circle{
    fill: #F1C40F;
  }*/
  #chartAll{
    min-height: 600px;
  }
  .node rect {
    cursor: move;
    fill-opacity: .9;
    shape-rendering: crispEdges;
  }

  .node text {
    pointer-events: none;
    text-shadow: 0 1px 0 #fff;
    font-size: 12px; 
  }

  .link {
    fill: none;
    stroke: #000;
    stroke-opacity: .2;
  }

  .link:hover {
    stroke-opacity: .5;
  }

  .radio-group{
    white-space: nowrap;
    display: inline-block;
    margin-bottom: 5px;
    text-overflow: ellipsis;
    overflow-x: hidden;
    cursor: pointer;
  }
  .zeromodal-body{
    overflow-x: hidden;
  }
  .box-row{
    clear: both;
  }

</style>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="myCtrl">
  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">

        <ul class="nav nav-tabs">
          <li ng-class="tab=='default'?'active':''" style="cursor: pointer;">
            <a ng-click="tab = 'default'">默认聚合</a>
          </li>
          <?php if (Yii::$app->user->identity->role == 'admin') { ?>
          <li ng-class="tab=='user-defined'?'active':''" style="cursor: pointer;">
            <a ng-click="tab = 'user-defined'">自定义聚合</a>
          </li>
          <?php } ?>
        </ul>
        
        <div class="tab-content">

          <div class="tab-pane" ng-class="tab=='default'?'active':''">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>节点名</th>
                  <th>指标</th>
                  <th>URL</th>
                  <th>操作</th>
                </tr>
                <tr ng-repeat="item in default_nodes">
                  <td ng-bind="item.name">节点名</td>
                  <td ng-bind="item.length"></td>
                  <td>
                    <a href="{{'/feeds/'+ item.name }}" target="_blank" ng-bind="rootUrl+'/feeds/'+item.name"></a>
                  </td>
                  <td>
                    <button class="btn btn-success btn-xs" ng-click="showSankey(item)">查看详细</button>
                  </td>
                </tr>
              </thead>
            </table>
          </div>

          <?php if (Yii::$app->user->identity->role == 'admin') { ?>
          <div class="tab-pane" ng-class="tab=='user-defined'?'active':''">
            <div class="row">
              <div class="col-lg-4 col-sm-6 col-xs-8">
                <div class="input-group">
                  <input type="text" ng-model="newGroup.name" class="form-control" placeholder="请输入分组名称...">
                    <span class="input-group-btn">
                      <button type="button" ng-click="addGroup()" class="btn btn-info">添加分组</button>
                    </span>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>

        </div>

      </div>
    </div>
  </div>


  <div class="row" ng-show="tab=='default'">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><i class="fa fa-dot-circle-o"></i> 聚合总览</h3>
          <div class="box-tools">

          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <div id="chartAll"></div>  
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>

  </div>

  <?php if (Yii::$app->user->identity->role == 'admin') { ?>
  <div class="row ng-cloak" ng-show="tab=='user-defined'">

    <div class="col-md-12">
      <div class="box collapsed-box">
        <div class="box-header">
          <h3 class="box-title">
            <i class="fa fa-object-group"></i>
            <span>全部节点</span>
          </h3>
          <div class="box-tools">
            <button type="button" class="btn btn-box-tool" ng-click="showAddNodeBox()" data-toggle="tooltip" title="新增">
              <i class="fa fa-plus-circle"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-plus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>节点名</th>
                <th>指标</th>
                <th>URL</th>
                <th>操作</th>
              </tr>
              <tr ng-repeat="node in output_nodes">
                <td ng-bind="node.name">节点名</td>
                <td ng-bind="node.length"></td>
                <td>
                  <a href="{{'/feeds/'+ node.name }}" target="_blank" ng-bind="rootUrl+'/feeds/'+node.name"></a>
                </td>
                <td>
                  <button class="btn btn-success btn-xs" ng-click="showSankey(node)" data-toggle="tooltip" title="查看详情">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-warning btn-xs" ng-click="showUpdateNodeBox(node)" ng-if="!node.isDefault" data-toggle="tooltip" title="自定义输入节点">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-xs" ng-click="delNode(node)" ng-if="!node.isDefault" data-toggle="tooltip" title="删除节点">
                    <i class="fa fa-remove"></i>
                  </button>
                </td>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
    
    <div class="col-md-12" ng-repeat="group in groups">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">
            <i class="fa fa-object-group"></i>
            <span ng-bind="group.name"></span>
          </h3>
          <div class="box-tools">
            <button type="button" class="btn btn-box-tool" ng-if="group.id == selectGroupID && !equals(group.nodes,selectNodeList)" ng-click="updateGroup($index);" data-toggle="tooltip" title="保存">
              <i class="fa fa-save"></i>
            </button>
            <button type="button" class="btn btn-box-tool" ng-click="showSelectTable(group);" data-toggle="tooltip" title="选择节点">
              <i class="fa fa-check-square-o"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" ng-click="delGroup($index);" data-toggle="tooltip" title="删除分组">
              <i class="fa fa-remove"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <table class="table table-hover" ng-if="group.id != selectGroupID">
            <thead>
              <tr>
                <th>节点名</th>
                <th>指标</th>
                <th>URL</th>
                <th>操作</th>
              </tr>
              <tr ng-repeat="nodeName in group.nodes" ng-if="output_nodes[nodeName]">
                <td ng-bind="output_nodes[nodeName].name">节点名</td>
                <td ng-bind="output_nodes[nodeName].length"></td>
                <td>
                  <a href="{{'/feeds/'+ output_nodes[nodeName].name }}" target="_blank" ng-bind="rootUrl+'/feeds/'+output_nodes[nodeName].name"></a>
                </td>
                <td>
                  <button class="btn btn-success btn-xs" ng-click="showSankey(output_nodes[nodeName]) ">查看详细</button>
                </td>
              </tr>
            </thead>
          </table>
          <table class="table table-hover" ng-if="group.id == selectGroupID">
            <thead>
              <tr>
                <th>节点名</th>
                <th>指标</th>
                <th>URL</th>
                <th>操作</th>
              </tr>
              <tr ng-repeat="node in output_nodes">
                <td ng-bind="node.name">节点名</td>
                <td ng-bind="node.length"></td>
                <td>
                  <a href="{{'/feeds/'+ node.name }}" target="_blank" ng-bind="rootUrl+'/feeds/'+node.name"></a>
                </td>
                <td>
                  <span class="icheckbox_minimal-blue" ng-click="selectNode(node.name)" ng-class="selectNodeList.indexOf(node.name) != -1 ? 'checked' : ''"></span>
                </td>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <?php } ?>

  <div id="hide_box" style="display: none;">
    <div id="chart"></div>
    <div id="addNodeBox">
      <div class="box-row">
        <div class="form-group col-md-12">
          <label>节点名称</label>
          <input class="form-control" style="width: 30%;min-width: 30em" ng-model="newNode.name" placeholder="请输入节点名称...">
        </div>
      </div>

      <div class="box-row">
        <div class="form-group col-md-12">
          <label>节点类型</label>
          <div class="radio">
            <span class="radio-group col-lg-3 col-md-4 col-sm-6 col-xs-12" ng-click="newNode.setType('md5')">
              <span class="iradio_minimal-blue" ng-class="newNode.type == 'md5' ? 'checked' : ''"></span>
              <span>MD5</span>
            </span>
            <span class="radio-group col-lg-3 col-md-4 col-sm-6 col-xs-12" ng-click="newNode.setType('IPv4')">
              <span class="iradio_minimal-blue" ng-class="newNode.type == 'IPv4' ? 'checked' : ''"></span>
              <span>IPv4</span>
            </span>
            <span class="radio-group col-lg-3 col-md-4 col-sm-6 col-xs-12" ng-click="newNode.setType('domain')">
              <span class="iradio_minimal-blue" ng-class="newNode.type == 'domain' ? 'checked' : ''"></span>
              <span>Domain</span>
            </span>
            <span class="radio-group col-lg-3 col-md-4 col-sm-6 col-xs-12" ng-click="newNode.setType('URL')">
              <span class="iradio_minimal-blue" ng-class="newNode.type == 'URL' ? 'checked' : ''"></span>
              <span>URL</span>
            </span>
          </div>
        </div>
      </div>
      
      <div class="box-row">
        <div class="form-group col-md-12">
          <label>请选择输入节点</label>
          <div class="radio">
            <div class="radio-group col-lg-3 col-md-4 col-sm-6 col-xs-12" ng-click="newNode.push2inputs(item)" ng-repeat="item in input_nodes" ng-if="item.indicator_types.indexOf(newNode.type) > -1">
              <span class="icheckbox_minimal-blue" ng-class="newNode.inputs.indexOf(item.name) > -1 ? 'checked' : ''"></span>
              <span ng-bind="item.name"></span>
            </div>
          </div>
        </div>
      </div>

    </div>


    <div id="updateNodeBox">

      <div class="box-row">
        <div class="form-group col-md-12">
          <label>请选择输入节点</label>
          <div class="radio">
            <div class="radio-group col-lg-3 col-md-4 col-sm-6 col-xs-12" ng-click="nowNode.push2inputs(item)" ng-repeat="item in input_nodes" ng-if="item.indicator_types.indexOf(nowNode.type) > -1">
              <span class="icheckbox_minimal-blue" ng-class="nowNode.inputs.indexOf(item.name) > -1 ? 'checked' : ''"></span>
              <span ng-bind="item.name"></span>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>


</section>
<!-- /.content -->

<script src="/plugins/dndTree/d3.v3.min.js"></script>
<script src="/js/agent/sankey.js"></script>
<script src="/js/controllers/agent.js"></script>

