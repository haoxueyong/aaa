var myApp = angular.module('myApp', []);
var rootScope;
myApp.controller('shareCtrl', function($scope, $http,$filter) {
    rootScope = $scope;
    $scope.pages = {
        data : [],
        count : 0,
        maxPage : "...",
        pageNow : 1,
    };
    $scope.IDList = [];
    $scope.ItemList = {};
    $scope.wds = [];
    $scope.searchWd = '';

    

    // $scope.getPage = function(pageNow)
    // {
    //     if(!sessionStorage.getItem('sharePage')){
    //         sessionStorage.setItem('sharePage',1);
    //     }
    //     pageNow = pageNow ? pageNow : sessionStorage.getItem('sharePage');
    //     var postData = {
    //         page:pageNow,
    //         wds:$scope.wds
    //     };
    //     $http.post('/share/page',postData).then(function success(rsp){
    //         $scope.setPage(rsp.data);
    //     },function err(rsp){
    //     });
    // }

    // $scope.setPage = function(data)
    // {
    //     $scope.pages = data;
    //     sessionStorage.setItem('sharePage',$scope.pages.pageNow);
    // }

    $scope.del = function(item,index){
        zeroModal.confirm({
            content: '确定删除这个分享吗？',
            okFn: function() {
                var postData = {
                    id:item.id,
                    wds:$scope.wds
                };
                var loading = zeroModal.loading(4);
                $http.post('/share/del',postData).then(function success(rsp){
                    if(rsp.data.status == 'success'){
                        $scope.list.splice(index,1);
                        delete $scope.listObj['' + item.id];
                    }
                    zeroModal.close(loading);
                },function err(rsp){
                  zeroModal.close(loading);
                });
            },
            cancelFn: function() {
            }
        });
    }


    var like_lock = false;
    $scope.like = function(item){
        if(like_lock){
            return;
        }
        var postData = {
            id:item.id,
            liked:(item.liked ? 0 : 1)
        };
        like_lock = true;
        $http.post('/share/like',postData).then(function success(rsp){
            if(rsp.data.status = 'success'){
                item.liked = rsp.data.data.liked;
                item.lq = rsp.data.data.lq;
            }
            like_lock = false;
        },function err(rsp){
            like_lock = false;
        });
    }

    $scope.detail = function(item){
        window.location.href = '/share/'+item.id;
    }

    $scope.showLength = function(str,length){
        if(!length){
            length = 30;
        }
        return str.substr(0,length)+'...';
    }
    $scope.search = function(){
        $scope.onload = false;
        $scope.wds = $scope.searchWd.split(/\s+/);
        $scope.init();
        $scope.getList();
    }
    $scope.myKeyup = function(e){
        var keycode = window.event?e.keyCode:e.which;
        if(keycode==13){
            e.target.blur();
            $scope.search();
        }
    };
    

    $scope.init = function(){
        $scope.listObj = {};
        $scope.list = [];
        $scope.listCount = 0;
    }
    
    $scope.push2list = function(item){
        if(!$scope.listObj[item.id]){
            item.timeString = moment(item.created_at,'X').fromNow();
            $scope.list.push(item);
            $scope.listObj[item.id] = item;
        }
    }

    $scope.getList = function() {
        var postData = {
            wds:$scope.wds,
            offSet:Object.keys($scope.listObj).length
        };
        if($scope.listCount != 0 && $scope.listCount <= postData.offSet){
            return;
        }
        $http.post('/share/list',postData).then(function success(rsp){
            if(rsp.data.status == 'success'){
                $scope.listCount = rsp.data.count;
                for (var i = 0; i < rsp.data.data.length; i++) {
                    var item = rsp.data.data[i];
                    $scope.push2list(item);
                }
                $scope.onload = true;
            }
        },function err(rsp){
        });
    }

    $scope.init();
    $scope.getList();
    var nowTop = 0;
    $('.content-wrapper').scroll(function(){
        var max = -25 - $(this).outerHeight(true);
        $(this).children().each(function(){
            if(this.tagName != 'SCRIPT'){
                max += $(this).outerHeight(true);
            }
        });
        var top = $(this).scrollTop();
        if(top >= max && nowTop < max){
            $scope.getList();
        }
        nowTop = top;
    });


});








