if(!myApp)
{
    var myApp = angular.module('myApp', []);
}

myApp.controller('SensorVersionCtrl', function($scope, $http,$filter) {
    

    $scope.detail = function(index){
        var Version = $scope.pages.data[index];
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: "探针版本:"+Version.Version,
            content: "<pre>"+JSON.stringify(Version, null, 2)+"</pre>",
            width: W+"px",
            height: H+"px",
            cancel: true,
            overlayClose: true,
            onCleanup: function() {
            }
        });
    }

    $scope.setDefault = function(id,$event){
        $event.stopPropagation();
        rqs_data = {
            id : id,
            page:$scope.pages.pageNow
        };
        var loading = zeroModal.loading(4);
        $http.post("sensordefault",rqs_data).then(function success(rsp){
            zeroModal.close(loading);
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    }

    $scope.del = function(id,$event){
        $event.stopPropagation();
        zeroModal.confirm({
            content: '操作提示',
            contentDetail: '是否确认删除吗？',
            okFn: function() {
                rqs_data = {
                    id : id,
                    page:$scope.pages.pageNow
                };
                var loading = zeroModal.loading(4);
                $http.post("sensordel",rqs_data).then(function success(rsp){
                    zeroModal.close(loading);
                    if(rsp.data.status == 'success')
                    {
                        $scope.pages = rsp.data;
                    }
                },function err(rsp){
                    zeroModal.close(loading);
                });
            },
            cancelFn: function() {
            }
        });
    }
    
    $scope.createdVersion = function(){
        var W = $(".content").width()*0.5;
        W = W > 480 ? W : 480;
        var H = (W/4)*3;
        var send = null;
        var loading = null;
        var box = null;
        $scope.sensorVersion={
            Version:null,
            status:true
        }
        $('#InputFile').attr("data-url","addversion");
        $('#InputFile').attr("name","file");
        $('#InputFile').fileupload({
            dataType: 'json',
            autoUpload:false,
            progressall:function(e,data){
                // var progress=parseInt(data.loaded/data.total*100,10);
                // // $('#load').html(progress+'%');
                // $scope.sensorVersion.load = progress+'%';
                // $scope.$apply();
            },
            add: function (e, data) {
                $scope.sensorVersion.FileName = data.files[0].name;
                $scope.sensorVersion.nofile = false;
                $scope.$apply();
                send = data;
            },
            success:function(data)
            {
                if(data.status == 'success')
                {
                    $scope.pages = data;
                    $scope.$apply();
                    history.replaceState({page: $scope.pages.pageNow}, "versionPage", "?page="+$scope.pages.pageNow);
                }
            },complete:function(data)
            {
                hideenBox.appendChild(newVersion);
                zeroModal.close(loading);
                zeroModal.close(box);
            }
        });
        box = zeroModal.show({
            title: '添加新的探针版本',
            content: newVersion,
            width: W+"px",
            height: H+"px",
            ok: true,
            cancel: true,
            okFn: function() {
                var Version = $scope.sensorVersion.Version;
                var flag = true;
                if(send == null)
                {
                    $scope.sensorVersion.nofile = true;
                    flag = false;
                }
                if(Version == null || Version.length==0)
                {
                    $scope.sensorVersion.Version = "";
                    flag = false;
                }
                $scope.$apply();
                if(!flag)
                {
                    return false;
                }
                loading = zeroModal.loading(4);
                send.submit();
                return false;
            },
            onCleanup: function() {
                hideenBox.appendChild(newVersion);
            }
        });
    }
    baseEX($scope,$http,{
        getPage:"versionpage"
    });

    

});
