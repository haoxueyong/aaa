var myApp = angular.module('myApp', []);
myApp.controller('PrototypeCtrl', function ($scope, $http, $filter) {
    //已处理
    var loading = null;
    $scope.init = function () {
        $scope.bgList = ['bg-primary', 'bg-aqua', 'bg-green', 'bg-yellow', 'bg-red', 'bg-teal', 'bg-purple'];
        if (loading == null) {
            loading = zeroModal.loading(4);
        }
        $scope.nodeList = null;
        $scope.prototypeData = null;
        $scope.info = null;
        $scope.getPrototypes();
        $scope.getNodes();
        $scope.getNum();
    };

    $scope.getPrototypes = function () {
        $http.get('/proxy/cyberhunt/prototype?f=local').then(function success(rsp) {
            if (rsp.data.result) {
                $scope.prototypeData = rsp.data.result;
                $scope.setData();
            } else {
                $scope.apiErr(rsp);
            }
        }, function err(rsp) {
            $scope.apiErr(rsp);
        });
    }

    $scope.getNodes = function () {
        $http.get('/proxy/cyberhunt/config/full').then(function success(rsp) {
            // console.log( rsp.data.result);
            
            if (rsp.data.result) {
                $scope.info = rsp.data.result;
                $scope.nodeList = $scope.info.nodes;
                $scope.setData();
            } else {
                $scope.apiErr(rsp);
            }
        }, function err(rsp) {
            $scope.apiErr(rsp);
        });
    }
    $scope.getNum = function () {
        $http.get('/proxy/cyberhunt/status/cyberhunt').then(function success(rsp) {
          $scope.getNumData = rsp.data;

        }, function err(rsp) {
         
        });

    }
    $scope.setData = function () {
        if ($scope.nodeList == null || $scope.prototypeData == null || !$scope.info == null) {
            return;
        }
        $scope.prototypes = {};
        $scope.prototypeList = {};
        for (var orgName in $scope.prototypeData) {
            var prototypes = $scope.prototypeData[orgName].prototypes;
            for (var name in prototypes) {
                var item = prototypes[name];
                $scope.prototypeList[orgName + '.' + name] = item;
                if (item.config.attributes && item.node_type == 'miner') {
                    if (!$scope.prototypes[item.config.attributes.share_level]) {
                        $scope.prototypes[item.config.attributes.share_level] = {};
                    }
                    item.orgName = orgName;
                    item.name = name;
                    item.node = null;
                    $scope.prototypes[item.config.attributes.share_level][name] = item;

                    if ($scope.nowPrototype &&
                        $scope.nowPrototype.orgName == item.orgName &&
                        $scope.nowPrototype.name == item.name
                    ) {
                        $scope.nowPrototype = $scope.prototypes[item.config.attributes.share_level][name];
                        $scope.copyNowPrototype();
                    }
                }
            }
        }
        // console.log($scope.nodeList);
        
        for (var id = $scope.nodeList.length - 1; id >= 0; id--) {
            var node = $scope.nodeList[id];
            if (!node) {
                continue;
            }
            node.id = id;
            if (node.properties.output == true && node.properties.inputs.length == 0) {
                // console.log( node.properties.prototype);
                // console.log( $scope.prototypeList);
                $scope.prototypeList[node.properties.prototype].node = node;
            }
        }
        zeroModal.close(loading);
        loading = null;
        // console.log($scope.prototypes.green);
        
        $http.get('/proxy/cyberhunt/status/cyberhunt').then(function success(rsp) {
            $scope.getNumData = rsp.data;
            // console.log( $scope.getNumData);
            // console.log($scope.getNumData.result);
            
            angular.forEach($scope.getNumData.result,function (item,index) {

                for(var k in $scope.prototypes.red){
                    if($scope.prototypes.red[k].node != undefined && $scope.prototypes.red[k].node){
                        if(item.name == $scope.prototypes.red[k].node.name){
                            $scope.prototypes.red[k].lengthNum = item.length;
                        }
                    }else{
                            $scope.prototypes.red[k].lengthNum = 0;
                    }
                }

                for(var k in $scope.prototypes.green){
                    if($scope.prototypes.green[k].node != undefined && $scope.prototypes.green[k].node){
                        if(item.name == $scope.prototypes.green[k].node.name){
                            // console.log(item.length);
                            $scope.prototypes.green[k].lengthNum = item.length;
                        }
                    }else{
                            $scope.prototypes.green[k].lengthNum = 0;
                    }


                }
            });
  
          
            
          }, function err(rsp) {
           
          });
      

        
    };

    $scope.changed = false;
    $scope.$watch('nowPrototype', function (newValue, oldValue, scope) {
        if ($scope.nowPrototype_old) {
            var config_old = $scope.nowPrototype_old.config;
            var config = newValue.config;
            $scope.changed = !(
                config_old.interval == config.interval &&
                config_old.attributes.confidence == config.attributes.confidence &&
                config_old.attributes.threat == config.attributes.threat
            );
        }
    }, true);
    $scope.changeConfigData = function (type, file) {
        var formData = new FormData();
        formData.append('file', file);
        $http({
            method: 'POST',
            url: '/proxy/cyberhunt/config/data/' + $scope.nowPrototype.orgName + '?t=' + type,
            data: formData,
            headers: {
                'Content-Type': undefined
            }
        }).then(function success(rsp) {
            if (rsp.data.result == 'ok') {
                zeroModal.success('私钥导入成功！');
            } else if (rsp.data.result.issuer) {
                var begin = moment(rsp.data.result.begin).format('YYYY-MM-DD');
                var end = moment(rsp.data.result.end).format('YYYY-MM-DD');
                zeroModal.success({
                    content: '证书导入成功！',
                    contentDetail: rsp.data.result.subject + '<br>' +
                        '<div style="margin: 5px 0 0 50px;">' +
                        '<span class="pull-left">有效时间：' + begin + '到' + end + '</span><br>' +
                        '<span class="pull-left">发行机构：' + rsp.data.result.issuer + '</span>' +
                        '</div>'
                });
            }
        }, function err(rsp) {
            zeroModal.error('此文件无法导入！');
        });
    }
    $('#inputFile_cert').change(function () {
        var file = this.files[0];
        if ((/.*\.cert$/).test(file.name.toLowerCase())) {
            $scope.changeConfigData('cert', file);
        } else {
            zeroModal.error('此文件无法导入！');
        }
    });
    $('#inputFile_pkey').change(function () {
        var file = this.files[0];
        if ((/.*\.key$/).test(file.name.toLowerCase())) {
            $scope.changeConfigData('pkey', file);
        } else {
            zeroModal.error('此文件无法导入！');
        }
    });
    $scope.detail = function (item) {
        oldTop = nowTop;
        $scope.nowPrototype = item;
        if (typeof $scope.nowPrototype.config.interval == 'undefined') {
            $scope.nowPrototype.config.interval = null;
        }
        if (typeof $scope.nowPrototype.config.attributes.threat == 'undefined') {
            $scope.nowPrototype.config.attributes.threat = null;
        }
        $scope.copyNowPrototype();
    }
    $scope.copyNowPrototype = function () {

        $scope.changed = false;
        $scope.nowPrototype_old = angular.copy($scope.nowPrototype);
    }
    $scope.backList = function (item) {
        $scope.nowPrototype = null;
        $scope.nowPrototype_old = null;
        setTimeout(function () {
            $('.content-wrapper').scrollTop(oldTop);
        }, 5);
    }
    $scope.save = function () {
        var item = $scope.nowPrototype;
        if (loading == null) {
            loading = zeroModal.loading(4);
        }
        $http.put('/proxy/cyberhunt/prototype/' + item.orgName + '.' + item.name + '?t=json', item).then(function success(rsp) {
            if (rsp.data.result) {
                $scope.init();
            } else {
                $scope.apiErr(rsp);
            }
        }, function err(rsp) {
            $scope.apiErr(rsp);
        });
    }
    $scope.delNode = function (item) {
        
        var nodeNames = [];
        for (var i = $scope.nodeList.length - 1; i >= 0; i--) {
            var node = $scope.nodeList[i];
            var index = node.properties.inputs.indexOf(item.node.name);
            if (index > -1) {
                nodeNames.push(node.name);
            }
        }
        $http.delete('/proxy/cyberhunt/config/node/' + item.node.id + '?r=1&version=' + item.node.version).then(function success(rsp) {
            // zeroModal.close(loading);
            // console.log(rsp.data.result.nodes);
            
            if (rsp.data.result) {
                $scope.info = rsp.data.result;
                $scope.nodeList = $scope.info.nodes;
                $scope.setData();
            } else {
                $scope.apiErr(rsp);
            }
        }, function err(rsp) {
            $scope.setData();
            location.reload();// 临时刷新页面
            $scope.apiErr(rsp);
        });
   
            
       
    }
    $scope.addNode = function (item) {
        var node = {
            name: item.orgName + '_' + item.name,
            properties: {
                inputs: [],
                output: true,
                prototype: item.orgName + '.' + item.name
            },
            version: $scope.info.version
        };
        $http.post('/proxy/cyberhunt/config/node?r=1', node).then(function success(rsp) {
            // console.log(7777);
            // console.log(rsp);
            if (rsp.data.result) {
                $scope.info = rsp.data.result;
                $scope.nodeList = $scope.info.nodes;
                $scope.setData();
            } else {
                $scope.apiErr(rsp);
            }
        }, function err(rsp) {
            // console.log(1212);
            $scope.setData();
            location.reload();// 临时刷新页面
            $scope.apiErr(rsp);
        });
    }
    $scope.changeStatus = function (item) {
        if (loading == null) {
            loading = zeroModal.loading(4);
        }
        if (item.node) {
            // console.log('333');
            $scope.delNode(item);
        } else {
            // console.log('444');
            $scope.addNode(item);
        }
    }
    $scope.init();

    var nowTop = 0;
    var oldTop = 0;
    $('.content-wrapper').scroll(function () {
        nowTop = $(this).scrollTop();
    });

    $scope.apiErr = function (rsp) {
        console.log(rsp);
    }

});