var rootScope;
var myApp = angular.module("myApp", []);
myApp.controller("myCtrl", function ($scope, $http, $filter) {
    rootScope = $scope;
    $scope.init = function () {
        $scope.info = true;
        // $scope._idNum = rootScope.item_id;
        // console.log($scope._idNum);
        $scope.mibsinfo = {};
        $scope.nameUndefined = true;
        $scope.familyUndefined = true;
        $scope.ipUndefined = true;
        $scope.snmpUndefined = true;
        $scope.authUndefined = true;
        $scope.userUndefined = true;
        $scope.authPassUndefined = true;
        $scope.privacyUndefined = true;
        $scope.privacyPassUndefined = true;
        $scope.logPage = true;  // 设备日志
        $scope.nowHost = {
            name: ' ',
            family:' ',
            protocol: {
                snmp: {
                    version: 2,
                    community: " ",
                    port: 161
                },
                host: ' ',
                ipv4: ' '
            }
        };

        $scope.cpu = {
            name: "cpu使用率",
            data: 23.4
        };
        $scope.memory = {
            name: "内存占用率",
            data: 28.4
        };
        $scope.disk = {
            name: "磁盘占用率",
            data: 12.5
        };
        $scope.hostName = "安全设备列表";
        $scope.protocols = null;
        $scope.hosts = null;
        $scope.getProtocol();
        $scope.getHost();
    };
    $scope.inputfocus = function (params) {
        $scope.nameUndefined = true;
        $scope.familyUndefined = true;
        $scope.ipUndefined = true;
        $scope.snmpUndefined = true;
        $scope.authUndefined = true;
        $scope.userUndefined = true;
        $scope.authPassUndefined = true;
        $scope.privacyUndefined = true;
        $scope.privacyPassUndefined = true;
           
    }
    // //获取设备日志
    $scope.getDtata = function (params) {
        $.ajax({
            url: '/devicelog/page',
            method: 'POST',
            data: {
                host: params,
                stime: ((new Date).valueOf() - 86400000 * 30).toString().substring(0, 10),
                etime: (new Date).valueOf().toString().substring(0, 10)
            },
            dataType: 'json',
            success: function (data) {
                // console.log(data);
                // console.log(data.count == 0);
                if(data.count == 0){
                    $scope.logPage = false;
                }else{
                    $scope.logPage = true;
                }
                $scope.devivelogData = data.data;
            }
        })
    }
    // 查看详情
    $scope.lookdetail = function (id, event) {
        event.stopPropagation();
        $scope.$apply(function () {
            $scope.info = false;
            $scope.hostName = "设备详情";
        });
        $scope.cpuEcharts(id);
        $scope.memEcharts(id);
        $scope.diskEcharts(id);
        $scope.getDtata(id); //获取设备日志
        
    }

    // 添加mibs信息
    $scope.saveMibs = function () {
        var dataJson = {
            "family": $scope.nowHost.family,
            "monitor": {
                "memory": $scope.mibsinfo.memory,
                "disk": $scope.mibsinfo.disk,
                "cpu": $scope.mibsinfo.cpu
            }
        }
        var params = JSON.stringify(dataJson);
        $.ajax({
            url: '/host/index-curd/cpe',
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (data) {
                // console.log(data);
            }
        })
        // console.log($scope.mibsinfo);
    }
    // 下载日志压力
     $scope.download = function (_id, event) {
        event.stopPropagation();

        // console.log(_id);
        function download_devicelog(_id, event) {
    
            var tt = new Date().getTime();
            var url = '/devicelog/log-file-download';
        
            /**
             * 使用form表单来发送请求
            * 1.method属性用来设置请求的类型——post还是get
            * 2.action属性用来设置请求路径。
            * 
             */
            var form=$("<form>");//定义一个form表单
            form.attr("style","display:none");
            form.attr("target","");
            form.attr("method","get");  //请求类型
            form.attr("action",url);   //请求地址
            $("body").append(form);//将表单放置在web中
        
            　　/**
                 * input标签主要用来传递请求所需的参数：
                 *
                 * 1.name属性是传递请求所需的参数名.
                 * 2.value属性是传递请求所需的参数值.
                 *
                 * 3.当为get类型时，请求所需的参数用input标签来传递，直接写在URL后面是无效的。
                 * 4.当为post类型时，queryString参数直接写在URL后面，formData参数则用input标签传递
                 *       有多少数据则使用多少input标签
                 *
            　　　*/
            var input1=$("<input>");
            input1.attr("type","hidden");
            input1.attr("name","device_id");
            input1.attr("value",_id);
            form.append(input1);
            form.submit();//表单提交
            
        }
     
        zeroModal.confirm({
            content: "确定下载资产吗？",
            okFn: function () {
                download_devicelog(_id, event);
            },
            cancelFn: function () {}
        });
        
     }
    $scope.del = function (_id, event) {
        // console.log(event);
        event.stopPropagation();
        function doDel() {
            var host = $scope.hosts[_id];
            // console.log(host);
            
            var url_host = "/proxy/iconnect/host/" + host._id;
            var url_protocol = "/proxy/iconnect/protocol/" + host.protocol._id;
            var loading = zeroModal.loading(4);
            // console.log(url_protocol);

            function err(rsp) {
                zeroModal.close(loading);
                zeroModal.error("删除失败!");
            }
            $http.get(url_protocol).then(function success(rsp) {
                // console.log(rsp);
                
                if (rsp.data._status != "ERR") {
                  
                    // 删除protocol
                    $http.delete(url_protocol).then(function success(rsp) {
                        // console.log(rsp);
                          // 删除host
                        $http.delete(url_host).then(function success(rsp) {
                            // console.log(rsp);
                            
                            if (rsp.data._status != "ERR") {
                                zeroModal.close(loading);
                                zeroModal.success("删除成功!");
                                $scope.init();
                            } else {
                                err(rsp);
                            }
                        }, err);
                    }, err);

                } else {
                    err(rsp);
                }
            }, err);
        }
        zeroModal.confirm({
            content: "确定删除资产吗？",
            okFn: function () {
                doDel();
            },
            cancelFn: function () {}
        });
    };
    //显示主机详情
    $scope.details = function (_id) {
        var host = $scope.hosts[_id];
        var url_metric = "/proxy/iconnect/metrics/" + host._id;
        var loading = zeroModal.loading(4);

        function err(rsp) {
            zeroModal.close(loading);
            zeroModal.error("请求超时!");
        }
        $http.get(url_metric).then(function success(rsp) {
            // console.log(123213);
            if (rsp.data._status != "ERR") {
                zeroModal.close(loading);
                //tan kuang
                $scope.nowHost = {
                    name: "",
                    family: "",
                    protocol: {
                        snmp: {
                            version: 2,
                            community: "",
                            port: 161
                        },
                        host: "",
                        ipv4: ""
                    }
                };
                // $scope.showMetricBox();
                //                zeroModal.success('显示成功!');
                $scope.init();
            } else {
                err(rsp);
            }
        }, err);
    };
    $scope.add = function () {
        if( $scope.nowHost.family ){

        }
        $scope.nowHost = {
            name: "",
            family: "",
            protocol: {
                snmp: {
                    version: 2,
                    community: "",
                    port: 161
                },
                host: "",
                ipv4: ""
            }
        };
        $scope.showHostBox();
    };
    $scope.change = function (_id, event) {
        // console.log(event);
        event.stopPropagation();
        $scope.nowHost = angular.copy($scope.hosts[_id]);
        $scope.$apply();
        $scope.showHostBox();
    };



    function isIPv4(ipv4) {
        return /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/.test(
            ipv4
        );
    }

    function verify() {
        var nowHost = $scope.nowHost;

         // 判断
         
        //  console.log(nowHost.protocol.snmp);
         
         if(nowHost.family == ''){
             $scope.$apply(function (params) {
                $scope.familyUndefined = false;
             })
        }
        if(nowHost.name == ''){
            $scope.$apply(function (params) {
                $scope.nameUndefined = false;
             })
        }
        if(nowHost.protocol.ipv4 == ''|| !isIPv4(nowHost.protocol.ipv4)){
            $scope.$apply(function (params) {
                $scope.ipUndefined = false;
             })
        }
        if(nowHost.protocol.snmp.community == ''){
            $scope.$apply(function (params) {
                $scope.snmpUndefined = false;
             })
        }
        // 认证方式
        if(nowHost.protocol.snmp.auth == undefined){
            $scope.$apply(function (params) {
                $scope.authUndefined = false;
             })
        }
        // 安全用户名
        if(nowHost.protocol.snmp.security_user == undefined){
            $scope.$apply(function (params) {
                $scope.userUndefined = false;
             })
        }
        // 认证密码
        if(nowHost.protocol.snmp.auth_pass == undefined){
            $scope.$apply(function (params) {
                $scope.authPassUndefined = false;
             })
        }
        // 加密方式
        if(nowHost.protocol.snmp.privacy == undefined){
            $scope.$apply(function (params) {
                $scope.privacyUndefined = false;
             })
        }
        // 加密密码
        if(nowHost.protocol.snmp.privacy_pass == undefined){
            $scope.$apply(function (params) {
                $scope.privacyPassUndefined = false;
             })
        }

        if (!nowHost.name) {
            return false;
        }
        if (!nowHost.family) {
            return false;
        }
        if (!isIPv4(nowHost.protocol.ipv4)) {
            return false;
        }
        if (!(nowHost.protocol.snmp.port >= 0 && nowHost.protocol.snmp.port <= 65535)) {
            return false;
        }
        if (nowHost.protocol.snmp.version == 3) {
            if (!nowHost.protocol.snmp.auth) {
                return false;
            }
            if (!nowHost.protocol.snmp.privacy) {
                return false;
            }
            if (!nowHost.protocol.snmp.security_user) {
                return false;
            }
            if (!nowHost.protocol.snmp.auth_pass) {
                return false;
            }
            if (!nowHost.protocol.snmp.privacy_pass) {
                return false;
            }
        } else {
            if (!nowHost.protocol.snmp.community) {
                return false;
            }
        }
        return true;
    }

    $scope.IPv4Error = function (ipv4) {
        if (ipv4) {
            if (isIPv4(ipv4)) {
                return "　";
            } else {
                return "请输入有效的IP地址";
            }
        } else {
            return "IP地址不为空";
        }
    };

    $scope.showHostBox = function () {
        var W = 900;
        var H = W / 15 * 10;
        var title = $scope.nowHost.name ? $scope.nowHost.name : "新资产";
        zeroModal.show({
            title: title,
            content: hostBox,
            width: W + "px",
            height: H + "px",
            ok: true,
            cancel: true,
            okFn: function () {
                if (!verify()) {
                    return false;
                }
                $scope.save();
                $scope.saveMibs(); //添加mibs信息
            },
            onCleanup: function () {
                $scope.inputfocus();
                hide_box.appendChild(hostBox);
            }
        });
    };
    //运行状态的弹框

    $scope.save = function () {
        // console.log(111);
        
        var loading = zeroModal.loading(4);
        var nowHost = angular.copy($scope.nowHost);
    
        // console.log(nowHost);
       
        
        var req;
        var url_host = "/proxy/iconnect/host";
        var url_protocol = "/proxy/iconnect/protocol";
        if (nowHost._id) {
            req = $http.put;
            url_host += "/" + nowHost._id;
            url_protocol += "/" + nowHost.protocol._id;
        } else {
            req = $http.post;
        }

        rqs_data_host = {
            name: nowHost.name,
            family: nowHost.family
        };
       
        rqs_data_protocol = {
            host: nowHost.name,
            snmp: nowHost.protocol.snmp,
            ipv4: nowHost.protocol.ipv4
        };
        // console.log(rqs_data_host);
        // console.log(rqs_data_protocol);
        function req_protocol() {
            req(url_protocol, rqs_data_protocol).then(
                function success(rsp) {
                    zeroModal.close(loading);
                    if (rsp.data._status == "OK") {
                        zeroModal.success("保存成功!");
                        $scope.init();
                    } else {
                        zeroModal.error("保存失败!");
                    }
                },
                function err(rsp) {
                    zeroModal.close(loading);
                    zeroModal.error("保存失败!");
                }
            );
        }

        req(url_host, rqs_data_host).then(
            function success(rsp) {
                zeroModal.close(loading);
                if (rsp.data._status == "OK") {
                    req_protocol();
                } else {
                    zeroModal.error("保存失败!");
                }
            },
            function err(rsp) {
                zeroModal.close(loading);
                zeroModal.error("保存失败!");
            }
        );
    };

    $scope.setHosts = function () {
        if ($scope.protocols && $scope.hosts) {
            $scope.hostList = [];
            for (var index in $scope.hosts) {
                var host = $scope.hosts[index];
                if ($scope.protocols[host.name]) {
                    host.protocol = $scope.protocols[host.name];
                    $scope.hostList.push(host);
                }
            }
            
            updataTable($scope.hostList);
        }
    };
    $scope.getProtocol = function () {
        $scope.protocols = {};
        $http.get("/proxy/iconnect/protocol").then(
            function success(rsp) {
                // console.log(rsp)
                for (var index in rsp.data._items) {
                    var protocol = rsp.data._items[index];
                    $scope.protocols[protocol.host] = protocol;
                }
                $scope.setHosts();
            },
            function err(rsp) {}
        );
    };
    $scope.getHost = function () {
        $scope.hosts = {};
        $scope.hostNameList = [];
        $http.get("/proxy/iconnect/host").then(
            function success(rsp) {
                for (var index in rsp.data._items) {
                    var host = rsp.data._items[index];
                    $scope.hosts[host._id] = host;
                    $scope.hostNameList.push(host.name);
                }
                $scope.setHosts();
            },
            function err(rsp) {}
        );
    };

    $scope.init();
    //显示设备详情
    $scope.infoClick = function (id) {
        $scope.$apply(function () {
            $scope.info = false;
            $scope.hostName = "设备详情";
        });
        $scope.cpuEcharts(id);
        $scope.memEcharts(id);
        $scope.diskEcharts(id);
        $scope.getDtata(id); //获取设备日志
    };
    //点击返回列表
    $scope.back = function () {
        $scope.info = true;
        $scope.hostName = "安全设备列表";
    };

    // cup 使用率
    $scope.cpuEcharts = function (params) {
        // console.log(params);

        $http({
            method: 'GET',
            url: '/host/safety-equipment-state',
            params: {
                host_id: params
            }
        }).then(function (data, status, headers, config) {
            // console.log(data.data);
            var myChart = echarts.init(document.getElementById('cpuEchart'));
            var option = {
                tooltip: {
                    formatter: "{b} : {c}%"
                },
                series: [{
                    name: "",
                    type: "gauge",
                    detail: {
                        formatter: "{value}%"
                    },
                    splitNumber: 5,
                    axisLine: {
                        lineStyle: {
                            width: 10,
                            shadowBlur: 2,
                            color: [
                                [0.3, "rgb(75, 106, 37)"],
                                [0.7, "rgb(29, 66, 106)"],
                                [1, "rgb(186, 75, 72)"]
                            ]
                        }
                    },
                    axisLabel: { // 坐标轴小标记
                        distance: '-5',
                    },
                    axisTick: { // 坐标轴小标记
                        length: 10, // 属性length控制线长
                        lineStyle: { // 属性lineStyle控制线条样式
                            color: 'auto'
                        }
                    },
                    splitLine: { // 分隔线
                        length: 20, // 属性length控制线长
                        lineStyle: { // 属性lineStyle（详见lineStyle）控制线条样式
                            width: 3,
                            color: '#fff'
                        }
                    },
                    pointer: { // 指针
                        width: '3',
                        shadowColor: '#fff', //默认透明
                        itemStyle: {
                            color: 'auto'
                        }
                    },
                    title: {
                        show: false
                    },
                    detail: {
                        offsetCenter: [0, '60%'], // x, y，单位px,
                        textStyle: {
                            fontSize: 16,
                            color: 'auto'
                        },
                        formatter:function (params) {
                            return params + '%';
                        }
                    },
                    data: [{
                        value: data.data.cpu,
                        name: 'cpu'
                    }]
                }]
            };
            myChart.setOption(option);

            // 当相应准备就绪时调用
        }, function (error, status, headers, config) {
            console.log(error);
        })



    }
    // 内存占用率
    $scope.memEcharts = function (params) {
        // console.log(params);

        $http({
            method: 'GET',
            url: '/host/safety-equipment-state',
            params: {
                host_id: params
            }
        }).then(function (data, status, headers, config) {
            // console.log(data.data);
            var myChart = echarts.init(document.getElementById('memEchart'));
            var option = {
                tooltip: {
                    formatter: "{b} : {c}%"
                },
                series: [{
                    name: "",
                    type: "gauge",
                    detail: {
                        formatter: "{value}%"
                    },
                    splitNumber: 5,
                    axisLine: {
                        lineStyle: {
                            width: 10,
                            shadowBlur: 2,
                            color: [
                                [0.3, "rgb(75, 106, 37)"],
                                [0.7, "rgb(29, 66, 106)"],
                                [1, "rgb(186, 75, 72)"]
                            ]
                        }
                    },
                    axisLabel: { // 坐标轴小标记
                        distance: '-5',
                    },
                    axisTick: { // 坐标轴小标记
                        length: 10, // 属性length控制线长
                        lineStyle: { // 属性lineStyle控制线条样式
                            color: 'auto'
                        }
                    },
                    splitLine: { // 分隔线
                        length: 20, // 属性length控制线长
                        lineStyle: { // 属性lineStyle（详见lineStyle）控制线条样式
                            width: 3,
                            color: '#fff'
                        }
                    },
                    pointer: { // 指针
                        width: '3',
                        shadowColor: '#fff', //默认透明
                        itemStyle: {
                            color: 'auto'
                        }
                    },
                    title: {
                        show: false
                    },
                    detail: {
                        offsetCenter: [0, '60%'], // x, y，单位px,
                        textStyle: {
                            fontSize: 16,
                            color: 'auto'
                        },
                        formatter:function (params) {
                            return params + '%';
                        }
                    },
                    data: [{
                        value: data.data.mem,
                        name: 'mem'
                    }]
                }]
            };
            myChart.setOption(option);

            // 当相应准备就绪时调用
        }, function (error, status, headers, config) {
            console.log(error);
        })



    }
    // 硬盘占用率
    $scope.diskEcharts = function (params) {
        // console.log(params);

        $http({
            method: 'GET',
            url: '/host/safety-equipment-state',
            params: {
                host_id: params
            }
        }).then(function (data, status, headers, config) {
            // console.log(data.data);
            var myChart = echarts.init(document.getElementById('diskEchart'));
            var option = {
                tooltip: {
                    formatter: "{b} : {c}%"
                },
                series: [{
                    name: "",
                    type: "gauge",
                    detail: {
                        formatter: "{value}%"
                    },
                    splitNumber: 5,
                    axisLine: {
                        lineStyle: {
                            width: 10,
                            shadowBlur: 2,
                            color: [
                                [0.3, "rgb(75, 106, 37)"],
                                [0.7, "rgb(29, 66, 106)"],
                                [1, "rgb(186, 75, 72)"]
                            ]
                        }
                    },
                    axisLabel: { // 坐标轴小标记
                        distance: '-5',
                    },
                    axisTick: { // 坐标轴小标记
                        length: 10, // 属性length控制线长
                        lineStyle: { // 属性lineStyle控制线条样式
                            color: 'auto'
                        }
                    },
                    splitLine: { // 分隔线
                        length: 20, // 属性length控制线长
                        lineStyle: { // 属性lineStyle（详见lineStyle）控制线条样式
                            width: 3,
                            color: '#fff'
                        }
                    },
                    pointer: { // 指针
                        width: '3',
                        shadowColor: '#fff', //默认透明
                        itemStyle: {
                            color: 'auto'
                        }
                    },
                    title: {
                        show: false
                    },
                    detail: {
                        offsetCenter: [0, '60%'], // x, y，单位px,
                        textStyle: {
                            fontSize: 16,
                            color: 'auto'
                        },
                        formatter:function (params) {
                            return params + '%';
                        }
                    },
                    data: [{
                        value: data.data.disk,
                        name: 'disk'
                    }]
                }]
            };
            myChart.setOption(option);

            // 当相应准备就绪时调用
        }, function (error, status, headers, config) {
            console.log(error);
        })



    }

});
var Tables = {};
// var domId = "hostTable";
function updataTable( data,domId) {
    if (typeof domId === "undefined") {
        domId = domId || "hostTable"; 
    };
    if (Tables[domId]) {
        Tables[domId].clear();
        Tables[domId].rows.add(data);
        Tables[domId].draw();
    } else {
        Tables[domId] = $("#" + domId).DataTable({
            paging: true,
            lengthChange: false,
            searching: false,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                paginate: {
                    next: "下一页",
                    sPrevious: "上一页"
                },
                sInfoEmpty: "",
                sEmptyTable: "未查询到相关信息",
                sInfo: ""
            },
            data: data,
            columns: [{
                    data: "name"
                },
                {
                    data: "protocol.ipv4"
                },
                {
                    data: "family"
                },
                {
                    data: "_created"
                },
                {
                    data: function (item) {
                        if (item.online) {
                            return (
                                '<img src="../../images/backgrounds/green.png" style="height:16px;width:16px;" alt="在线" onclick="rootScope.details(\'' +
                                item._id +
                                "') \"/>"
                            );
                        } else {
                            return (
                                '<img src="../../images/backgrounds/red.png" style="height:16px;width:16px;" alt="离线" onclick="rootScope.details(\'' +
                                item._id +
                                "') \"/>"
                            );
                        }
                    }
                },
                {
                    data: function (item) {
                        return (
                            '<button style="margin: 0 5px;" class="btn btn-default btn-xs" onclick="rootScope.lookdetail(\'' +
                            item._id +
                            '\',event) " data-toggle="tooltip" title="查看"><i class="fa fa-eye"></i></button>' +
                            '<button style="margin: 0 5px;" class="btn btn-default btn-xs" onclick="rootScope.del(\'' +
                            item._id +
                            '\',event) " data-toggle="tooltip" title="删除资产"><i class="fa fa-trash-o"></i></button>' +
                            '<button style="margin: 0 5px;" class="btn btn-default btn-xs" onclick="rootScope.change(\'' +
                            item._id +
                            '\',event) " data-toggle="tooltip" title="修改资产"><i class="fa fa-edit"></i></button>'+
                            '<button style="margin: 0 5px;" class="btn btn-default btn-xs" onclick="rootScope.download(\'' +
                            item._id +
                            '\',event) " data-toggle="tooltip" title="下载日志"><i class="fa fa-download"></i></button>'
                        );
                    }
                }
            ]
        });
        //行点击事件
        // $("#" + domId).on("click", "tr", function () {

        //     var data = Tables[domId].row(this).data(); //获取值的对象数据
        //     // console.log(data._id);
        //     rootScope.infoClick(data._id);

        // })
    }
};