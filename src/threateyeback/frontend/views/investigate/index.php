<?php
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */

$this->title = '安全调查';
?>
<!-- Main content -->
<section class="content" ng-app="myApp" ng-controller="myCtrl">
<div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active" ng-click="showTable = 'Computer'"><a href="#computer" data-toggle="tab" aria-expanded="true"><i class="fa fa-laptop"></i> 计算机</a></li>
            <li ng-click="showTable = 'File'"><a href="#file" data-toggle="tab" aria-expanded="false"><i class="fa fa-file-o"></i> 文件</a></li>
            <li ng-click="showTable = 'FileTransfer'"><a href="#fileTransfer" data-toggle="tab" aria-expanded="false"><i class="fa fa-share-alt"></i> 文件传输</a></li>
            <li ng-click="showTable = 'Signer'"><a href="#signer" data-toggle="tab" aria-expanded="false"><i class="fa fa-pencil-square-o"></i> 签名</a></li>
            <li ng-click="showTable = 'Domain'"><a href="#domain" data-toggle="tab" aria-expanded="false"><i class="fa fa-internet-explorer"></i> 域名IP</a></li>
            <li ng-click="showTable = 'User'"><a href="#user" data-toggle="tab" aria-expanded="false"><i class="fa fa-user-o"></i> 用户</a></li>
            <li ng-click="showTable = 'IOC'"><a href="#IOC" data-toggle="tab" aria-expanded="false"><i class="fa fa-search"></i> IOC扫描</a></li>
        </ul>
        <div class="tab-content">

            <?php include 'computer.php';?>
			
            <?php include 'file.php';?>

            <?php include 'fileTransfer.php';?>

            <?php include 'signer.php';?>
			
            <?php include 'domain.php';?>

            <?php include 'user.php';?>

            <?php include 'ioc.php';?>

        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
<?php include 'data.php';?>
</section>
<!-- /.content -->
<script src="/js/controllers/updateTable.js"></script>
<script src="/js/controllers/investigate.js"></script>