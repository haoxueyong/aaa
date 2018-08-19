<?php
return [
	'confPath'=>'/web/conf',
 	'selfIP'=>'127.0.0.1',//管理端IP，此IP在隔离计算机时自动加入隔离白名单
 	'engineIP'=>'127.0.0.1',//引擎IP，此IP在隔离计算机时自动加入隔离白名单
 	'staticUrl'=>'http://127.0.0.1:8888',//静态文件下载路径，包括配置文件下载和探针文件下载，此配置需配合Nginx配置，公网可支持支持CDN分发
 	'backendUrl'=>'https://127.0.0.1:9090',//本项目中未使用到
 	'frontendUrl'=>'https://127.0.0.1',//管理端访问路径，告警时可能发送邮件给用户，邮件内容中的超链接可直接跳转到管理平台
 	'socketUrl'=>'ssl://127.0.0.1:9999',//管理端访问引擎的协议、IP地址、端口号
 	'staticPath'=>'/web/static',//静态文件保存路径，包括配置文件下载和探针文件下载，此配置需配合Nginx配置，公网可支持支持CDN分发
 	'logPath'=>'/web/log',//管理端运行日志存放路径
 	'orgId'=>10,//机构ID，每个管理端建议使用不同的机构ID，不同的机构ID可保证下发的SensorID在不同环境下都可以唯一
 	'ssl'=>[ 'ssl' => [
 			'local_cert'	=> '/web/src/threateyeback/pems/client-cert.pem',//链接引擎时使用的证书
 			'local_pk'	=> '/web/src/threateyeback/pems/client-key.pem',//链接引擎时使用的密钥
 			'verify_peer_name'  => false,
 			'allow_self_signed' => true,
 		]
 	], 
];
