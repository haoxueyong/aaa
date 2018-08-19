<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\News;

AppAsset::register($this);


function isActive($urls)
{
    $url = explode('/',Yii::$app->request->getUrl())[1]; 
    if(in_array($url, $urls))
    {
        return 'active';
    }else
    {
        return '';
    }
}
function getPath($path)
{
    $url = explode('?',Yii::$app->request->getUrl())[0];
    $url = rtrim($url, '/'); 
    if($url == $path)
    {
        return 'javascript:void(0);';
    }else
    {
        return $path;
    }

}


// $bodyClassName = empty($_COOKIE['bodyClassName']) ? 'hold-transition skin-blue sidebar-mini' : $_COOKIE['bodyClassName'];

?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>iCatch <?= $this->title ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/plugins/font-awesome-4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/plugins/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/dist/css/skins/_all-skins.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/plugins/iCheck/flat/blue.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="/plugins/morris/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="/plugins/datepicker/datepicker3.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <link rel="stylesheet" href="/css/zeroModal.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="stylesheet" href="/plugins/iCheck/minimal/_all.css">
  <link rel="stylesheet" href="/css/style.css">
  <script type="text/javascript" src="/js/angular.min.js"></script>
  <script src="/js/controllers/News.js"></script>
  <script src="/js/controllers/self.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="/images/hoohoolab-logo-black.png" style="height: 36px"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="/images/hoohoolab-logo-black.png" style="height: 36px"><b>HooHooLab</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->


     
      <div class="navbar-custom-menu" style="float: left;">
        <ul class="nav navbar-nav">

          <!-- Tasks: style can be found in dropdown.less -->
          <li class="treeview <?= isActive(['site',''])?>">
            <a href="<?= getPath('/site/index') ?>">
              <i class="fa fa-dashboard"></i> 
              <span>总览</span>
            </a>
          </li>
          <li class="treeview <?= isActive(['alert'])?>">
            <a href="<?= getPath('/alert/index') ?>">
              <i class="fa fa-heartbeat"></i>
              <span>威胁</span>
            </a>
          </li>
          
          <li class="treeview <?= isActive(['investigate'])?>">
            <a href="<?= getPath('/investigate/index') ?>">
              <i class="fa fa-search"></i>
              <span>安全调查</span>
            </a>
          </li>
          
          <li class="treeview <?= isActive(['sensor'])?>">
            <a href="<?= getPath('/sensor/index') ?>">
              <i class="fa fa-laptop"></i>
              <span>计算机</span>
            </a>
          </li>

          <?php if(Yii::$app->user->identity->role == 'admin'){?>
          <li class="treeview <?= isActive(['seting'])?>" >
            <a href="<?= getPath('/seting/index') ?>">
              <i class="fa fa-gears"></i>
              <span>设置</span>
              </span>
            </a>
          </li>
          <?php }?>

          
        </ul>
      </div>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <?php include 'self.php';?>
          <?php include 'news.php';?>

          <li class="dropdown user user-menu">
            <a href="/site/logout">
              <i class="fa fa-sign-out"></i>
              <span>退出</span>
            </a>
          </li>
        </ul>
        
      </div>
    </nav>
  </header>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= $this->title ?>
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <?php if($this->title != '总览'){ ?> 
        <li><a href="/site/index"><i class="fa fa-dashboard"></i> 总览</a></li>
        <?php }?>

        <?php if($this->title != '威胁' && isActive(['alert']) == 'active'){ ?> 
        <li><a href="/alert/index"><i class="fa fa-heartbeat"></i> 威胁</a></li>
        <?php }?>

        <?php if($this->title != '计算机' && isActive(['sensor']) == 'active'){ ?> 
        <li><a href="/sensor/index"><i class="fa fa-laptop"></i> 计算机</a></li>
        <?php }?>

        <?php if($this->title != '安全调查' && isActive(['investigate']) == 'active'){ ?> 
        <li><a href="/investigate/index"><i class="fa fa-search"></i> 安全调查</a></li>

        <?php }?>
        <?php if($this->title != '设置' && isActive(['seting']) == 'active'){ ?> 
        <li><a href="/seting/index"><i class="fa fa-gears"></i> 设置</a></li>
        <?php }?>
        
        
        <li class="active"><?= $this->title ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <?= $content ?>
    <!-- /.content -->
    <div class="hoohoolab-footer">
      <span>&copy; 2017 虎特信息科技(上海)有限公司 版权所有</span>
    </div>
  <!-- /.content-wrapper -->
  <!--
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>版本</b> 2.1.1
    </div>
    <strong>HooHooLab &copy; 2017 <a href="http://www.hoohoolab.com/" target="_blank">虎特信息科技(上海)有限公司</a>.</strong> All rights
    reserved.
  </footer>
  -->
 

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <!-- <div class="control-sidebar-bg"></div> -->
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<!-- <script src="/js/jquery-ui.min.js"></script> -->
<script src="/plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>

<!-- Sparkline -->
<script src="/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="/plugins/knob/jquery.knob.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/plugins/fastclick/fastclick.js"></script>
<!-- date-range-picker -->
<script src="/plugins/daterangepicker/moment.min.js"></script>
<script src="/plugins/daterangepicker/moment-zh.js"></script>
<script src="/plugins/daterangepicker/daterangepicker.js"></script>
<!-- zeroModal -->
<script src="/js/zeroModal.min.js"></script>
<!-- Fileupload -->
<!-- <script src="/js/jquery-ui.min.js"></script> -->
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<!-- Chart.js -->
<script src="/plugins/chartjs/Chart.min.2.5.js"></script>
<!-- canvasjs.js -->
<script src="/plugins/canvasjs-1.9.8/canvasjs.min.js"></script>
<!-- bootstrap-treeview -->
<script src="/js/bootstrap-treeview.js"></script>
<!-- Highcharts -->
<script src="https://cdn.hcharts.cn/highcharts/highcharts.js"></script>
<!-- DataTables -->
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/app.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/dist/js/demo.js"></script>
</body>
</html>




