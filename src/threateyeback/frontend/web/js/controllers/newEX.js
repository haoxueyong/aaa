if(!myApp)
{
    var myApp = angular.module('myApp', []);
}
myApp.controller('behCtrl', function($scope, $http,$filter) {
    $scope.detail = function(id){
        var item = $scope.ItemList[id];
        item['Beh'] = 1;
        var W = $(".content").width();
        var H = (W/16)*9;
        zeroModal.show({
            title: "告警ID："+item.AlertID,
            content: "<pre>"+JSON.stringify(item, null, 2)+"</pre>",
            width: W+"px",
            height: H+"px",
            overlayClose: true,
            buttons: [
                
                {
                    className: 'zeromodal-btn zeromodal-btn-primary',    
                    name: '已解决', 
                    fn:function(opt)
                    { 
                        var type = item.Type == "Beh" ? "setOldBeh" : "setOld";
                        $scope.update(type,item);
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
        getPage:"newpage",
        update:"update"
    },"NewEXPageNow");


    $scope.getChart = function(){
        $http.post('chart-data',{}).then(function success(rsp){
            updateChart(rsp.data);
        },function err(rsp){
        });
    }
    $scope.getChart();
    setInterval(function(){
        $scope.getChart();
    },10000);
    
});
