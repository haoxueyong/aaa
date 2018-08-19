<!-- signer -->
			<div class="tab-pane" id="signer" ng-controller="SignerCtrl">
				<div class="row margin">
	                <div class="form-group col-md-2">
	                    <label>公司名</label>
	                    <input type="text" class="form-control" ng-model="search_data.Signer">
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
			<!-- /signer -->