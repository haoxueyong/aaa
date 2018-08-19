var myApp = angular.module('myApp', []);
myApp.controller('UserCtrl', function($scope, $http,$filter,$httpParamSerializerJQLike) {
    $scope.pages = {
        data : [],
        count : 0,
        maxPage : "...",
        pageNow : 1,
    };
    $scope.getPage = function(pageNow)
    {
        pageNow = pageNow ? pageNow : 1;
        $http.post('/user/page',{page:pageNow}).then(function success(rsp){
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
            }
        },function err(rsp){
        });
    }
    $scope.getPage();

    $scope.sendUser = function(id,$event){
        rqs_data = {
            username : $scope.newUser.username,
            password : $scope.newUser.password,
            role : $scope.newUser.role,
            page:$scope.pages.pageNow
        };
        var loading = zeroModal.loading(4);
        $http.post("/user/add",rqs_data).then(function success(rsp){
            zeroModal.close(loading);
            if(rsp.data.status == 'success')
            {
                $scope.pages = rsp.data;
            }else if(rsp.data.errorCode == 1){
                zeroModal.error({
                    content: '用户添加失败',
                    contentDetail: '此用户名已经存在！'
                });
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    }

    $scope.del = function(id){
        zeroModal.confirm({
            content: '操作提示',
            contentDetail: '是否确认删除吗？',
            okFn: function() {
                rqs_data = {
                    id : id,
                    page:$scope.pages.pageNow
                };
                var loading = zeroModal.loading(4);
                $http.post("/user/del",rqs_data).then(function success(rsp){
                    zeroModal.close(loading);
                    if(rsp.data.status == 'success')
                    {
                        $scope.pages = rsp.data;
                    }
                },function err(rsp){
                    zeroModal.close(loading);
                });
            },
            cancelFn: function() {
            }
        });
        
    }

    $scope.resetPassword = function(user){
        var loading = zeroModal.loading(4);
        $http.get("/user/get-password-reset-token?id="+user.id).then(function success(rsp){
            zeroModal.close(loading);
            if(rsp.data.status == 'success')
            {
                var W = 540;
                var H = W*3/4;
                zeroModal.show({
                    title: '重置['+user.username+']的密码',
                    content: resetPassword,
                    width: W+"px",
                    height: H+"px",
                    ok: true,
                    cancel: true,
                    okFn: function() {
                        var flag = true;
                        var password = $scope.resetUser.password;
                        var pattern = /(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^a-zA-Z0-9]).{8,30}/;
                        if(!pattern.test(password))
                        {
                            flag = false;
                            $scope.resetUser_passworderror = true;
                        }else{
                            $scope.resetUser_passworderror = false;
                        }
                        if(password != $scope.resetUser.repassword)
                        {
                            flag = false;
                            $scope.resetUser_repassworderror = true;
                        }else{
                            $scope.resetUser_repassworderror = false;
                        }
                        $scope.$apply();
                        if(!flag)
                        {
                            return false;
                        }
                        var post_data = {
                            'ResetPasswordForm':{
                                'password':password
                            }
                        };
                        var formData = {
                            method: 'POST',
                            url: '/user/reset-password?token='+rsp.data.token,post_data,
                            data:post_data
                        }
                        loading = zeroModal.loading(4);
                        $http({
                            method : formData.method,
                            url :formData.url,
                            data : $httpParamSerializerJQLike(formData.data),
                            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
                        }).then(function success(rsp){
                            if(rsp.data.status == 'success'){
                                zeroModal.success('密码重置成功！');
                            }else{
                                zeroModal.error('密码重置失败！');
                            }
                            zeroModal.close(loading);
                        },function err(rsp){
                            zeroModal.close(loading);
                        });
                    },
                    onCleanup: function() {
                        hideenBox.appendChild(resetPassword);
                    }
                });
            }
        },function err(rsp){
            zeroModal.close(loading);
        });
    }

    $scope.add = function(){
        var W = 540;
        var H = 480;
        var box = null;
        $scope.newUser={
            username:'',
            password:'',
            role:'user'
        }


        box = zeroModal.show({
            title: '添加用户',
            content: newUser,
            width: W+"px",
            height: H+"px",
            ok: true,
            cancel: true,
            okFn: function() {
                var username = $scope.newUser.username;
                var flag = true;

                if(username == null || username.length==0 ||!/^[a-z0-9_-]{2,16}$/.test(username))
                {
                    flag = false;
                    $scope.nameerror = true;
                }else{
                    $scope.nameerror = false;
                }
                var password = $scope.newUser.password;
                var pattern = /(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^a-zA-Z0-9]).{8,30}/;
                if(!pattern.test(password))
                {
                    flag = false;
                    $scope.passworderror = true;
                }else{
                    $scope.passworderror = false;
                }
                if(password != $scope.newUser.repassword)
                {
                    flag = false;
                    $scope.repassworderror = true;
                }else{
                    $scope.repassworderror = false;
                }

                $scope.$apply();
                if(!flag)
                {
                    return false;
                }
                $scope.sendUser();
            },
            onCleanup: function() {
                hideenBox.appendChild(newUser);
            }
        });
    }

});




