if(!myApp)
{
    var myApp = angular.module('myApp', []);
}
myApp.controller('urlCtrl', function($scope, $http,$filter) {
    $scope.URLType_str = [
        {
            css:'warning',
            label:'未知'
        },{
            css:'success',
            label:'✓'
        },{
            css:'danger',
            label:'僵尸链接'
        },,{
            css:'danger',
            label:'恶意链接'
        },{
            css:'danger',
            label:'钓鱼链接'
        }
    ];
    $scope.detail = function(id){
        var item = $scope.ItemList[id];
        
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: item.URL,
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
        getPage:"urlpage",
        update:"update"
    },"URLEXPageNow");
});