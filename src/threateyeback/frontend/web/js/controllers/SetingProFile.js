if(!myApp)
{
    var myApp = angular.module('myApp', []);
}

myApp.controller('ProFileCtrl', function($scope, $http,$filter) {
    

    $scope.detail = function(index){
        return;
        var Version = $scope.pages.data[index];
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: "配置文件版本:"+Version.Version,
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
        $http.post("profiledefault",rqs_data).then(function success(rsp){
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
                $http.post("profiledel",rqs_data).then(function success(rsp){
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
        $scope.ProFile={
            Version:null,
            status:true
        }
        $('#InputFile').attr("data-url","addprofile");
        $('#InputFile').attr("name","file");
        $('#InputFile').fileupload({
            dataType: 'json',
            autoUpload:false,
            progressall:function(e,data){
                // var progress=parseInt(data.loaded/data.total*100,10);
                // // $('#load').html(progress+'%');
                // $scope.ProFile.load = progress+'%';
                // $scope.$apply();
            },
            add: function (e, data) {
                var names = data.files[0].name.split('.');
                if(names[names.length-1].toLowerCase() != 'json'){
                    $scope.ProFile.nofile = true;
                    $scope.$apply();
                    return;
                }
                $scope.ProFile.FileName = data.files[0].name;
                $scope.ProFile.nofile = false;
                send = data;
                $scope.$apply();
            },
            success:function(data)
            {
                if(data.status == 'success')
                {
                    $scope.pages = data;
                    $scope.$apply();
                    history.replaceState({page: $scope.pages.pageNow}, "profilepage", "?page="+$scope.pages.pageNow);
                }
            },complete:function(data)
            {
                hideenBox.appendChild(newVersion);
                zeroModal.close(loading);
                zeroModal.close(box);
            }
        });
        box = zeroModal.show({
            title: '添加新的配置文件',
            content: newVersion,
            width: W+"px",
            height: H+"px",
            ok: true,
            cancel: true,
            okFn: function() {
                var Version = $scope.ProFile.Version;
                var flag = true;
                if(send == null)
                {
                    $scope.ProFile.nofile = true;
                    flag = false;
                }
                if(Version == null || Version.length==0)
                {
                    $scope.ProFile.Version = "";
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
        getPage:"profilepage"
    });

    

});
