<div class="col-md-3" id="event_detail">
              <div class="box box-solid" style="background-color:#f5f5f5;">
                <div class="box-header with-border">
                  <h3 class="box-title">详情</h3>
                  <div class="box-tools">
                    <i class="fa fa-close"></i>
                  </div>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                  <table class="table">
                    <tbody>
                      <tr>
                        <th>类型:</th>
                        <td ng-bind="detail.TypeName"></td>
                      </tr>

                      <!--  name -->
                      <tr ng-if="detail.ProcessName">
                        <th>进程名:</th>
                        <td ng-bind="detail.ProcessName"></td>
                      </tr>
                      <tr ng-if="detail.ServiceName">
                        <th>服务名:</th>
                        <td ng-bind="detail.ServiceName"></td>
                      </tr>
                      




                      <tr ng-if="detail.PID">
                        <th>进程ID:</th>
                        <td ng-bind="detail.PID"></td>
                      </tr>
                      <tr ng-if="detail.ParentName">
                        <th>父进程:</th>
                        <td ng-bind="detail.ParentName"></td>
                      </tr>
                      <tr ng-if="detail.ParentPID">
                        <th>父进程ID:</th>
                        <td ng-bind="detail.ParentPID"></td>
                      </tr>
                      <tr ng-if="detail.ImagePath">
                        <th>文件路径:</th>
                        <td ng-bind="detail.ImagePath"></td>
                      </tr>
                      <tr ng-if="detail.FilePath">
                        <th>文件路径:</th>
                        <td ng-bind="detail.FilePath"></td>
                      </tr>
                      <tr ng-if="detail.FileSize">
                        <th>文件大小:</th>
                        <td ng-bind="detail.FileSize"></td>
                      </tr>
                      <tr ng-if="detail.MD5">
                        <th>MD5:</th>
                        <td ng-bind="detail.MD5"></td>
                      </tr>
                      <tr ng-if="detail.SHA256">
                        <th>SHA256:</th>
                        <td ng-bind="detail.SHA256"></td>
                      </tr>
                      <tr ng-if="detail.CommandLine">
                        <th>命令行:</th>
                        <td ng-bind="detail.CommandLine"></td>
                      </tr>
                      <tr ng-if="detail.Signer">
                        <th>签名:</th>
                        <td ng-bind="detail.Signer"></td>
                      </tr>

                      <!-- OBJ_FILE -->
                      <tr ng-if="detail.IsPE">
                        <th>文件类型:</th>
                        <td ng-bind="detail.IsPE?'可执行文件':''"></td>
                      </tr>


                      <!-- OBJ_WIN_REG_KEY -->
                      <tr ng-if="detail.Key">
                        <th>注册表项:</th>
                        <td ng-bind="detail.Key"></td>
                      </tr>
                      <tr ng-if="detail.ValueName">
                        <th>注册表名称:</th>
                        <td ng-bind="detail.ValueName"></td>
                      </tr>
                      <tr ng-if="detail.ValueType">
                        <th>注册表类型:</th>
                        <td ng-bind="detail.ValueType"></td>
                      </tr>
                      <tr ng-if="detail.ValueData">
                        <th>值:</th>
                        <td ng-bind="detail.ValueData"></td>
                      </tr>


                      <!-- OBJ_NETWORK_CONN -->
                      <tr ng-if="detail.LocalIP">
                        <th>本地IP地址:</th>
                        <td ng-bind="detail.LocalIP"></td>
                      </tr>
                      <tr ng-if="detail.LocalPort">
                        <th>本地端口:</th>
                        <td ng-bind="detail.LocalPort"></td>
                      </tr>
                      <tr ng-if="detail.RemoteIP">
                        <th>访问IP地址:</th>
                        <td ng-bind="detail.RemoteIP"></td>
                      </tr>
                      <tr ng-if="detail.RemotePort">
                        <th>访问端口:</th>
                        <td ng-bind="detail.RemotePort"></td>
                      </tr>
                      <tr ng-if="detail.ConnectType">
                        <th>连接类型:</th>
                        <td ng-bind="detail.ConnectType"></td>
                      </tr>

                      <!-- OBJ_WIN_VOLUME -->
                      <tr ng-if="detail.DriverType">
                        <th>驱动类型:</th>
                        <td ng-bind="detail.DriverType"></td>
                      </tr>
                      <tr ng-if="detail.DriverLetter">
                        <th>驱动器号:</th>
                        <td ng-bind="detail.DriverLetter"></td>
                      </tr>

                      <!-- OBJ_WIN_USER_ACCOUNT -->
                      <tr ng-if="detail.UserName">
                        <th>用户名:</th>
                        <td ng-bind="detail.UserName"></td>
                      </tr>
                      <tr ng-if="detail.SID">
                        <th>SID:</th>
                        <td ng-bind="detail.SID"></td>
                      </tr>
                      <tr ng-if="detail.UserGroup">
                        <th>用户组:</th>
                        <td ng-bind="detail.UserGroup"></td>
                      </tr>

                      <!-- OBJ_DNS -->
                      <tr ng-if="detail.DomainName">
                        <th>域名:</th>
                        <td ng-bind="detail.DomainName"></td>
                      </tr>
                      <tr ng-if="detail.Success">
                        <th>是否成功:</th>
                        <td ng-bind="detail.Success"></td>
                      </tr>

                      <!-- OBJ_NETWORK_SHARE -->
                      <tr ng-if="detail.LocalPath">
                        <th>本地路径:</th>
                        <td ng-bind="detail.LocalPath"></td>
                      </tr>
                      <tr ng-if="detail.NetName">
                        <th>网络名称:</th>
                        <td ng-bind="detail.NetName"></td>
                      </tr>

                      <!-- OBJ_ADDRESS -->
                      <tr ng-if="detail.IP">
                        <th>IP地址:</th>
                        <td ng-bind="detail.IP"></td>
                      </tr>

                      <!-- OBJ_USER_LOGON -->
                      <tr ng-if="detail.LogonType">
                        <th>登录类型:</th>
                        <td ng-bind="detail.LogonType"></td>
                      </tr>

                      <!-- OBJ_ERROR -->
                      <tr ng-if="detail.fileError">
                        <th>可疑文件:</th>
                        <td>Yes</td>
                      </tr>
                      <tr ng-if="detail.fileDesc">
                        <th>可疑描述:</th>
                        <td ng-bind="detail.fileDesc"></td>
                      </tr>
                      <tr ng-if="detail.ipError">
                        <th>可疑IP:</th>
                        <td>Yes</td>
                      </tr>
                      <tr ng-if="detail.urlError">
                        <th>可疑URL:</th>
                        <td>Yes</td>
                      </tr>
                      <tr ng-if="detail.EventTime">
                        <th>时间:</th>
                        <td ng-bind="detail.EventTime | date:'yyyy-MM-dd HH:mm:ss'"></td>
                      </tr>
                      <tr ng-repeat="item in detail.HitRegulationList">
                        <th>可疑行为{{($index+1)}}:</th>
                        <td ng-bind="item.RegDesc"></td>
                      </tr>

                      <?php if(Yii::$app->user->identity->role == 'admin'){?>
                      <tr ng-if="detail.kill && detail.PID && detail.ImagePath">
                        <th>操作:</th>
                        <td>
                          <button class="btn btn-danger btn-xs" ng-click="kill(detail)">
                            <span>结束进程</span>
                            <i class="fa fa-close"></i>
                          </button>
                        </td>
                      </tr>
                      <?php }?>
                      
                    </tbody>
                  </table>
                  
                </div>
                <!-- /.box-body -->
              </div>
            </div>