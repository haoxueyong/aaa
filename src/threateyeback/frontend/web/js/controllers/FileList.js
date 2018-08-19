var app = angular.module('myApp', []);
function baseEX($scope,$http,ajaxURL,pageNowName)
{
    $scope.pages = {
        data : [],
        count : 0,
        maxPage : "...",
        pageNow : 1,
    };
    $scope.getPage = function(pageNow)
    {
        pageNow = pageNow ? pageNow : 1;
        $http.post(ajaxURL.getPage,{page:pageNow}).then(function success(rsp){
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
            }
        },function err(rsp){
        });
    }
    $scope.getPage();

    $scope.del = function(status,id,$event){
        $event.stopPropagation();
        zeroModal.confirm({
            content: '操作提示',
            contentDetail: '是否确认删除吗？',
            okFn: function() {
                rqs_data = {
                    status:status,
                    id : id,
                    page:$scope.pages.pageNow
                };
                var loading = zeroModal.loading(4);
                $http.post("/file/del",rqs_data).then(function success(rsp){
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

    $scope.sendFile = function(id,$event){
        rqs_data = {
            MD5 : $scope.File.MD5.toLowerCase(),
            SHA256 : $scope.File.SHA256.toLowerCase(),
            status : $scope.File.status,
            page:$scope.pages.pageNow
        };
        var loading = zeroModal.loading(4);
        $http.post("/file/addfile",rqs_data).then(function success(rsp){
            zeroModal.close(loading);
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    }
    $scope.add = function(type){
        var W = $(".content").width()*0.5;
        W = W > 480 ? W : 480;
        var H = (W/4)*3;
        var box = null;
        $scope.File={
            MD5:'',
            SHA256:'',
            status:type
        }

        var titles = {
            '3':'添加白名单',
            '4':'添加黑名单'
        };

        var hideenBox = null;
        var newFile = null;

        if(type == '3'){
           hideenBox = hideenBox_white;
           newFile = newFile_white;
        }else
        {
            hideenBox = hideenBox_black;
            newFile = newFile_black;
        }

        box = zeroModal.show({
            title: titles[type],
            content: newFile,
            width: W+"px",
            height: H+"px",
            ok: true,
            cancel: true,
            okFn: function() {
                var MD5 = $scope.File.MD5;
                var flag = true;

                if(MD5 == null || MD5.length==0)
                {
                    $scope.File.MD5 = "";
                    flag = false;
                    $scope.MD5error = true;
                }else if(!/^[0-9A-Za-z]{32}$/.test(MD5)){
                    $scope.MD5error = true;
                    flag = false;
                }else{
                    $scope.MD5error = false;
                }

                $scope.$apply();
                if(!flag)
                {
                    return false;
                }
                $scope.sendFile();
            },
            onCleanup: function() {
                hideenBox.appendChild(newFile);
            }
        });
    }
}



if(!myApp)
{
    var myApp = angular.module('myApp', []);
}

myApp.controller('WhiteCtrl', function($scope, $http,$filter) {
    baseEX($scope,$http,{
        getPage:"/file/whitepage"
    });
});

myApp.controller('BlackCtrl', function($scope, $http,$filter) {
    baseEX($scope,$http,{
        getPage:"/file/blackpage"
    });
});



