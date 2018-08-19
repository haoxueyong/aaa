var myApp = angular.module('myApp', []);
myApp.controller('myCtrl', function ($scope, $http, $filter) {
    $scope.SensorVersion = false;
    $scope.rsqType = true;

    $scope.statusData = [{
        num: 3,
        type: '所有'
    }, {
        num: 2,
        type: '已解决'
    }, {
        num: 0,
        type: '未解决'
    }];

    // 折线图表
    $scope.alertEcharts = function (params) {
        $http({
            method: 'GET',
            url: '/alert/get-alert-count'
        }).then(function (data, status, headers, config) {
            // console.log(data.data);
            var myChart = echarts.init(document.getElementById('alertEchart'));
            var option = {
                grid: {
                    bottom: 80,
                    top: 50,
                    left: 50,
                    right: 50
                },
                tooltip: {
                    trigger: 'axis',
                },
                dataZoom: [{
                        show: true,
                        realtime: true,
                        start: 80,
                        end: 100
                    },
                    {
                        type: 'inside',
                        realtime: true,
                        start: 80,
                        end: 100
                    }
                ],
                xAxis: [{
                    type: 'category',
                    boundaryGap: false,
                    axisLine: {
                        onZero: false
                    },
                    data: data.data.times.map(function (str) {
                        return str.replace(' ', '\n')
                    }),
                    axisTick: {
                        show: false
                    }
                }],
                yAxis: [{
                    name: '告警',
                    type: 'value',
                    axisTick: {
                        show: false
                    }
                }],
                series: [{
                        name: '告警',
                        type: 'line',
                        smooth: true,
                        showSymbol: false,
                        symbol: 'circle',
                        symbolSize: 3,
                        areaStyle: {
                            normal: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgba(150,33,22,.8)'
                                }, {
                                    offset: 1,
                                    color: 'rgba(150,33,22,.5)'
                                }], false)
                            }
                        },
                        animation: true,
                        lineStyle: {
                            normal: {
                                width: 3
                            }
                        },
                        data: data.data.alert_count
                    }

                ]
            };
            myChart.setOption(option);
        }, function (error, status, headers, config) {
            console.log(error);
        });

    };
    $scope.alertEcharts();
    // 默认是未解决
    $scope.selectedName = 0;
    $scope.setAriaID = function (item, $event) {
        $event.stopPropagation();
        if ($scope.ariaID == item.id) {
            $scope.ariaID = null;
        } else {
            $scope.ariaID = item.id;
        }
    }
    $scope.delAriaID = function ($event) {
        $event.stopPropagation();
        setTimeout(function () {
            $scope.ariaID = null;
        }, 10);
    };

    $scope.status_str = [{
        css: 'success',
        label: '新告警'
    }, {
        css: 'danger',
        label: '未解决'
    }, {
        css: 'default',
        label: '已解决'
    }];

    $scope.update = function (item) {
        // console.log(item.id);
        var dataJson = {
            id: item.id,
            status: '2'
        };
        var params = JSON.stringify(dataJson);
        $.ajax({
            url: '/alert/do-alarm',
            method: 'PUT',
            data: params,
            dataType: 'json',
            success: function (data) {
                // console.log(data);
                // 更改成功,刷新数据
                if (data == 1) {
                    $scope.search();
                }
            }
        })
    };
    $scope.pages = {
        data: [],
        count: 0,
        maxPage: "...",
        pageNow: 1,
    };
    $scope.IDList = [];
    $scope.ItemList = {};
    $scope.getPage = function (pageNow) {
        pageNow = pageNow ? pageNow : 1;
        $scope.pageGeting = true;
        var postData = {};
        if ($scope.postData) {
            postData = angular.copy($scope.postData);
        };
        postData['page'] = pageNow;
        // console.log(postData);
        $scope.loading = zeroModal.loading(4);
        $http.post('/alert/page', postData).then(function success(rsp) {
            angular.forEach(rsp.data.data, function (item, index) {
                $scope.hoohoolab_false = false;
                angular.forEach(JSON.parse(item.data).attr.sources, function (key, value) {
                // console.log(item.category);
                    if (key.split('_')[0] == 'hoohoolab') {
                        $scope.hoohoolab_false = true;
                        switch (key.split('_')[1]) {
                            case 'BotnetCAndCURL':
                                item.category = JSON.parse(item.data).attr.hoohoolab_threat; // 威胁类型
                                break;
                            case 'IPReputation':
                                // 多种判断
                                if (JSON.parse(item.data).attr.hoohoolab_category.indexOf(',') != -1) {
                                    var arrayCategory = JSON.parse(item.data).attr.hoohoolab_category.split(',');
                                    angular.forEach(arrayCategory, function (gx, dx) {
                                        gx = $.trim(gx);
                                        if (gx == 'malware') {
                                            arrayCategory[dx] = '恶意地址';
                                        } else if (gx == 'spam') {
                                            arrayCategory[dx] = '垃圾邮件';
                                        } else if (gx == 'botnet_cnс') {
                                            arrayCategory[dx] = '僵尸网络';
                                        } else if (gx == 'proxy') {
                                            arrayCategory[dx] = '网络代理';
                                        } else if (gx == 'tor_node') {
                                            arrayCategory[dx] = 'tor入口节点';
                                        } else if (gx == 'tor_exit_node') {
                                            arrayCategory[dx] = 'tor出口节点';
                                        } else if (gx == 'phishing') {
                                            arrayCategory[dx] = '钓鱼网站';
                                        }
                                    });
                                    item.category = arrayCategory.join(',');
                                } else {
                                    if (JSON.parse(item.data).attr.hoohoolab_category == 'malware') {
                                        item.category = '恶意地址';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'spam') {
                                        item.category = '垃圾邮件';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'botnet_cnс') {
                                        item.category = '僵尸网络';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'proxy') {
                                        item.category = '网络代理';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'tor_node') {
                                        item.category = 'tor入口节点';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'tor_exit_node') {
                                        item.category = 'tor出口节点';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'phishing') {
                                        item.category = '钓鱼网站';
                                    } else {
                                        item.category = JSON.parse(item.data).attr.hoohoolab_category;
                                    }
                                }
                                break;
                            case 'MaliciousHash':
                                item.category = JSON.parse(item.data).attr.hoohoolab_threat; // 威胁类型
                                break;
                            case 'MaliciousURL':
                                // 多种判断
                                if (JSON.parse(item.data).attr.hoohoolab_category.indexOf(',') != -1) {
                                    var arrayCategory = JSON.parse(item.data).attr.hoohoolab_category.split(',');
                                    angular.forEach(arrayCategory, function (gx, dx) {
                                        gx = $.trim(gx);
                                        if (gx == 'Malware') {
                                            arrayCategory[dx] = '恶意地址';
                                        } else if (gx == 'Bot C&C') {
                                            arrayCategory[dx] = '僵尸网络';
                                        } else if (gx == 'Fraud') {
                                            arrayCategory[dx] = '网络诈骗';
                                        } else if (gx == 'MobileMalware or Malicious redirect') {
                                            arrayCategory[dx] = '移动恶意软件及恶意重定向';
                                        }
                                    });
                                    item.category = arrayCategory.join(',');
                                } else {
                                    if (JSON.parse(item.data).attr.hoohoolab_category == 'Malware') {
                                        item.category = '恶意地址';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'Bot C&C') {
                                        item.category = '僵尸网络';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'Malware') {
                                        item.category = '网络诈骗';
                                    } else if (JSON.parse(item.data).attr.hoohoolab_category == 'MobileMalware or Malicious redirect') {
                                        item.category = '移动恶意软件及恶意重定向';
                                    } else {
                                        item.category = JSON.parse(item.data).attr.hoohoolab_category; // 威胁类型
                                    };
                                }
                                break;
                            case 'PhishingURL':
                                item.category = '钓鱼网站'; // 威胁类型
                                break;
                            case 'MobileMaliciousHash':
                                item.category = JSON.parse(item.data).attr.hoohoolab_threat; // 威胁类型
                                break;
                            default:
                                break;
                        }
                    }
                });
                // 开源情报匹配
                if (!$scope.hoohoolab_false) {
                    switch (item.category) {
                        case 'MalwareIP':
                            item.category = '恶意地址';
                            break;
                        case 'C&C':
                            item.category = '僵尸网络';
                            break;
                        case 'Malicious Host':
                            item.category = '恶意地址';
                            break;
                        case 'Spamming':
                            item.category = '垃圾邮件';
                            break;
                        default:
                            break;
                    }
                };
            });
            zeroModal.close($scope.loading);
            $scope.setPage(rsp.data);
        }, function err(rsp) {
            console.log(rsp);
        });
    };

    $scope.setPage = function (data) {
        // console.log(data.data);
        angular.forEach(data.data, function (item, index) {
            switch (item.status) {
                case '0':
                    item.statusName = '新告警';
                    break;
                case '1':
                    item.statusName = '未解决';
                    break;
                case '2':
                    item.statusName = '已解决';
                    break;
                default:
                    break;
            }
        })
        $scope.pages = data;
        sessionStorage.setItem('alertPage', $scope.pages.pageNow);
    };
    // 点击跳转详情
    $scope.detail = function (item) {
        window.location.href = '/alert/' + item.id;
    };

    $scope.del = function (item, $event) {
        zeroModal.confirm({
            content: '确定删除这条告警吗？',
            okFn: function () {
                var postData = {
                    page: sessionStorage.getItem('alertPage'),
                    id: item.id
                };
                $http.post('/alert/del', postData).then(function success(rsp) {
                    $scope.setPage(rsp.data);
                }, function err(rsp) {});
            },
            cancelFn: function () {}
        });
    };

    $scope.showLength = function (str, length) {
        if (!length) {
            length = 60;
        }
        return str.substr(0, length) + '...';
    };

    $scope.myKeyup = function (e) {
        var keycode = window.event ? e.keyCode : e.which;
        if (keycode == 13) {
            e.target.blur();
            $scope.search();
        }
    };

    $('.timerange').daterangepicker({
        timePicker: true,
        timePickerIncrement: 10,
        // startDate: moment().startOf('year'),
        // endDate: moment().endOf('day'),
        startDate: moment().subtract(365, 'days'),
        endDate: moment(),
        locale: {
            applyLabel: '确定',
            cancelLabel: '取消',
            format: 'YYYY-MM-DD HH:mm',
            customRangeLabel: '指定时间范围'
        },
        ranges: {
            '今天': [moment().startOf('day'), moment().endOf('day')],
            '7日内': [moment().startOf('day').subtract(7, 'days'), moment().endOf('day')],
            '本月': [moment().startOf('month'), moment().endOf('day')],
            '今年': [moment().startOf('year'), moment().endOf('day')],
        }
    }, function (start, end, label) {
        $scope.searchData.startTime = start.unix();
        $scope.searchData.endTime = end.unix();
    });

    $scope.searchData = {
        client_ip: '',
        startTime: moment().subtract(365, 'days').unix(),
        endTime: moment().unix()
    };
    $scope.postData = {};

    $scope.search = function (key_change) {
        if ($scope.selectedName == '2' || $scope.selectedName == '3') {
            $scope.rsqType = false;
        } else {
            $scope.rsqType = true;
        };
        $scope.searchData.status = $scope.selectedName;
        $scope.postData = angular.copy($scope.searchData);
        $scope.getPage();
    };
    $scope.search();
    $scope.pageGeting = false;

});