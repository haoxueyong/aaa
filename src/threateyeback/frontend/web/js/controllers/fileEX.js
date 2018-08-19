if(!myApp)
{
    var myApp = angular.module('myApp', []);
}
myApp.controller('fileCtrl', function($scope, $http,$filter) {
    $scope.detail = function(id){
        var item = $scope.ItemList[id];
        
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: item.FilePath,
            content: "<pre>"+JSON.stringify(item, null, 2)+"</pre>",
            width: W+"px",
            height: H+"px",
            overlayClose: true,
            buttons: [
                {
                    className: 'zeromodal-btn zeromodal-btn-success',    
                    name: '添加到白名单', 
                    fn:function(opt)
                    { 
                        $scope.update("setWhite",item);
                    }
                },
                {
                    className: 'zeromodal-btn zeromodal-btn-primary',    
                    name: '已解决', 
                    fn:function(opt)
                    { 
                        $scope.update("setOld",item);
                    }
                },
                {
                    className: 'zeromodal-btn zeromodal-btn-default', 
                    name: '取消', 
                    fn:function(opt){}
                }
            ]
        });
    }

    baseEX($scope,$http,{
        getPage:"filepage",
        update:"update"
    },"FileEXPageNow");
});