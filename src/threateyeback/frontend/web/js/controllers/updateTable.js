var Tables = {};
var ColumnsTemplate = {
    UserLogon:[
        { data: 'UserName' },
        { data: 'ComputerName' },
        { 
            data: function(item){
                var Type_str = ['本地','远程'];
                return Type_str[item.Type];
            } 
        },
        { data: 'Count' },
        { 
            data: function(item){
                if(item.LastTime == null || item.LastTime == 18446732429207150){
                    return '';
                }
                return moment(item.LastTime,'x').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    NetProcess:[
        { data: 'UserName' },
        { data: 'ComputerName' },
        { data: 'ProcessName' },
        { data: 'PID' },
        {
            data: function(item){
                var dom  = $('<div>');
                var span = $('<span>');
                span.attr('title',item.CommandLine);
                dom.append(span);
                if(item.CommandLine.length > 45){
                    span.text(item.CommandLine.substr(0,45)+'...');
                }else{
                    span.text(item.CommandLine);
                }
                return dom.html();
            },
            class: 'oneline'
        },
        { data: 'LocalPort' },
        { data: 'RemoteIP' },
        { data: 'RemotePort' },
        {
            data: function(item){
                return moment(item.Time,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    UsbPlug:[
        { data: 'UserName' },
        { data: 'ComputerName' },
        { data: 'DriverName' },
        { data: 'DriverLetter' },
        {
            data: function(item){
                if(item.Time == null || item.Time == 18446732429207150){
                    return '';
                }
                return moment(item.Time,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    ConnectedComputer:[
        { data: 'ComputerName' },
        { data: 'IP' },
        { data: 'LocalPort' },
        { data: 'RemotePort' },
        {
            data: function(item){
                if(item.Time == null || item.Time == 18446732429207150){
                    return '';
                }
                return moment(item.Time,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    FileComputer:[
        { data: 'UserName' },
        { data: 'ComputerName' },
        { data: 'IP' },
        { data: 'FileName' },
        { data: 'ProcessName' },
        { data: 'PID' },
        {
            data: function(item){
                var hash = item.MD5 ? item.MD5 : item.SHA256;
                hash = hash.substr(0,5)+'...'+hash.substr(hash.length-6,5)
                var title = 'MD5:'+item.MD5+"\n\n"+
                            'SHA256:'+item.SHA256
                if(hash){
                    var dom  = $('<div>');
                    var span = $('<span>');
                    span.attr('title',title);
                    dom.append(span);
                    span.text(hash);
                    return dom.html();
                }else{
                    return ''
                }
            }
        },
        {
            data: function(item){
                if(item.ChildProcessNameList.length > 0){
                    var dom  = $('<div>');
                    var span = $('<span>');
                    span.attr('title',item.ChildProcessNameList.join("\n"));
                    dom.append(span);
                    if(item.ChildProcessNameList.length > 1){
                        span.text(item.ChildProcessNameList[0]+'...');
                    }else{
                        span.text(item.ChildProcessNameList[0]);
                    }
                    return dom.html();
                }else{
                    return '';
                }
            }
        },
        { data: 'ParentName' },
        {
            data: function(item){
                var dom  = $('<div>');
                var span = $('<span>');
                span.attr('title',item.CommandLine);
                dom.append(span);
                if(item.CommandLine.length > 15){
                    span.text(item.CommandLine.substr(0,15)+'...');
                }else{
                    span.text(item.CommandLine);
                }
                return dom.html();
            },
            class: 'oneline'
        },
        { 
            data: function(item){
                Status_str = ['未解决','已解决'];
                return Status_str[item.Status];
            }
        },
        {
            data: function(item){
                if(item.Time == null || item.Time == 18446732429207150){
                    return '';
                }
                return moment(item.Time,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    IMProcess:[
        { data: 'ComputerName' },
        { data: 'IP' },
        { data: 'FileName' },
        { data: 'ProcessName' },
        { data: 'PID' },
        {
            data: function(item){
                if(item.FileHashList.length > 0){
                    var title = '';
                    for (var i = 0; i < item.FileHashList.length; i++) {
                        var file = item.FileHashList[i];
                        title += '['+(i+1)+']'+file.MD5+"\n";
                    }
                    var hash = item.FileHashList[0].MD5;
                    var dom  = $('<div>');
                    var span = $('<span>');
                    span.attr('title',title);
                    dom.append(span);
                    span.text(hash);
                    return dom.html();
                }else{
                    return '';
                }
            }
        },
        {
            data: function(item){
                if(item.Time == null || item.Time == 18446732429207150){
                    return '';
                }
                return moment(item.Time,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    SignerFile:[
        { data: 'FileName' },
        { data: 'MD5' },
        {
            data: function(item){
                if(item.SHA256){
                    var title = item.SHA256;
                    var hash = item.SHA256.substr(0,15)+'...'+item.SHA256.substr(item.SHA256.length-16,15);
                    var dom  = $('<div>');
                    var span = $('<span>');
                    span.attr('title',title);
                    dom.append(span);
                    span.text(hash);
                    return dom.html();
                }else{
                    return '';
                }
            }
        },
        { data: 'Signer' }

    ],
    DomainProcess:[
        { data: 'ComputerName' },
        { data: 'IP' },
        {
            data: function(item){
                if(item.OSType){
                    var title = item.OSType;
                    var OSTypeArr = item.OSType.split(',');
                    var OSbits = OSTypeArr[4].split(' ')[0];
                    var OSTypeShort = OSTypeArr[0].trim()+' ('+OSbits+'-bit)';
                    var dom  = $('<div>');
                    var span = $('<span>');
                    span.attr('title',title);
                    dom.append(span);
                    span.text(OSTypeShort);
                    return dom.html();
                }else{
                    return '';
                }
            }
        },
        { data: 'ProcessName' },
        { data: 'PID' },
        {
            data: function(item){
                if(item.ImagePath.length > 30){
                    var title = item.ImagePath;
                    var dom  = $('<div>');
                    var span = $('<span>');
                    span.attr('title',title);
                    dom.append(span);
                    span.text(item.ImagePath.substr(0,30)+'...');
                    return dom.html();
                }else{
                    return item.ImagePath;
                }
            }
        },
        {
            data: function(item){
                if(item.FristTime == null || item.FristTime == 18446732429207150){
                    return '';
                }
                return moment(item.FristTime,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        },
        {
            data: function(item){
                if(item.LastTime == null || item.LastTime == 18446732429207150){
                    return '';
                }
                return moment(item.LastTime,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ],
    LogonEvent:[
        { data: 'UserName' },
        { data: 'ComputerName' },
        { data: 'IP' },
        { data: 'Domain' },
        {
            data: function(item){
                var Type_str = ['失败','成功'];
                return Type_str[item.LogonStatus];
            }
        },
        {
            data: function(item){
                if(item.Time == null || item.Time == 18446732429207150){
                    return '';
                }
                return moment(item.Time,'X').format("YYYY-MM-DD HH:mm:ss");
            }
        }
    ]

}
function updateTable(data,domId){
    if(Tables[domId]){
        Tables[domId].clear();
        Tables[domId].rows.add(data.List);
        Tables[domId].draw();
    }else{
        Tables[domId] = $('#'+domId).DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "paginate": {
                    "next": "下一页",
                    "sPrevious": "上一页"
                },
                "sInfoEmpty": "共 0 条记录",
                "sEmptyTable": "未查询到相关信息",
                "sInfo": ""
            },
            data: data.List,
            columns: ColumnsTemplate[domId]
        });
        Tables[domId].on('draw.dt', function () {
            $('#'+domId+'_paginate .pagination').addClass('pagination-sm');
            $('#'+domId+'_info').text("共 " + data.Count + " 有条记录");
        } );
        $('#'+domId+'_col').show();
        $('#'+domId+'_paginate').addClass('pull-right');
        $('#'+domId+'_paginate .pagination').addClass('pagination-sm');
        $('#'+domId+'_info').text("共 " + data.Count + " 有条记录");
    }
}
