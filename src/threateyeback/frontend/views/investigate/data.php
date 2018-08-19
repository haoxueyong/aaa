<div class="row ng-cloak" ng-show="showTable == 'Computer'">
    


    <div class="col-md-12 ng-scope" id="ComputerDetails_col" ng-show="nowSensor">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-laptop"></i>
                    <span ng-bind="nowSensor.ComputerName"></span>
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="col-md-6 border-right">
                    <ul class="nav nav-stacked sensor-detail">
                        <li>
                            <span class="sensor-detail-title">操作系统</span>
                            <span ng-bind="nowSensor.OSType"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title">所在域</span>
                            <span ng-bind="nowSensor.Domain"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title">IP地址</span>
                            <span ng-bind="nowSensor.IP"></span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 border-right">
                    <ul class="nav nav-stacked sensor-detail">
                        <li>
                            <span class="sensor-detail-title-long">状态</span>
                            <span class="label label-{{status_str[nowSensor.status].css}}" ng-bind="status_str[nowSensor.status].label"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title-long">Sensor版本</span>
                            <span ng-bind="nowSensor.SensorVersion"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title-long">最近一次通讯</span>
                            <span ng-bind="nowSensor.updated_at*1000 | date:'yyyy-MM-dd HH:mm'"></span>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
        <div id="hide_box" style="display: none;">
            <table id="sensorList" class="table table-hover selectSensor" >
                <thead>
                    <tr>
                        <th>计算机名</th>
                        <th>IP地址</th>
                        <th>最近一次通讯</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="item in sensorList" ng-class="item.SensorID == selectSensor.SensorID ? 'focus' : ''" ng-click='select(item)'>
                        <td ng-bind="item.ComputerName"></td>
                        <td ng-bind="item.IP"></td>
                        <td ng-bind="item.updated_at*1000 | date:'yyyy-MM-dd HH:mm'"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-12 ng-scope col-data" id="UserLogon_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-user-circle-o"></i> 用户登录信息
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="UserLogon" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>计算机名</th>
                                    <th>登录类型</th>
                                    <th>次数</th>
                                    <th>最近一次登录时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
    
    <div class="col-md-12 ng-scope col-data" id="NetProcess_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-globe"></i> 带有网络链接的进程
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="NetProcess" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>计算机名</th>
                                    <th>进程</th>
                                    <th>PID</th>
                                    <th>命令</th>
                                    <th>本地端口</th>
                                    <th>远端IP</th>
                                    <th>远端端口</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>

    <div class="col-md-12 ng-scope col-data" id="UsbPlug_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-usb"></i> 外接移动设备
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="UsbPlug" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>计算机名</th>
                                    <th>外接设备名称</th>
                                    <th>盘符</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>

    <div class="col-md-12 ng-scope col-data" id="ConnectedComputer_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-link"></i> 与这台计算机通讯过的机器
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="ConnectedComputer" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>计算机名</th>
                                    <th>IP地址</th>
                                    <th>本地端口</th>
                                    <th>远端计算机端口</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
</div>


<div class="row ng-cloak" ng-show="showTable == 'File'">
    <div class="col-md-12 ng-scope" id="FileDetails_col" ng-show="nowFile">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-file-o"></i>
                    <span ng-bind="nowFile.FileName"></span>
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="col-md-6 border-right">
                    <ul class="nav nav-stacked sensor-detail">
                        <li>
                            <span class="sensor-detail-title-Long">哈希值</span>
                            <span ng-bind="nowFile.MD5"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title-Long">首次出现的计算机</span>
                            <span ng-bind="nowFile.FristComputerName"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title-Long">最近出现的计算机</span>
                            <span ng-bind="nowFile.LastComputerName"></span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 border-right">
                    <ul class="nav nav-stacked sensor-detail">
                        <li>
                            <span class="sensor-detail-title-Long">计算机数量</span>
                            <span ng-bind="nowFile.FileComputer.Count"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title-Long">首次出现的日期</span>
                            <span ng-bind="nowFile.FristTime*1000 | date:'yyyy-MM-dd HH:mm'"></span>
                        </li>
                        <li>
                            <span class="sensor-detail-title-Long">最近出现的日期</span>
                            <span ng-bind="nowFile.LastTime*1000 | date:'yyyy-MM-dd HH:mm'"></span>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-12 ng-scope col-data" id="FileComputer_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-user-circle-o"></i> 出现过该文件的计算机
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="FileComputer" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>用户</th>
                                    <th>计算机名</th>
                                    <th>IP地址</th>
                                    <th>文件名</th>
                                    <th>进程名</th>
                                    <th>PID</th>
                                    <th>哈希值</th>
                                    <th>子进程</th>
                                    <th>父进程</th>
                                    <th>CommandLine</th>
                                    <th>状态</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
</div>


<div class="row ng-cloak" ng-show="showTable == 'FileTransfer'">
    <div class="col-md-12 ng-scope col-data" id="IMProcess_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-share-alt"></i> 文件传输
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="IMProcess" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>计算机名</th>
                                    <th>IP地址</th>
                                    <th>文件名</th>
                                    <th>进程名</th>
                                    <th>PID</th>
                                    <th>MD5</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
</div>


<div class="row ng-cloak" ng-show="showTable == 'Signer'">
    <div class="col-md-12 ng-scope col-data" id="SignerFile_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-pencil-square-o"></i> 文件签名
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="SignerFile" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>文件名</th>
                                    <th>MD5</th>
                                    <th>SHA256</th>
                                    <th>签名</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
</div>

<div class="row ng-cloak" ng-show="showTable == 'Domain'">
    <div class="col-md-12 ng-scope col-data" id="DomainProcess_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-internet-explorer"></i> 域名查询
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="DomainProcess" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>计算机名</th>
                                    <th>IP地址</th>
                                    <th>OS</th>
                                    <th>进程名</th>
                                    <th>PID</th>
                                    <th>文件路径</th>
                                    <th>最早一次访问域名</th>
                                    <th>最近一次访问域名</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
</div>


<div class="row ng-cloak" ng-show="showTable == 'User'">
    <div class="col-md-12 ng-scope col-data" id="LogonEvent_col">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-user-circle-o"></i> 用户调查
                </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="nav-tabs-custom" style="margin-bottom: 0px">
                    <div class="tab-content">
                        <table id="LogonEvent" class="table table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>计算机名</th>
                                    <th>IP地址</th>
                                    <th>域名</th>
                                    <th>登录</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        <!-- /.box-body -->
        </div>
    <!-- /.box -->
    </div>
</div>



