var app = angular.module('myApp', []);
var rootScope;
app.controller('myCtrl', function($scope, $http,$filter) {
  rootScope = $scope;
  $scope.share = share;
  $scope.changed = false;
  $scope.$watch('share',function(newValue,oldValue, scope){
    for (var i = share_old.data.length - 1; i >= 0; i--) {
      var s = share_old.data[i];
      var t = newValue.data[i];
      if(s.threat != t.threat || s.confidence != t.confidence){
        $scope.changed = true;
        return;
      }
    }
    $scope.changed = false;
  },true);

  $scope.save = function(){
    var loading = null;
    
    function submit(){
      if(postStatusList['IPv4'] == 1 || postStatusList['domain'] == 1 || postStatusList['URL'] == 1 || postStatusList['md5'] == 1){
        return;
      }
      if(postStatusList['IPv4'] == 3 || postStatusList['domain'] == 3 || postStatusList['URL'] == 3 || postStatusList['md5'] == 3){
        zeroModal.close(loading);
        zeroModal.error('保存失败！');
        return;
      }
      $http.post('/share/update',$scope.share).then(function success(rsp){
        if(rsp.data.status == 'success'){
          share_old.data = angular.copy(share.data);
          $scope.changed = false;
          zeroModal.close(loading);
          zeroModal.success('保存成功！');
        }else{
          zeroModal.close(loading);
          zeroModal.error('保存失败！');
        }
      },function err(rsp){
        zeroModal.close(loading);
        zeroModal.error('保存失败！');
      });
    }
    var postDatas = {
      IPv4:[],
      domain:[],
      URL:[],
      md5:[]
    };
    var postStatusList = {
      IPv4:0,
      domain:0,
      URL:0,
      md5:0
    };
    var sourceNames = {
      IPv4:'BlackListIPv4_indicators',
      domain:'BlackListDomain_indicators',
      URL:'BlackListURL_indicators',
      md5:'BlackListMD5_indicators'
    }
    var getType = {
      BlackListIPv4_indicators:'IPv4',
      BlackListDomain_indicators:'domain',
      BlackListURL_indicators:'URL',
      BlackListMD5_indicators:'md5'
    }

    for (var i = $scope.share.data.length - 1; i >= 0; i--) {
      var item = $scope.share.data[i];
      postDatas[item.type].push(item);
    }
    
    for (var type in postDatas) {
      var postData = postDatas[type];
      if(postData.length > 0){
        postStatusList[type] = 1;
        if(loading == null){
          loading = zeroModal.loading(4);
        }
        $http.post('/proxy/cyberhunt/config/data/' + sourceNames[type] + '/append?t=yaml',postData).then(function success(rsp){
          var sourceName = rsp.config.url.match(/BlackList\S*indicators/)[0];
          var Type = getType[sourceName];
          if(rsp.data.result == 'ok'){
            postStatusList[Type] = 2;
          }else{
            postStatusList[Type] = 3;
          }
          submit();
        },function err(rsp){
          postStatusList[Type] = 3;
          submit();
        });
      }
    }
  }
  //comment
  $('#comment').autoTextarea({});
  $scope.newComment = {
    sid:$scope.share.id,
    content:''
  }
  $scope.commentList = [];
  $scope.commentObj = {};
  $scope.commentCount = 0;
  $scope.addComment = function() {
    $scope.newComment.content = $scope.newComment.content.trim();
    if($scope.newComment.content == ''){
      return;
    }
    var loading = zeroModal.loading(4);
    $http.post('/share/add-comment',$scope.newComment).then(function success(rsp){
      if(rsp.data.status == 'success'){
        if($scope.commentCount == 0 || $scope.commentCount == Object.keys($scope.commentObj).length){
          $scope.pushComment(rsp.data.comment);
        }
        $scope.commentCount++;
        $scope.newComment.content = '';
        $('#comment').autoTextarea('update');
      }else{
        zeroModal.error('发送失败！');
      }
      zeroModal.close(loading);
    },function err(rsp){
      zeroModal.close(loading);
      zeroModal.error('发送失败！');
    });
  }

  $scope.pushComment = function(comment){
    if(!$scope.commentObj[comment.id]){
      comment.timeString = moment(comment.created_at,'X').fromNow();
      $scope.commentList.push(comment);
      $scope.commentObj[comment.id] = comment;
      $scope.timeLabel($scope.commentList.length - 1);
    }
  }

  $scope.timeLabelList = [];
  $scope.timeLabelColorList = ['bg-red','bg-green','bg-yellow','bg-blue'];
  $scope.timeLabel = function(index){
    var comment = $scope.commentList[index];
    if(comment.type == 'label'){
      if($scope.commentList[index+1]){
        comment.timeString = moment($scope.commentList[index+1].created_at,'X').format('YYYY-MM-DD');
      }else{
        var labelIndex = $scope.timeLabelList.indexOf(comment.timeString);
        $scope.timeLabelList.splice(labelIndex,1);
        $scope.commentList.splice(index,1);
      }
      return;
    }
    comment.date = moment(comment.created_at,'X').format('YYYY-MM-DD');
    if(index == 0 || ($scope.commentList[index-1].type != 'label' && moment($scope.commentList[index-1].created_at,'X').format('YYYY-MM-DD') != comment.date)){
      $scope.timeLabelList.push(comment.date);
      var label = {
        timeString:comment.date,
        type:'label',
        color:$scope.timeLabelColorList[($scope.timeLabelList.length-1)%4]
      };
      $scope.commentList.splice(index,0,label);
      return;
    }
  }

  $scope.delComment = function(item) {
    zeroModal.confirm({
      content: '确定删除这条评论吗？',
      okFn: function() {
        var postData = {
          id:item.id,
          sid:item.sid
        };
        $http.post('/share/del-comment',postData).then(function success(rsp){
          if(rsp.data.status == 'success'){
            $scope.commentCount = rsp.data.count;
            delete $scope.commentObj[postData.id];
            for (var i = $scope.commentList.length - 1; i >= 0; i--) {
              var comment = $scope.commentList[i];
              if(comment.id == postData.id){
                $scope.commentList.splice(i,1);
                $scope.timeLabel(i - 1);
              }
            }
          }
        },function err(rsp){
        });
      },
      cancelFn: function() {
      }
    });
  }
  $scope.getComment = function() {
    var postData = {
      sid:$scope.share.id,
      offset:Object.keys($scope.commentObj).length
    };
    if($scope.commentCount != 0 && $scope.commentCount <= postData.offset){
      return;
    }
    $http.post('/share/get-comment',postData).then(function success(rsp){
      if(rsp.data.status == 'success'){
        $scope.commentCount = rsp.data.count;
        for (var i = 0; i < rsp.data.data.length; i++) {
          var item = rsp.data.data[i];
          $scope.pushComment(item);
        }
      }
    },function err(rsp){
    });
  }

  $scope.getComment();
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
      $scope.getComment();
    }
    nowTop = top;
  });
});