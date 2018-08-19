var myApp = angular.module('myApp', []);
myApp.controller('UserLogCtrl', function($scope, $http,$filter) {
    $scope.rqs_data = {
        username:'',
        StartTime:null,
        EndTime:null
    };
    $scope.search_data = {
        username:'',
        StartTime:null,
        EndTime:null
    };
    $scope.search = function(){
        $scope.rqs_data['username'] = $scope.search_data['username'];
        $scope.rqs_data['StartTime'] = $scope.search_data['StartTime'];
        $scope.rqs_data['EndTime'] = $scope.search_data['EndTime'];
        $scope.getPage();
    }
    $scope.getPage = function(pageNow)
    {
        
        pageNow = pageNow ? pageNow : 1;
        $scope.rqs_data['page'] = pageNow;
        $http.post('/userlog/page',$scope.rqs_data).then(function success(rsp){
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
            }
        },function err(rsp){
        });
    }
    $scope.getPage();

    window.onload = function(){
        $('#reservationtime').daterangepicker(
        {
            maxDate:moment(),
            minDate:moment().subtract(90, 'days'),
            timePickerIncrement: 10,
            startDate: moment().subtract(7, 'days'),
            endDate: moment(),
            locale : {  
                applyLabel : '确定',  
                cancelLabel : '取消',
                format: 'YYYY-MM-DD'
            }
        },function(start, end, label) {
            $scope.search_data.StartTime = start.unix();
            $scope.search_data.EndTime = end.unix();
        });
    }
});




