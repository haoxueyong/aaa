if(!myApp)
{
    var myApp = angular.module('myApp', []);
}
myApp.controller('ipCtrl', function($scope, $http,$filter) {
    $scope.IPType_str = [
        {
            css:'warning',
            label:'未知'
        },{
            css:'success',
            label:'✓'
        },{
            css:'danger',
            label:'恶意地址'
        },{
            css:'danger',
            label:'钓鱼地址'
        }
    ];
    $scope.detail = function(id){
        var item = $scope.ItemList[id];
        
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: item.IP,
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
        getPage:"ippage",
        update:"update"
    },"IPEXPageNow");
});