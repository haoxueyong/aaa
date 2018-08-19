if(!myApp)
{
    var myApp = angular.module('myApp', []);
}
myApp.directive('setFocus', function(){
  return function(scope, element){
    if(scope.item.id == scope.ariaID){
        element[0].focus();
    }
  };
});
window.onload = function(){
    var sensorChartData = {
        labels: [
            "有告警的计算机",
            "无告警的计算机"
        ],
        datasets: [
        {
            data: [0,0],
            backgroundColor: [
                "#ed561b",
                "#64e572"
            ],
            hoverBackgroundColor: [
                "#ed561b",
                "#64e572"
            ]
        }]
    };
    var sensorChart_doughnutChart = null;
    updateSensorChart = function(newdata)
    {
        var data = sensorChartData.datasets[0].data;
        sensorAlert = parseInt(newdata[0].SensorCount);
        sensorAll = parseInt(newdata[1].SensorCount);
        sensorNotAlert = sensorAll - sensorAlert;
        if(data[0] != sensorAlert || data[1] != sensorNotAlert){
            sensorChartData.datasets[0].data = [
                sensorAlert,
                sensorNotAlert
            ];
            if(sensorChart_doughnutChart){
                sensorChart_doughnutChart.destroy();
            }
            sensorChart_doughnutChart = new Chart(
            $("#sensorChart"), 
            {
                type: "doughnut",
                data: sensorChartData,
                options: {
                    animation:{
                        animateScale:true
                    }
                }
            });
        }
    }

    var AlertChartData = {
    labels: ["邮件", "网页", "USB", "内网", "其他"],
        datasets: [
            {
                label: "来源类型",
                backgroundColor: "rgba(255,99,132,0.2)",
                borderColor: "rgba(255,99,132,1)",
                pointBackgroundColor: "rgba(255,99,132,1)",
                pointBorderColor: "#fff",
                pointHoverBackgroundColor: "#fff",
                pointHoverBorderColor: "rgba(255,99,132,1)",
                data: [0, 0, 0, 0, 0]
            }
        ]
    };
    var alertChart_radarChart = null;
    updateAlertChart = function(data)
    {
        for (var i = data.length - 1; i >= 0; i--) {
            var SrcType = parseInt(data[i].SrcType);
            var AlertCount = parseInt(data[i].AlertCount);
            if(SrcType > 0 && SrcType < 5){
                SrcType = SrcType - 1;
            }else{
                SrcType = 4;
            }
            AlertChartData.datasets[0].data[SrcType] = AlertCount;
        }
        if(alertChart_radarChart == null){
            alertChart_radarChart = new Chart(
            $("#alertChart"), 
            {
                type: "radar",
                data: AlertChartData,
                options: {
                    scale: {
                        ticks: {
                            min:-0.5
                            // ,
                            // stepSize:1
                        }
                    }
                }
            });
        }
        alertChart_radarChart.update();
    }
}
var updateSensorChart = null;
var updateAlertChart = null;
function updateChart(data){
    if(data.chartData){
        if(data.chartData.AlertSensor && updateSensorChart != null){
            updateSensorChart(data.chartData.AlertSensor);
        }
        if(data.chartData.AlertSrcType && updateAlertChart != null){
            updateAlertChart(data.chartData.AlertSrcType);
        }
    }
    
}

function baseEX($scope,$http,ajaxURL,pageNowName)
{


    $scope.pages = {
        data : [],
        count : 0,
        maxPage : "...",
        pageNow : 1,
    };
    $scope.IDList = [];
    $scope.ItemList = {};
    $scope.SrcType_str = [
        {
            css:'warning',
            label:'未知'
        },{
            css:'danger',
            label:'邮件'
        },{
            css:'danger',
            label:'网页'
        },{
            css:'danger',
            label:'USB'
        },{
            css:'danger',
            label:'内网'
        },{
            css:'warning',
            label:'其他'
        }
    ];

    $scope.AlertType_str = [
        {
            css:'warning',
            label:'可疑行为'
        },{
            css:'danger',
            label:'可疑文件'
        },{
            css:'danger',
            label:'可疑IP'
        },{
            css:'danger',
            label:'可疑URL'
        },{
            css:'warning',
            label:'可疑行为'
        },{
            css:'warning',
            label:'可疑行为'
        },{
            css:'danger',
            label:'漏洞利用'
        },{
            css:'danger',
            label:'勒索软件'
        }
    ];
    $scope.status_str = [
        {
            css:'success',
            label:'新告警'
        },{
            css:'danger',
            label:'未解决'
        },{
            css:'default',
            label:'已解决'
        },{
            css:'primary',
            label:'白名单'
        },{
            css:'primary',
            label:'例外'
        }
    ];
    $scope.OldType_str = {
        IP:{
            label:"可疑IP",
            css:"warning"
        },
        URL:{
            label:"可疑URL",
            css:"warning"
        },
        Beh:{
            label:"可疑行为",
            css:"warning"
        },
        File:{
            label:"可疑文件",
            css:"danger"
        },
        Loophole:{
            label:"漏洞利用",
            css:"danger"
        },
        extortion:{
            css:'danger',
            label:'勒索软件'
        }
    };

   
    $scope.setPage = function(rsp)
    {
        if(rsp.data.status!="success")
        {
            return;
        }
        $scope.pages = rsp.data;
        var ID4NewPage = []
        for (var i in $scope.pages.data) {
            var item = $scope.pages.data[i];
            if(item.Label){
                var names = item.Label.split('\\');
                item.Label = names[names.length-1];
            }
            $scope.ItemList[item.id] = item;
            ID4NewPage.push(item.id);
        }
        for (var i = $scope.IDList.length - 1; i >= 0; i--) {
            var ID = $scope.IDList[i]
            if(ID4NewPage.indexOf(ID) == -1)
            {
                $scope.IDList.splice(i,1);
            }
        }
        updateChart(rsp.data);
        sessionStorage.setItem(pageNowName,$scope.pages.pageNow);
    }

    $scope.getPage = function(pageNow)
    {
        pageNow = pageNow ? pageNow : sessionStorage.getItem(pageNowName);
        $scope.pageGeting = true;
        $http.post(ajaxURL.getPage,{page:pageNow}).then(function success(rsp){
            $scope.pageGeting = false;
            $scope.setPage(rsp);
        },function err(rsp){
            $scope.pageGeting = false;
        });
    }

    $scope.selectAll = function(){
        if($scope.IDList.length == $scope.pages.data.length){
            $scope.IDList = [];
        }else{
            $scope.IDList = [];
            for (var i in $scope.pages.data) {
                var item = $scope.pages.data[i];
                $scope.IDList.push(item.id);
            }
        }
    }
    $scope.selectOne = function(id,$event){
        $event.stopPropagation();
        var index = $scope.IDList.indexOf(id);
        if(index == -1){
            $scope.IDList.push(id);
        }else{
            $scope.IDList.splice(index,1);
        }
    }

    $scope.update = function(type,item){
        var rqs_data = {
            type:type,
            page:sessionStorage.getItem(pageNowName),
            List:[]
        };
        var send = function(){
            var loading = zeroModal.loading(4);
            $http.post(ajaxURL.update,rqs_data).then(function success(rsp){
                $scope.setPage(rsp);
                zeroModal.close(loading);
            },function err(rsp){
                zeroModal.close(loading);
            });
        }
        if(item)
        {
            rqs_data.List.push(item);
            send();
        }else{
            IDValidate(function(){
                for (var i = $scope.IDList.length - 1; i >= 0; i--) {
                    rqs_data.List.push($scope.ItemList[$scope.IDList[i]]);
                }
                send();
            });
        }
    }

    function IDValidate(callback){

        if($scope.IDList.length == 0)
        {
            zeroModal.error({
                content: '操作失败',
                contentDetail: '请选择计算机！'
            });
            return;
        }
        callback();
    }

    $scope.detail = function(id){
        var item = $scope.ItemList[id];
        switch(item.Type)
        {
            case "File":
                location.href = "file?id="+item.rid;
                break;
            case "IP":
                location.href = "ip?id="+item.rid;
                break;
            case "URL":
                location.href = "url?id="+item.rid;
                break;
            case "Beh":
                if(item.AlertType == 6){
                    location.href = "loophole?id="+item.rid;
                }else if(item.AlertType == 7){
                    location.href = "extortion?id="+item.rid;
                }
                else{
                    location.href = "beh?id="+item.rid;
                }
                break;
        }
    }

    $scope.ariaID = null;
    $scope.setAriaID = function(item,$event){
        $event.stopPropagation();
        if($scope.ariaID == item.id){
            $scope.ariaID = null;
        }else{
            $scope.ariaID = item.id;
        }
        // $scope.dropdown_menu = {
        //     position:'fixed',
        //     top:'200px',
        //     left:'200px'
        // }
    }
    $scope.delAriaID = function($event){
        $event.stopPropagation();
        setTimeout(function(){
            $scope.ariaID = null;
        },10);
    }
    $scope.stopPropagation = function($event){
        // $event.stopPropagation();
    }
    $scope.getPage();
    $scope.pageGeting = false;
    setInterval(function(){
        if(!$scope.pageGeting)
        {
            $scope.getPage();
        }
    },5000);
}
