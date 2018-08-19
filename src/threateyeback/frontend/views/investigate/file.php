<!-- file -->
			<div class="tab-pane" id="file" ng-controller="FileCtrl">
				<div class="row margin">
	                <div class="form-group col-md-2">
	                    <label>文件名</label>
	                    <input type="text" class="form-control" ng-model="search_data.FileName">
	                </div>
	                <div class="form-group col-md-2">
	                    <label>MD5或SHA256</label>
	                    <input type="text" class="form-control" ng-model="search_data.MD5OrSHA256">
	                </div>
	                <div class="form-group col-md-2">
	                    <label>命令行</label>
	                    <input type="text" class="form-control" ng-model="search_data.CommandLine">
	                </div>
	                <div class="form-group col-md-2">
	                    <label>计算机名</label>
	                    <input type="text" class="form-control" ng-model="search_data.ComputerName">
	                </div>
	                <div class="form-group col-md-2">
	                    <label>文件路径排除</label>
	                    <input type="text" class="form-control" ng-model="search_data.IgnorePath">
	                </div>
	                <div class="form-group col-md-2">
	                    <label>父进程排除</label>
	                    <input type="text" class="form-control" ng-model="search_data.IgnoreParentName">
	                </div>
	                <div class="form-group col-md-3">
	                    <label>时间范围</label>
	                    <input type="text" class="form-control timerange" readonly style="background-color: #fff;">
	                </div>
	                <div class="form-group col-md-2 pull-right">
	                    <label style="width: 100%;">&nbsp;</label>
	                    <button class="form-control btn btn-success pull-right" style="max-width: 80px;" ng-click="search()">搜&nbsp;&nbsp;索</button>
	                </div>
	            </div>
			</div>
			<!-- /file -->