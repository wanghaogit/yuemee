<?php
/**
 * 全局配置文件
 */

define("SECURITY_SALT_USER"		, 'user.yuemee.com');
define("SECURITY_SALT_ADMIN"	, 'admin.yuemee.com');
define("SECURITY_SALT_WORKER"	, 'work.yuemee.com');
define("SECURITY_SALT_SUPPLIER"	, 'supplier.yuemee.com');
define('SMS_ACCESS_KEY'			, 'DewlBvHJ2JA8culRt6g5AYeTkZHMKi');

define('WECHAT_NAME'			, '阅米家');
define('WECHAT_SRCID'			, 'gh_cf37397d3da2');
define('WECHAT_APPID'			, 'wx6c6f1cd0b051daee');					//wxf220af62fe16cffb
define('WECHAT_SECRET'			, '4be41605b8160a8b3bb0099e17aaad5c');		//8eef71a7f6b01a86e6d65157d6178af4
define('WECHAT_TOKEN'			, 'a0164cc0cbf8653466e94538bfbb5bed');
define('WECHAT_AESKEY'			, '6zPXnzjjhXrQXxqnw3m6LjX4lcT3ZP338kVg91rn2pZ');

define('WXAPP_NAME'				, '阅米');
define('WXAPP_APPID'			, 'wxf220af62fe16cffb');
define('WXAPP_SECRET'			, '8eef71a7f6b01a86e6d65157d6178af4');
define('WXAPP_BOUND'			, 'com.yuemee.app.main');
define('WXAPP_PARTNER_ID'		, '1501011831');
define('WXAPP_PARTNER_API_KEY'	, '8eef71a7f6b01a86e6d65157d6178af4');

define('WXOA_NAME'				, '阅米OA');
define('WXOA_APPID'				, 'wx261b6dc7ea841423');
define('WXOA_SECRET'			, '80497b66577595f64f838aa6d56f6ac1');
define('WXOA_BOUND'				, 'com.yuemee.app.oa');
define('WXOA_PARTNER_ID'		, '1499789582');

define('ALIYUN_SMSSIGN'			,'阅米');
define('ALIYUN_ACCESSID'		,'LTAIFLNgDJQHUmWw');
define('ALIYUN_ACCESSKEY'		,'DewlBvHJ2JA8culRt6g5AYeTkZHMKi');

define('KUAIDI_KEY'				, 'C4415213B8AEB5ED09DA35597A8C45CA');
define('KUAIDI_TOKEN'			, 'iHFRWqAL5367');

define('NG_CLIENTID'			, '242019a522a5173d4368003e8a545c50'); // 正式环境
define('NG_SECRET'				, '3936ba3c3a2e621584ecdf6348f14c48'); // 正式环境
define('NG_URL_BASE'			, 'https://openapi.neigou.com'); // 正式环境

define("YUNTONGXUN_AccountSid", "8a216da86339b5e801633f05893104a2"); // 账户Id
define("YUNTONGXUN_AuthToken", "bfcb90b2117d47998dd4c0e7697b0c7d"); // 账户授权令牌
define("YUNTONGXUN_AppIdMain", "8aaf0708635e4ce0016367ec2fcd064f"); // 主应用Id

if(Z_OS == 'WINDOWS' || Z_OS == 'LINUXMINT' || Z_OS == 'UBUNTU' || Z_HOSTNAME == 'winode'){		//本地开发环境
	define("MONGODB_HOST", '127.0.0.1');
	define("MONGODB_PORT", 27017);
	define("MONGODB_AUTH", '');

	define("REDIS_HOST", '127.0.0.1');
	define("REDIS_PORT", 6379);
	define("REDIS_AUTH", '');

	define("MYSQL_READER", 'mysql://root:123456@127.0.0.1:3306/yuemi_main');
	define("MYSQL_WRITER", 'mysql://root:123456@127.0.0.1:3306/yuemi_main');

	define("URL_DOMAIN",'.ym.cn');
	define("URL_PROTOCOL",'http');
	if(Z_OS == 'WINDOWS'){
		define('UPLOAD_ROOT'	, Z_SITE . '/data/upload');
	}else{
		define('UPLOAD_ROOT'	, '/data/nfs/upload');
	}
}elseif(Z_OS == 'CENTOS' && Z_IP == '172.19.57.234' && Z_HOSTNAME == 'test'){
	define("MONGODB_HOST", '127.0.0.1');
	define("MONGODB_PORT", 27017);
	define("MONGODB_AUTH", '');

	define("REDIS_HOST", '127.0.0.1');
	define("REDIS_PORT", 6379);
	define("REDIS_AUTH", 'Q1w2e3r4t5');

	define("MYSQL_READER", 'mysql://root:123456@127.0.0.1:3306/yuemi_main');
	define("MYSQL_WRITER", 'mysql://root:123456@127.0.0.1:3306/yuemi_main');

	define("URL_DOMAIN",'.yuemee.com');
	define("URL_PROTOCOL",'https');
	define('UPLOAD_ROOT'	, '/data/nfs/upload');
}elseif(Z_OS == 'CENTOS'){	//公网运行环境
	define("MONGODB_HOST", '172.19.57.234');
	define("MONGODB_PORT", 27017);
	define("MONGODB_AUTH", '');

	define("REDIS_HOST", 'r-uf6a30972cb2cf44.redis.rds.aliyuncs.com');
	define("REDIS_PORT", 6379);
	define("REDIS_AUTH", 'Q1w2e3r4t5');

	define("MYSQL_READER", 'mysql://yuemi:Q1w2e3r4t5@rr-uf61mqw0mqw68731x.mysql.rds.aliyuncs.com:3306/yuemi_main');
	define("MYSQL_WRITER", 'mysql://yuemi:Q1w2e3r4t5@rm-uf645fgt710n5mglw.mysql.rds.aliyuncs.com:3306/yuemi_main');

	define("URL_DOMAIN",'.yuemee.com');
	define("URL_PROTOCOL",'https');
	define('UPLOAD_ROOT'	, '/data/nfs/upload');
}else{
	throw new Error('尚未支持的操作系统 ' . Z_OS);
}

define('URL_ADMIN'		,URL_PROTOCOL . '://z' . URL_DOMAIN);
define('URL_API'		,URL_PROTOCOL . '://a' . URL_DOMAIN);
define('URL_RES'		,URL_PROTOCOL . '://r' . URL_DOMAIN);
define('URL_WECHAT'		,URL_PROTOCOL . '://x' . URL_DOMAIN);
define('URL_WEB'		,URL_PROTOCOL . '://m' . URL_DOMAIN);
define('URL_WORKER'		,URL_PROTOCOL . '://w' . URL_DOMAIN);
define('URL_SPUULIER'	,URL_PROTOCOL . '://s' . URL_DOMAIN);

define('STATUS_NAMES'	, [
	'User'	=> [
		'LevelUser'		=> ['禁闭','正常'],
		'LevelVip'		=> ['','测试','免费','卡邀','兑换','付费'],
		'LevelCheif'	=> ['','免费','晋升','卡位'],
		'LevelDirector'	=> ['','晋升','卡位'],
		'LevelTeam'		=> ['','员工','组长','经理','离职'],
		'LevelAdmin'	=> ['','普通','超级'],
		'LevelSupplier'	=> ['','间接','直接']
	],
	'Order'	=> [

	]
]);
define('ALIYUN_SMS_TEMPLATES' , [
	'SMS_134327385'	=> '您好，您已成功下载阅米APP，再次登陆阅米注册页面即可快速完成入驻。入驻平台会有更多功能可以体验哦！',
	'SMS_134322318'	=> '恭喜您已成功入驻阅米平台，亲，想要拥有自买省钱，分享赚钱的福利，马上注册成为阅米VIP导购哦！阅米，阅米，省钱由你。',
	'SMS_134328142'	=> '尊敬的${name}您好，您的VIP特权已开启，有效期从${start}到${expire}。',
	'SMS_134318038'	=> '尊敬的${name}您好，您的VIP特权已激活，有效期从${start}到${expire}。',
	
	'SMS_134327386'	=> '尊敬的${name}您好！您的总监账号已开通，阅米平台赠送的10张VIP激活卡已发放至您的阅米OA账户。您可以使用本手机号登陆“阅米OA”查收使用。祝您财源滚滚！',
	'SMS_134317308'	=> '尊敬的${name}您好！您的总监账号已激活，阅米平台赠送的10张VIP激活卡已发放至您的阅米OA账户。您可以使用本手机号登陆“阅米OA”查收使用。祝您财源滚滚！',
	'SMS_134312552'	=> '尊敬的${name}您好！您的总监账号已开通。您可以使用本手机号登陆“阅米OA”查收使用。祝您财源滚滚！',
	'SMS_134312305'	=> '尊敬的${name}您好！您的总监账号技术服务服务将于${days}天后到期，请及时续费。',
	'SMS_134318040'	=> '尊敬的${name}您好，你的VIP激活卡${card}已于${time}被${user}激活使用。',
	
	'SMS_134318037'	=> '尊敬的${name}您好！您的总经理账号已开通，阅米平台赠送的30张总经理激活卡已发放至您的阅米OA账户。您可以使用本手机号登陆“阅米OA”查收使用。祝您财源滚滚！',
	'SMS_134328150'	=> '尊敬的${name}您好！您的总经理账号已开通。您可以使用本手机号登陆“阅米OA”查收使用。祝您财源滚滚！',
	'SMS_134328141'	=> '尊敬的${name}您好！您的总经理账号技术服务服务将于${days}天后到期，请及时续费。',
	'SMS_134322320'	=> '尊敬的${name}您好，您旗下的总监激活卡${card}已于${time}被${user}激活使用。',
]);
include_once Z_ROOT . '/Ziima.php';
