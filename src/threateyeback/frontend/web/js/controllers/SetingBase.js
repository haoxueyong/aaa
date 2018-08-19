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
}

