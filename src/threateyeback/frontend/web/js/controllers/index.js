Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

window.onload = function(){
    var sensorChartData = {
        labels: [],
        datasets: [{
            label: "每日告警计算机数(2周内)",
            fill: false,
            lineTension: 0.3,
            backgroundColor: "rgba(54, 162, 235,0.4)",
            borderColor: "rgba(54, 162, 235,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(54, 162, 235,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 10,
            pointHoverBackgroundColor: "rgba(54, 162, 235,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 6,
            pointHitRadius: 10,
            pointStyle:'rectRot',
            data: [],
            spanGaps: false
        }]
    };
    var sensor_lineChart = new Chart(
        $("#sensorChart"),
        {
            type: 'line',
            data: sensorChartData,
            options:
            {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        },
                        scaleLabel:{
                            display: true,
                            labelString:"计算机数量"
                        }
                    }],
                    xAxes: [{
                        scaleLabel:{
                            display: true,
                            labelString:"告警日期"
                        }
                    }]
                },
                animation:{
                    animateScale:true
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            var label = data.labels[tooltipItem.index];
                            var percentage = Math.round(value / 100 * 100);
                            return value+'台计算机';
                        }
                    }
                }
            }
        }
    );
    var updateSensorChart = function(data)
    {
        var sensorLine = {
            labels:[],
            datas:[]
        };
        var keys = {};

        for (var i = 0; i < 14; i++) {
            var date = new Date();
            date.setDate(date.getDate()-i);
            var label = date.Format("yyyy-MM-dd");

            keys[label] = 13-i;
            keys[label+"sidList"] = [];

            sensorLine.labels[13-i] = label;
            sensorLine.datas[13-i] = 0;
        }
        for (var i = data.length - 1; i >= 0; i--) {
            var sensor = data[i];
            var date = new Date(sensor.created_at*1000);
            var label = date.Format("yyyy-MM-dd");

            if(keys[label+"sidList"].indexOf(sensor.SensorID) == -1)
            {
                keys[label+"sidList"].push(sensor.SensorID);
                sensorLine.datas[keys[label]]++;
            }
            
        }
        sensorChartData.labels = sensorLine.labels;
        sensorChartData.datasets[0].data = sensorLine.datas;
        sensor_lineChart.update();
    }



    var sensorTopData = {
        labels: [],
        datasets: [
            {
                label: "告警最多的计算机(3个月内)",
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1,
                data: [],
            }
        ]
    };
    var sensorTop_lineChart = new Chart(
        $("#sensorTopChart"), 
        {
            type: "bar",
            data: sensorTopData,
            options: {
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero:true
                        },
                        scaleLabel:{
                            display: true,
                            labelString:"告警数量"
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            autoSkip:false
                        },
                        barPercentage: 0.5,
                        scaleLabel:{
                            display: true,
                            labelString:"计算机名称"
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            var label = data.labels[tooltipItem.index];
                            var percentage = Math.round(value / 100 * 100);
                            return value+'次告警';
                        }
                    }
                }
            }
        });
    var updateSensorTopChart = function(data)
    {
        for (var i in data) {
            var sensor = data[i];
            sensorTopData.datasets[0].data.push(sensor.count);
            sensorTopData.labels.push(sensor.ComputerName);
        }
        for (var i = (10 - sensorTopData.labels.length); i > 0; i--) {
            sensorTopData.labels.push('');
        }
        sensorTop_lineChart.update();
    }





    var fileChartData = {
        labels: [],
        datasets: [{
            label: "可疑文件数(2周内)",
            fill: false,
            lineTension: 0.3,
            backgroundColor: "rgba(255, 99, 132,0.4)",
            borderColor: "rgba(255, 99, 132,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(255, 99, 132,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 10,
            pointHoverBackgroundColor: "rgba(255, 99, 132,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointStyle:'rectRot',
            pointRadius: 6,
            pointHitRadius: 10,
            data: [],
            spanGaps: false
        }]
    };
    var file_lineChart = new Chart(
        $("#fileChart"),
        {
            type: 'line',
            data: fileChartData,
            options:
            {
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero:true
                        },
                        scaleLabel:{
                            display: true,
                            labelString:"可疑文件数量"
                        }
                    }],
                    xAxes: [{
                        scaleLabel:{
                            display: true,
                            labelString:"告警日期"
                        }
                    }]
                },
                animation:{
                    animateScale:true
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            var label = data.labels[tooltipItem.index];
                            var percentage = Math.round(value / 100 * 100);
                            return value+'个可疑文件';
                        }
                    }
                }
            }
        }
    );
    var updateFileChart = function(data)
    {
        var fileLine = {
            labels:[],
            datas:[]
        };
        var keys = {};

        for (var i = 0; i < 14; i++) {
            var date = new Date();
            date.setDate(date.getDate()-i);
            var label = date.Format("yyyy-MM-dd");

            keys[label] = 13-i;
            keys[label+"FileidList"] = [];

            fileLine.labels[13-i] = label;
            fileLine.datas[13-i] = 0;
        }
        for (var i = data.length - 1; i >= 0; i--) {
            var file = data[i];
            var date = new Date(file.created_at*1000);
            var label = date.Format("yyyy-MM-dd");

            if(keys[label+"FileidList"].indexOf(file.id) == -1)
            {
                keys[label+"FileidList"].push(file.id);
                fileLine.datas[keys[label]]++;
            }
        }
        fileChartData.labels = fileLine.labels;
        fileChartData.datasets[0].data = fileLine.datas;
        file_lineChart.update();
    }

    var fileTopData = {
        labels: [],
        datasets: [
            {
                label: "告警最多的文件(3个月内)",
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1,
                data: []
            }
        ]
    };
    var fileTop_lineChart = new Chart(
        $("#fileTopChart"), 
        {
            type: "bar",
            data: fileTopData,
            options: {
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero:true
                        },
                        scaleLabel:{
                            display: true,
                            labelString:"可疑文件告警数量"
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            autoSkip:false,
                            callback: function(value, index, values) {
                                if(value.length > 15){
                                    value = value.substr(0,15)+'...';
                                }
                                return value;
                            }
                        },
                        barPercentage: 0.5,
                        scaleLabel:{
                            display: true,
                            labelString:"可疑文件名"
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        title: function(arr, data) {
                            var tooltipItem = arr[0];
                            var label = data.labels[tooltipItem.index];
                            return label;
                        },
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            var label = data.labels[tooltipItem.index];
                            var percentage = Math.round(value / 100 * 100);
                            return value+'次告警';
                        }
                    }
                }
            }
        });
    var updateFileTopChart = function(data)
    {
        for (var i in data) {
            var file = data[i];
            fileTopData.datasets[0].data.push(file.count);
            var names = file.Label.split('\\');
            Label = names[names.length-1];

            fileTopData.labels.push(Label);
        }
        for (var i = (10 - fileTopData.labels.length); i > 0; i--) {
            fileTopData.labels.push('');
        }
        fileTop_lineChart.update();
    }


    // var updateOSChart = function(data)
    // {
        
    //     rootScope.OSData = {};


    //     max = 0;
    //     for (var i = data.length - 1; i >= 0; i--) {
    //         var d = data[i];
    //         if(!rootScope.OSData[d.OSTypeShort])
    //         {
    //             rootScope.OSData[d.OSTypeShort] = {};
    //         }
    //         var count = parseInt(d.count);
    //         rootScope.OSData[d.OSTypeShort][d.type] = count;

    //         if(count > max)
    //         {
    //             max = count;
    //         }
            
    //     }

    //     for (var key in rootScope.OSData) {
    //         var item = rootScope.OSData[key];

    //         var all = parseInt(item.all);
    //         var alert = parseInt(item.alert ? item.alert : 0);


    //         item.all_P = {width:((all-alert) * 100)/max+"%"};
    //         item.alert_P = {width:(alert * 100)/max+"%"};
    //     }
    //     rootScope.$apply();
    // }

    var updateOSChart = function(data){
        var osTypes = [];
        var countAll = [];
        var countAlert = [];
        var countUnalert = [];
        for (var i in data) {
            var d = data[i];
            if(d.type == 'all'){
                osTypes.push(d.OSTypeShort);
                countAll.push(parseInt(d.count));
                countAlert.push(0);
                countUnalert.push(parseInt(d.count));
            }else{
                var index = osTypes.indexOf(d.OSTypeShort);


                countAlert[index] = parseInt(d.count);
                countUnalert[index] = countAll[index] - countAlert[index];
            }
        }
        $('#OSData').highcharts({
            credits: {
                enabled: false
            },
            chart: {
                type: 'bar'
            },
            title: {
                text: '操作系统分类(3个月内)'
            },
            xAxis: {
                categories: osTypes
            },
            yAxis: {
                minTickInterval: 1,
                min: 0,
                title: {
                    text: '计算机数量'
                }
            },
            legend: {
                reversed: true
            },
            plotOptions: {
                bar:{
                    maxPointWidth:20
                },
                series: {
                    stacking: 'normal'
                }
            },
            series: [{
                color:"#dd4b39",
                name: '告警计算机',
                data: countAlert
            }, {
                color:"#00a65a",
                name: '未告警计算机',
                data: countUnalert
            }]
        });
    } 

    var updateGroupChart = function(data){
        $('#GroupData').highcharts({
            credits: {
                enabled: false
            },
            chart: {
                type: 'bar'
            },
            title: {
                text: '计算机分组(3个月内)'
            },
            xAxis: {
                categories: data.textList
            },
            yAxis: {
                minTickInterval: 1,
                min: 0,
                title: {
                    text: '计算机数量'
                }
            },
            legend: {
                reversed: true
            },
            plotOptions: {
                bar:{
                    maxPointWidth:20
                },
                series: {
                    stacking: 'normal'
                }
            },
            series: [{
                color:"#dd4b39",
                name: '告警计算机',
                data: data.countAlert
            }, {
                color:"#00a65a",
                name: '未告警计算机',
                data: data.countUnalert
            }]
        });
    } 
    $.ajax({
        dataType: 'json',
        url: '/chart',
        success:function(rsp)
        {
            if(rsp.status == 'success')
            {
                updateSensorChart(rsp.Sensor);
                updateSensorTopChart(rsp.SensorTop);
                updateFileChart(rsp.File);
                updateFileTopChart(rsp.FileTop);
                updateOSChart(rsp.OSData);
            }
        },complete:function(rsp)
        {
        }
    });
    
    

    
    


    var treeDom;
    var GroupTree = [];
    var Groups = {};

    function updateTree(){
        for (var i = 0; i < GroupList.length; i++) {
            var group = GroupList[i];
            Groups[group.id] = group;
            group.type = ''+group.type;
            if(group.pid == 0)
            {
                GroupTree.push(group);
            }else{
                if(!Groups[group.pid].nodes){
                    Groups[group.pid].nodes = [];
                }
                Groups[group.pid].nodes.push(group);
            }
        }
        treeDom = $('#groupTree').treeview({
            color: "#428bca",
            data: GroupTree,
            showIcon: false,
            showCheckbox: true,
            onNodeSelected: function(event, node) {
            },
            onNodeUnselected: function (event, node) {
            },
            onNodeChecked: function(event, node) {
            },
            onNodeUnchecked: function (event, node) {
            }
        });

      treeDom.treeview('checkAll', { silent: true });
      getGroupChart(getCheckedIDList());
      // var nodes = treeDom.treeview('getUnselected');
      // Nodes = {};
      // for (var i = nodes.length - 1; i >= 0; i--) {
      //   var node = nodes[i];
      //   Nodes[node.id] = node;
      // }
      // console.log(Nodes)
    }
    updateTree();

    function getCheckedIDList(){
        var nodeList = treeDom.treeview('getChecked');
        var idList = [];
        for (var i = nodeList.length - 1; i >= 0; i--) {
            var node = nodeList[i];
            idList.push(node.id);
        }
        return idList;
    }
    
    function getGroupChart(groupIDList){
        $.ajax({
            dataType: 'json',
            url: '/chart/group',
            type:'post',
            data:JSON.stringify(groupIDList),
            success:function(rsp)
            {
                if(rsp.status == 'success')
                {
                    updateGroupChart(rsp.GroupData);
                }
            },complete:function(rsp)
            {
            }
        });
    }
    changeGroup = function(){
        var W = 480;
        var H = 360;
        zeroModal.show({
            title: '请选计算机分组！',
            content: groupTree,
            width: W+"px",
            height: H+"px",
            ok: true,
            cancel: true,
            okFn: function() {
                var idList = getCheckedIDList();
                if(!idList || idList.length == 0){
                    zeroModal.error('请选计算机分组！');
                    return false;
                }
                getGroupChart(idList);
            },
            onCleanup: function() {
                hide_box.appendChild(groupTree);
            }
        });
    }
}

var changeGroup;
// var rootScope = null;
// var myApp = angular.module('myApp', []);
// myApp.controller('myCtrl', function($scope, $http,$filter) {
//     rootScope = $scope;
// });

