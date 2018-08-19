var rootScope;
var myApp = angular.module("myApp", []);
myApp.controller("myCtrl", function ($scope, $http, $filter) {
    rootScope = $scope;


    $scope.init = function (params) {
        $scope.datafalse = false;  //版本暂时不需要，隐藏
        $scope.reportNameInput = true;
        $scope.selectedName = 'csv';
        $scope.alertMsg = false;
       
        $scope.reportName = $scope.getDate;
        $scope.choosetime = 'week';
        //   默认周报
        // console.log(moment());
        
        $scope.timerChoose = {
            maxDate: moment(),
            minDate: moment().subtract(90, 'days'),
            timePickerIncrement: 10,
            startDate: moment().subtract(7, 'days'),
            endDate: moment()
        }
        $scope.getDate = new Date();
        // console.log($scope.getDate);
        
        //可以当前日期
        $scope.nowDate = $filter("date")($scope.getDate, "yyyy-MM-dd HH:mm:ss");
        //获取前一天日期
        $scope.oldDay = $filter("date")($scope.getDate - 86400000, "yyyy-MM-dd HH:mm:ss");
        // console.log($scope.oldDay);
        
        //获取一周前日期
        $scope.oldWeek = $filter("date")($scope.getDate - 86400000 * 7, "yyyy-MM-dd HH:mm:ss");
        //获取一月前日期
        $scope.oldMonth = $filter("date")($scope.getDate - 86400000 * 30, "yyyy-MM-dd HH:mm:ss");
        $scope.startTime = $scope.oldWeek;
        $scope.endTime = $scope.nowDate;
        $scope.datapicker($scope.timerChoose);

        $scope.datatype = [{name:'运行报告(docx)',value:'docx'},{name:'告警列表(csv)',value:'csv'}];
        $scope.pages = {
            data: [],
            count: 0,
            maxPage: "...",
            pageNow: 1,
        };
        //获取报表 记录
        $scope.getPage(1);
    };
    
    $scope.inputfocus = function (params) {
           $scope.reportNameInput = true;
    }


    // 分页
    $scope.getPage = function (pageNow) {
       
        $scope.pages.pageNow=pageNow;
        $scope.getDataInfo($scope.pages);

    }

    //获取报表 记录
    $scope.getDataInfo = function (data) {
        // console.log(data);
        $http({
            method: 'GET',
            url: '/report/page',
            params: {
                page: data.pageNow,
                rows: 10
            }
        }).then(function (data, status, headers, config) {
           
          
            // console.log(data.data);
            $scope.pages = {
                data: data.data.data,
                count: data.data.count,
                maxPage: data.data.maxPage,
                pageNow: data.data.pageNow,
            };
            $scope.dataInfo = $scope.pages.data;

        }, function (error, status, headers, config) {
            console.log(error);
        })
    }


    //生成报表
    $scope.generate = function (params) {
     
        if($scope.reportName == undefined){
               $scope.reportNameInput = false;
       }else{
        $scope.loading =  zeroModal.loading(4);
        $scope.reportNameInput = true;
        // console.log(new Date($scope.startTime));
        // console.log(new Date($scope.endTime));
        
        $http({
            method: 'GET',
            url: '/report/create-report-form',
            params: {
                stime: new Date($scope.startTime).valueOf()/1000,
                etime: new Date($scope.endTime).valueOf()/1000,
                report_name: $scope.reportName,
                report_type:$scope.selectedName
            }
        }).then(function (data, status, headers, config) {
            // console.log(data.data);
            if(data.data == 0){
                // 添加成功，刷新数据 
                zeroModal.close($scope.loading);
                zeroModal.success("保存成功!");
                // $scope.getDataInfo();
                $scope.getPage();
                
            }
        }, function (error, status, headers, config) {
            zeroModal.close($scope.loading);
            zeroModal.error("保存失败!");
            console.log(error);
        })
       }
        if($scope.reportName){
            $scope.alertMsg = false;
         
        }else{
            $scope.alertMsg = true;
        }
    }
    // 删除报表
    $scope.del = function (params) {
        var item = params;
        function del_confirm(item) {
            $scope.loading =  zeroModal.loading(4);
            $.ajax({
                url: '/report/delete',
                method: 'POST',
                data: {
                    id: item
                },
                dataType: 'json',
                success: function (data) {
                //    console.log(data);  
                zeroModal.close($scope.loading);
                   if(data == 0){
                    // 添加成功，刷新数据
                    $scope.getDataInfo($scope.pages);
                    }
                }
    
            })
        }
        zeroModal.confirm({
            content: "确定删除报表吗？",
            okFn: function () {
                // console.log(111);
                del_confirm(item);
            },
            cancelFn: function () {}
        });
    }

    // 下载报表
    $scope.download = function (params) {
        function download_report(params) {
            var tt = new Date().getTime();
            var url = '/report/model';
        
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
            input1.attr("name","id");
            input1.attr("value",params);
            form.append(input1);
        
            // var input2=$("<input>");
            // input2.attr("type","hidden");
            // input2.attr("name","id");
            // input2.attr("value",params);
            // form.append(input2);
        
            form.submit();//表单提交
        }
           

            zeroModal.confirm({
                content: "确定下载报表吗？",
                okFn: function () {
                    // console.log(111);
                    download_report(params);
                },
                cancelFn: function () {}
            });
    }

    // 选择 日报月报 周报 触发
    $scope.changeTime = function (params) {

        if ($scope.choosetime == 'day') {
            $scope.timerChoose = {
                maxDate: moment(),
                minDate: moment().subtract(90, 'days'),
                timePickerIncrement: 10,
                startDate: moment().subtract(1, 'days'),
                endDate: moment()
            }
            $scope.startTime = $scope.oldDay;
            $scope.endTime = $scope.nowDate;
        } else if ($scope.choosetime == 'week') {
            $scope.timerChoose = {
                maxDate: moment(),
                minDate: moment().subtract(90, 'days'),
                timePickerIncrement: 10,
                startDate: moment().subtract(7, 'days'),
                endDate: moment()
            }
            $scope.startTime = $scope.oldWeek;
            $scope.endTime = $scope.nowDate;
        } else if ($scope.choosetime == 'month') {
            $scope.timerChoose = {
                maxDate: moment(),
                minDate: moment().subtract(90, 'days'),
                timePickerIncrement: 10,
                startDate: moment().subtract(30, 'days'),
                endDate: moment()
            }
            $scope.startTime = $scope.oldMonth;
            $scope.endTime = $scope.nowDate;
        }
        $scope.datapicker($scope.timerChoose);
    }

    // 时间插件
    $scope.datapicker = function (choosetime) {
        $('#reservationtime').daterangepicker({
            maxDate: choosetime.maxDate,
            minDate: choosetime.minDate,
            timePickerIncrement: 10,
            startDate: choosetime.startDate,
            endDate: choosetime.endDate,
            locale: {
                applyLabel: '确定',
                cancelLabel: '取消',
                format: 'YYYY-MM-DD HH:mm:ss'
            }
        }, function (start, end, label) {
            $scope.startTime = $filter("date")(start.unix() * 1000, "yyyy-MM-dd HH:mm:ss");
            $scope.endTime = $filter("date")(end.unix() * 1000, "yyyy-MM-dd HH:mm:ss");
        });

    }
    // 点击时间插件 取消日报月报周报选项
    $scope.custom = function (params) {
        $("input[type='radio']").removeAttr('checked');
    }




    $scope.init();


});