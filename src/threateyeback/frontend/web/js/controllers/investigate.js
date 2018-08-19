var myScope;
function init($scope, $http,$filter,timerangeDom){
    $scope.sendCommand = function(command_data){
        console.log(command_data);
        var loading = zeroModal.loading(4);
        $http.post('send-command',command_data).then(function success(rsp){
            console.log(rsp.data);
            zeroModal.close(loading);
            if(rsp.data.status == 'success')
            {
                var post_data = {
                    CommandID:rsp.data.CommandID,
                    Type:command_data.Type
                };
                $scope.getResult(post_data,true);
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    };

    var loading_getResult = null;
    $scope.getResult = function(post_data,init){
        if(init){
            $scope.getResultTimeOut = moment().add(1,'minute').unix();
        }
        if($scope.getResultTimeOut < moment().unix()){
            zeroModal.error("调查超时");
            zeroModal.close(loading_getResult);
            loading_getResult = null;
            return;
        }
        console.log(post_data);
        if(loading_getResult == null){
            loading_getResult = zeroModal.loading(4);
        }
        $http.post('get-result',post_data).then(function success(rsp){
            if(rsp.data.status == 'success')
            {
                zeroModal.close(loading_getResult);
                loading_getResult = null;
                $scope.analyResult[post_data.Type](rsp.data.Result);
            }else{
                console.log(rsp.data);
                setTimeout(function(){
                    $scope.getResult(post_data);
                },3000);
            }
        },function err(rsp){
            zeroModal.close(loading);
            loading_getResult = null;
        });
    };

    $scope.analyResult = {
        Computer:function(data){
            updateTable(data['UserLogon'],'UserLogon');
            updateTable(data['NetProcess'],'NetProcess');
            updateTable(data['UsbPlug'],'UsbPlug');
            updateTable(data['ConnectedComputer'],'ConnectedComputer');
        },
        File:function(data){
            myScope.nowFile = data;
            updateTable(data['FileComputer'],'FileComputer');
        },
        FileTransfer:function(data){
            updateTable(data['IMProcess'],'IMProcess');
        },
        Signer:function(data){
            updateTable(data['SignerFile'],'SignerFile');
        },
        Domain:function(data){
            updateTable(data['NetProcess'],'DomainProcess');
        },
        User:function(data){
            updateTable(data['LogonEvent'],'LogonEvent');
        }
    };

    timerangeDom.daterangepicker(
    {
        maxDate:moment(),
        minDate:moment().subtract(90, 'days'),
        timePicker: true,
        timePickerIncrement: 10,
        startDate: moment().subtract(24,'hours'),
        endDate: moment(),
        locale : {  
            applyLabel : '确定',  
            cancelLabel : '取消',
            format: 'YYYY-MM-DD HH:mm',
            customRangeLabel:'指定时间范围'
        },
        ranges : {
            '24小时内': [moment().subtract(24,'hours'), moment()],
            '一周内': [moment().startOf('weeks'), moment()],
            '一个月内': [moment().startOf('months'), moment()]
        }
    },function(start, end, label) {
        // start = start.subtract(1, 'days').add(1, 'days');
        $scope.search_data.StartTime = start.unix();
        $scope.search_data.EndTime = end.unix();
        console.log(moment($scope.search_data.StartTime,'X').format('YYYY-MM-DD HH:mm:ss'));
        console.log(moment($scope.search_data.EndTime,'X').format('YYYY-MM-DD HH:mm:ss'));
    });
}


var myApp = angular.module('myApp', []);

/**
 * My controller
 */
myApp.controller('myCtrl', function($scope, $http,$filter) {
    $scope.showTable = "Computer";
    myScope = $scope;
    $scope.status_str = [
        {
            css:'danger',
            label:'卸载'
        },{
            css:'success',
            label:'在线'
        },{
            css:'warning',
            label:'断线'
        }
    ];
});


/**
 * Computer controller
 */
myApp.controller('ComputerCtrl', function($scope, $http,$filter) {
    $scope.search_data = {
        ComputerName:'',
        IP:'',
        StartTime:moment('00:00:00','HH:mm:ss').unix(),
        EndTime:moment().unix()
    }
    
    myScope.select = function(item){
        myScope.selectSensor = item;
    }

    $scope.search = function(){
        var loading = zeroModal.loading(4);
        $http.post('get-sensor',$scope.search_data).then(function success(rsp){
            if(rsp.data.status == 'success')
            {
                zeroModal.close(loading);
                if(rsp.data.data.length == 0){
                    zeroModal.error("未找到计算机！");
                    return;
                }
                myScope.sensorList = rsp.data.data;
                var W = 800;
                var H = 480;
                zeroModal.show({
                    title: '请选择计算机！',
                    content: sensorList,
                    width: W+"px",
                    height: H+"px",
                    ok: true,
                    cancel: true,
                    okFn: function() {
                        myScope.nowSensor = myScope.selectSensor;
                        var command_data = {
                            Type:"Computer",
                            SensorID:myScope.nowSensor.SensorID,
                            StartTime:$scope.search_data.StartTime,
                            EndTime:$scope.search_data.EndTime
                        }
                        $scope.sendCommand(command_data);
                    },
                    cancelFn:function(){
                        myScope.$apply();
                    },
                    onCleanup: function() {
                        hide_box.appendChild(sensorList);
                    }
                });
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    }
    init($scope, $http,$filter,$('#computer .timerange'));
});
/**
 * File controller
 */
myApp.controller('FileCtrl', function($scope, $http,$filter) {
    $scope.search_data = {
        Type:"File",
        FileName:"",
        MD5OrSHA256:"",
        CommandLine:"",
        ComputerName:"",
        IgnorePath:"",
        IgnoreParentName:"",
        StartTime:moment('00:00:00','HH:mm:ss').unix(),
        EndTime:moment().unix()
    }
    $scope.search = function(){
        $scope.sendCommand($scope.search_data);
    }
    init($scope, $http,$filter,$('#file .timerange'));
});
/**
 * FileTransfer controller
 */
myApp.controller('FileTransferCtrl', function($scope, $http,$filter) {
    $scope.search_data = {
        Type:"FileTransfer",
        ProcessName:"",
        ComputerName:"",
        StartTime:moment('00:00:00','HH:mm:ss').unix(),
        EndTime:moment().unix()
    }
    $scope.search = function(){
        $scope.sendCommand($scope.search_data);
    }
    init($scope, $http,$filter,$('#fileTransfer .timerange'));
});
/**
 * Signer controller
 */
myApp.controller('SignerCtrl', function($scope, $http,$filter) {
    $scope.search_data = {
        Type:"Signer",
        Signer:"",
        StartTime:moment('00:00:00','HH:mm:ss').unix(),
        EndTime:moment().unix()
    }
    $scope.search = function(){
        $scope.sendCommand($scope.search_data);
    }
    init($scope, $http,$filter,$('#signer .timerange'));
});
/**
 * Domain controller
 */
myApp.controller('DomainCtrl', function($scope, $http,$filter) {
    $scope.search_data = {
        Type:"Domain",
        URL:"",
        IP:"",
        Port:null,
        IgnoreProcessName:"",
        StartTime:moment('00:00:00','HH:mm:ss').unix(),
        EndTime:moment().unix()
    }
    $scope.search = function(){
        $scope.sendCommand($scope.search_data);
    }
    init($scope, $http,$filter,$('#domain .timerange'));
});

/**
 * User controller
 */
myApp.controller('UserCtrl', function($scope, $http,$filter) {
    $scope.search_data = {
        Type:"User",
        Domain:"",
        UserName:"",
        SID:"",
        StartTime:moment('00:00:00','HH:mm:ss').unix(),
        EndTime:moment().unix()
    }
    $scope.search = function(){
        $scope.sendCommand($scope.search_data);
    }
    init($scope, $http,$filter,$('#user .timerange'));
});













