if(!myApp)
{
    var myApp = angular.module('myApp', []);
}
myApp.controller('by3Ctrl', function($scope, $http,$filter) {
    $scope.detail = function(id){
        var item = $scope.ItemList[id];
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: $scope.OldType_str[item.Type].label+"："+item.Label,
            content: "<pre>"+JSON.stringify(item, null, 2)+"</pre>",
            width: W+"px",
            height: H+"px",
            overlayClose: true,
            buttons: [
                {
                    className: 'zeromodal-btn zeromodal-btn-default', 
                    name: '关闭', 
                    fn:function(opt){}
                }
            ]
        });
    }

    baseEX($scope,$http,{
        getPage:"by3page",
        update:"update"
    },"by3EXPageNow");

    
});