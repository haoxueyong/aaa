echo "     __    __            __    __            __             __  "
echo "    / /   / /           / /   / /           / /            / /  "
echo "   / /___/ /__   ____  / /___/ /__   ____  / /    ____    / /_  "
echo "  / ____  / __ \/ __ \/ ____  / __ \/ __ \/ /    / __ \  / __ \ "
echo " / /   / / /_/ / /_/ / /   / / /_/ / /_/ / /____/ /_/  \/ /_/ / "
echo "/_/   /_/\____/\____/_/   /_/\____/\____/\_____/\____/\/\____/  "
echo ""

if [ ! -n "$1" ] ;then  
    echo "Please input the management platform IP:"
	read selfIP
else  
    selfIP=$1
fi

if [ ! -n "$2" ] ;then  
    echo "Please input engine IP:"
	read engineIP
else  
    engineIP=$2
fi


sudo cat > /web/conf/params-local.php<<-EOF
<?php
return [
 	'confPath'=>'/web/conf',
 	'selfIP'=>'${selfIP}',//管理端IP，此IP在隔离计算机时自动加入隔离白名单
 	'engineIP'=>'${engineIP}',//引擎IP，此IP在隔离计算机时自动加入隔离白名单
 	'staticUrl'=>'http://${selfIP}:8888',//静态文件下载路径，包括配置文件下载和探针文件下载，此配置需配合Nginx配置，公网可支持支持CDN分发
 	'backendUrl'=>'https://${selfIP}:9090',//本项目中未使用到
 	'frontendUrl'=>'https://${selfIP}',//管理端访问路径，告警时可能发送邮件给用户，邮件内容中的超链接可直接跳转到管理平台
 	'socketUrl'=>'ssl://${engineIP}:9999',//管理端访问引擎的协议、IP地址、端口号
 	'staticPath'=>'/web/static',//静态文件保存路径，包括配置文件下载和探针文件下载，此配置需配合Nginx配置，公网可支持支持CDN分发
 	'logPath'=>'/web/log',//管理端运行日志存放路径
 	'orgId'=>10,//机构ID，每个管理端建议使用不同的机构ID，不同的机构ID可保证下发的SensorID在不同环境下都可以唯一
 	'ssl'=>[ 'ssl' => [
 			'local_cert'        => '/web/src/threateyeback/pems/client-cert.pem',//链接引擎时使用的证书
 			'local_pk'  => '/web/src/threateyeback/pems/client-key.pem',//链接引擎时使用的密钥
 			'verify_peer_name'  => false,
 			'allow_self_signed' => true,
 		]
 	], 
];
EOF

sudo /web/conf/init.sh
if [[ -f /web/conf/socketPid ]]; then
	sudo kill $(cat /web/conf/socketPid)
fi