<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '告警详情';
?>
<style type="text/css">
  
  .node {
    cursor: pointer;
  }

  .overlay{
      background-color:#FFF;
  }
   
  .node circle {
    fill: #fff;
    stroke: steelblue;
    stroke-width: 1.5px;
    
  }
   
  .node text {
    font-size:10px; 
    font-family:sans-serif;
  }
   
  .link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
  }

  .templink {
    fill: none;
    stroke: red;
    stroke-width: 3px;
  }

  .ghostCircle.show{
      display:block;
  }

  .ghostCircle, .activeDrag .ghostCircle{
       display: none;
  }

</style>
<section class="content" ng-app="myApp" >

  <div class="row" ng-controller="fileCtrl">
    <div class="col-md-8">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#proTree" data-toggle="tab" aria-expanded="true">进程树</a></li>
          <li><a href="#fileTree" data-toggle="tab" aria-expanded="false">文件树</a></li>
        </ul>
        <div class="tab-content">


          <!-- proTree -->
          <div class="tab-pane active" id="proTree">
            <div id="tree-container"></div>
          </div>
          <!-- /proTree -->

          <!-- fileTree -->
          <div class="tab-pane" id="fileTree">
            <div></div>
          </div>
          <!-- /fileTree -->
      
        </div>
      </div>
    </div>



    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">详情</h3>
          <div class="box-tools">

          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            
          <div class="table-responsive">
            <table class="table" id="tar_obj">
              <tbody>
                <tr>
                  <th>进程ID：</th>
                  <td id="tar_PID"></td>
                </tr>
                <tr>
                  <th>进程类型：</th>
                  <td id="tar_ObjType"></td>
                </tr>
                <tr>
                  <th>文件名：</th>
                  <td id="tar_ProcessName"></td>
                </tr>
                <tr>
                  <th>文件路径：</th>
                  <td id="tar_ImagePath"></td>
                </tr>
                <tr>
                  <th>文件大小：</th>
                  <td id="tar_FileSize"></td>
                </tr>
                <tr>
                  <th>文件MD5：</th>
                  <td id="tar_MD5"></td>
                </tr>
                <tr>
                  <th>用户名：</th>
                  <td id="tar_UserName"></td>
                </tr>
     
        
                
              </tbody>
            </table>
          </div>


        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">来源进程</h3>
          <div class="box-tools">

          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <!-- <pre id="src_obj"></pre> -->
          <div class="table-responsive">
            <table class="table" id="src_obj">
              <tbody>
                <tr>
                  <th>进程ID：</th>
                  <td id="src_PID"></td>
                </tr>
                <tr>
                  <th>进程类型：</th>
                  <td id="src_ObjType"></td>
                </tr>
                <tr>
                  <th>文件名：</th>
                  <td id="src_ProcessName"></td>
                </tr>
                <tr>
                  <th>文件路径：</th>
                  <td id="src_ImagePath"></td>
                </tr>
                <tr>
                  <th>文件大小：</th>
                  <td id="src_FileSize"></td>
                </tr>
                <tr>
                  <th>文件MD5：</th>
                  <td id="src_MD5"></td>
                </tr>
                <tr>
                  <th>用户名：</th>
                  <td id="src_UserName"></td>
                </tr>
                
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
    

  </div>
</section>

<script src="/plugins/dndTree/d3.v3.min.js"></script>
<script src="/js/tree/eventTree.js"></script>

 












































