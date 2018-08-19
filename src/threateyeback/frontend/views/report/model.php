<?php

$header = '<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
	<!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:View>Print</w:View>
  </w:WordDocument>
</xml><![endif]-->
</head>
<body>';
$footer = '</body></html>';
$content = '';
$content .= '<div style="width:100%;height:300px;text-align:center;"><img src="' . $server_ip . '/images/model-img.png" width="90" height="107.5"/></div>
    <h1 align="center" style="word-wrap:break-word; font-family:微软雅黑; word-break:normal; ">ThreatEye网络防APT解决方案</h1>
    <h2 align="center" style="word-wrap:break-word; font-family:微软雅黑; word-break:normal;">运行报告</h2>
    <h3 align="center" style="word-wrap:break-word; font-family:微软雅黑; word-break:normal;">' . $stime . '-' . $etime . '</h3>
<h4>目录</h4>
    <div style="font-family:宋体;font-size:17px;">
    <span>&nbsp;&nbsp;一、安全概览</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;1、威胁使用应用协议</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;2、告警趋势</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;3、威胁类型</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;4、受害主机</span><br/>
    <span>&nbsp;&nbsp;二、威胁详情</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;1、威胁等级分布</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;2、恶意URL TOP10</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;3、恶意IP TOP10</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;4、恶意文件 TOP10</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;5、受害主机TOP50</span><br/>
    <span>&nbsp;&nbsp;三、高危告警详情</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;1、勒索软件攻击</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;2、钓鱼攻击</span><br/>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;3、僵尸网络访问</span><br/>
    </div>
<h2>一、安全概览</h2>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">统计指定时间段内，平台整体威胁检测情况，对告警数量、威胁等级、应用协议等进行汇总，以快速了解当前安全形式。</p>
    <h3>1、威胁使用应用协议</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">网络内被检出的安全威胁利用的应用协议</p>
        <div style = "width:100%;height:300px;text-align:center;"><img src = "' . $server_ip . '/echarts/' . $threat_protocol . '" width = "500" height = "300"/></div >
        <p style = "text-indent:3em;word-wrap:break-word; font-family:楷体; word-break:normal; font-size:17px;">*关注威胁传播所利用的应用协议，并进行有针对性的防护，以及应用合适的安全策略</p>
    <h3>2、告警趋势</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">网络内指定时间段内每天产生威胁告警数量</p>
        <div style = "width:100%;height:300px;text-align:center;"><img src = "' . $server_ip . '/echarts/' . $alert_trend . '" width = "500" height = "300"/></div >
    <h3>3、威胁类型</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">网络内被检出的威胁告警所属威胁类型</p>
        <div style = "width:100%;height:300px;text-align:center;"><img src = "' . $server_ip . '/echarts/' . $alert_type . '" width = "500" height = "300"/></div >
        <p style = "text-indent:3em;word-wrap:break-word; font-family:楷体; word-break:normal; font-size:17px;">*通过威胁类型明确当前网络面临的整体安全形式，以调整管理和安全策略</p>
    <h3>4、受害主机</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">网络内每天产生威胁告警的内网IP数量</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr style="font-size:17px;">
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">威胁指标</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">访问次数</th>
        </tr>
    </thead>
    <tbody>';
foreach ($perday_ip as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $key . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value . '</td>
    </tr >';
}
$content .= '</tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; font-family:楷体; word-break:normal; font-size:17px;">*受害主机指用户内网中被攻击或者已经感染恶意软件的主机横向攻击内网其他主机以及发起对外部网络的攻击</p>
<h2>二、威胁详情</h2>
    <h3>1、威胁等级分布</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">网络内威胁告警的风险等级分布情况</p>
        <div style = "width:100%;height:300px;text-align:center;"><img src = "' . $server_ip . '/echarts/' . $threat_level . '" width = "500" height = "300"/></div >
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*平台告警按照不同的威胁指标划为为高、中、低三个等级，以帮助用户区分告警处理的优先级。</p>
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*高危告警</p>
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">会给用户带来直接损失的威胁，需要即刻关注和处理，例如勒索软件、钓鱼网站</p>
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*中危告警</p>
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">不会马上带来影响，但往往是其他攻击发起的前奏，需要及时关注和处理，例如弱口令、垃圾邮件</p>
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*低危告警</p>
        <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">不会马上带来影响，没有明显的恶意行为，可能是灰色软件或者不合规访问出发的告警，需要用户结合自身环境做一进步判断，例如：端口扫描、广告软件</p>
    <h3>2、恶意URL TOP10</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">被内网主机访问数量最多的前10个URL</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">威胁指标</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">威胁类型</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">访问次数</th>
        </tr>
    </thead>
    <tbody>';
foreach ($url_top10 as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['category'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator_count'] . '</td>
    </tr >';
}
$content .= '</tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*恶意URL</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">用户访问此URL会造成损失或者带来潜在安全隐患，包括钓鱼链接、恶意软件下载、C&C服务器地址</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*如何防御</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">通过网关设备阻止内网主机对这些URL地址的访问，如果是C&C服务器地址访问，需要检测访问主机是否被植入木马，并进行清理</p>  
<h3>3、恶意IP TOP10</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">被内网主机访问数量最多的前10个IP</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">威胁指标</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">威胁类型</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">访问次数</th>
        </tr>
    </thead>
    <tbody>';
foreach ($ip_top10 as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['category'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator_count'] . '</td>
    </tr >';
}
$content .= '</tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*恶意IP</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">用户访问此IP地址会造成损失或者带来潜在安全隐患，包括钓鱼链接、恶意软件下载、C&C服务器地址、垃圾邮件地址等</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*如何防御</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">过网关设备阻止内网主机对这些URL地址的访问，如果是C&C服务器地址访问，需要检测访问主机是否被植入木马，并进行清理</p>
    <h3>4、恶意文件 TOP10</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">内网主机感染或者传播数量最多的前10个恶意文件</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">文件名称</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">威胁类型</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">传播协议</th>
        </tr>
    </thead>
    <tbody>';
foreach ($hash_top10 as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['category'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['application'] . '</td>
    </tr >';
}
$content .= '</tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*恶意文件</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">被恶意软件检测引擎或者安全沙箱判断为存在恶意行为，运行会给用户带来威胁和损失的文件，需要用户及时响应和处理</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*如何防御</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">通过网关型防病毒设备检测和组织通过网络传播的恶意文件，在终端部署杀毒方案阻止病毒的落地和运行</p>
    <h3>5、受害主机TOP50</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">内网中被攻击的主机按照被攻击次数排名前50的IP</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">受害主机</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">次数</th>
        </tr>
    </thead>
    <tbody>';
foreach ($host_top50 as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $key . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value . '</td>
    </tr >';
}
$content .= '</tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*受害主机</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">内网中被攻击或者被感染恶意文件横向攻击其他主机以及存在木马外联、数据回传行为的主机</p>
    <h2>三、高危告警详情</h2>
    <h3>1、勒索软件攻击</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">指定时间段内，内网用户发送和接收勒索软件统计</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">源地址</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">目的地址</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">勒索软件名称</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">使用应用协议</th>
        </tr>
    </thead>
    <tbody>';
foreach ($extortion_software as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['src_ip'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['dest_ip'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['application'] . '</td>
    </tr >';
}
$content .= '
    </tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*勒索软件</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">当下比较流行的攻击方式，通过加密用户主机关键信息，以要挟用户缴纳赎金来获取利益，往往会对用户业务造成直接的影响和不可挽回的损失，需要用户高度关注和响应</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*如何防御</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">1、避免勒索软件进入网络</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">通过规范内网用户网络访问行为，及时检测和阻止用户访问恶意软件站点、钓鱼网站、钓鱼邮件等高危行为，阻止勒索软件进入网络</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">2、有效检测和清除</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">在内网用户终端部署勒索软件专杀工具，避免勒索软件的落地和运行</p>
    <h3>2、钓鱼攻击</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">指定时间段内，内网用户访问钓鱼网站统计</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">源地址</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">目的地址</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">钓鱼网站</th>
        </tr>
    </thead>
    <tbody>';
foreach ($phishing as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['src_ip'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['dest_ip'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator'] . '</td>
    </tr >';
}
$content .= '</tbody>
    </table>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*钓鱼攻击</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">通过伪装成被用户信任的网站、邮件等形式获取用户敏感信息例如用户名、账号、信用卡信息等，或者向用户植入木马、后门程序以窃取信息。</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*如何防御</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">提高用户安全风险意识，并部署检测方案，阻止用户对这些链接的访问</p>    
    <h3>3、僵尸网络访问</h3>
        <p style = "text-indent:3em;word-wrap:break-word; word-break:normal; font-size:17px;">指定时间段内，内网被控主机访问C&C服务器统计</p>
        <table border="1" cellspacing="0" align="center">
    <thead>
        <tr>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">肉鸡IP</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">访问时间</th>
            <th style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:17px;">C&C服务器地址</th>
        </tr>
    </thead>
    <tbody>';
foreach ($botc_c as $key => $value) {
    $content .= '<tr align="center" style="font-size:16px">
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['botc_c_ip'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['visit_time'] . '</td>
    <td style = "overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' . $value['indicator'] . '</td>
    </tr >';
}
$content .= '
    </tbody>
    </table>
     <p style = "text-indent:3em;word-wrap:break-word; margin:0; margin-top:15px; font-family:楷体; word-break:normal; font-size:17px;">*僵尸网络</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">通过各种手段，在内网主机中植入bot程序，从而实现对主机的控制形成僵尸网络，从而利用这些主机成为被利用的工具，不仅会造成主机资源浪费，而且会导致信息泄露，典型利用如：DDOS攻击、恶意挖矿</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">*如何防御</p>
    <p style = "text-indent:3em;word-wrap:break-word; margin:0; font-family:楷体; word-break:normal; font-size:17px;">bot程序的传播往往是通过钓鱼网站或者垃圾邮件进行大面积传播，需要在网络如部署相应解决方案来阻止对钓鱼网站的访问，以及阻止垃圾邮件的接收。如果已经落地到本地，可以结合威胁情报检测肉鸡的非法外联，并进行阻止和清除bot程序</p>';
//文件下载
$file_name = iconv("utf-8", "gb2312", "运行报告_" . $stime . "-" . $etime . ".docx");
down_load($file_name, $header . $content . $footer);

//如果想直接保存到服务器的话 
// file_put_contents('test.doc',$header.$content.$footer); 
//文件下载函数
function down_load($showname, $content) {
    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
        $showname = rawurlencode($showname);
        $showname = preg_replace('/\./', '%2e', $showname, substr_count($showname, '.') - 1);
    }
    header("Cache-Control: ");
    header("Pragma: ");
    header("Content-Type: application/octet-stream");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Content-Length: " . (string) (strlen($content)));
    header('Content-Disposition: attachment; filename="' . $showname . '"');
    header("Content-Transfer-Encoding: binary\n");
    echo $content;
    exit();
}
