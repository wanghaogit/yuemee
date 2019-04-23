<?php
/**
 * 微信回调
 */
include "../../_base/config.php";
include Z_ROOT . '/Wechat.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_SITE . '/../../_base/entity/yuemi_main.php';

// http://a.f.ziima.cn/wechat.php
// /wechat.php?signature=ed90dd408d77dc9a0a16f41033e66248953c419c&echostr=14694977429902964703&timestamp=1520908172&nonce=3367194938
$_REQUEST_MESSAGE_MAP = [
	'text' => '\Ziima\MVC\Wechat\TextRequest',
	'image' => '\Ziima\MVC\Wechat\ImageRequest',
	'voice' => '\Ziima\MVC\Wechat\VoiceRequest',
	'video' => '\Ziima\MVC\Wechat\VideoRequest',
	'shortvideo' => '\Ziima\MVC\Wechat\ShortVideoRequest',
	'location' => '\Ziima\MVC\Wechat\LocationRequest',
	'link' => '\Ziima\MVC\Wechat\LinkRequest',
	'event' => [
		'subscribe' => '\Ziima\MVC\Wechat\SubscribeEventRequest',
		'unsubscribe' => '\Ziima\MVC\Wechat\UnSubscribeEventRequest',
		'SCAN' => '\Ziima\MVC\Wechat\ScanEventRequest',
		'LOCATION' => '\Ziima\MVC\Wechat\LocationEventRequest',
		'CLICK' => '\Ziima\MVC\Wechat\ClickEventRequest',
		'VIEW' => '\Ziima\MVC\Wechat\ViewEventRequest',
		'scancode_push' => '\Ziima\MVC\Wechat\ScancodePushEventRequest',
		'scancode_waitmsg' => '\Ziima\MVC\Wechat\ScancodeWaitmsgEventRequest',
		'pic_sysphoto' => '\Ziima\MVC\Wechat\PicSysPhotoEventRequest',
		'pic_photo_or_album' => '\Ziima\MVC\Wechat\PicPhotoOrAlbumEventRequest',
		'pic_weixin' => '\Ziima\MVC\Wechat\PicWeixinEventRequest',
		'location_select' => '\Ziima\MVC\Wechat\LocationSelectEventRequest',
	]
];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	//首次测试入口
	if (empty($_GET)) {
		echo 'ERROR_1';
		return;
	}
	$signature = $_GET['signature'] ?? '';
	$echostr = $_GET['echostr'] ?? '';
	$timestamp = $_GET['timestamp'] ?? '';
	$nonce = $_GET['nonce'] ?? '';
	$token = WECHAT_TOKEN;
	if (empty($signature)) {
		echo 'ERROR_2';
		return;
	}
	if (empty($echostr)) {
		echo 'ERROR_3';
		return;
	}
	if (empty($timestamp)) {
		echo 'ERROR_4';
		return;
	}
	if (empty($nonce)) {
		echo 'ERROR_5';
		return;
	}
	if (empty($token)) {
		echo 'ERROR_6';
		return;
	}
	$src = [$token, $timestamp, $nonce];
	sort($src);
	if (sha1(implode('', $src)) == $signature) {
		echo $echostr;
	} else {
		echo 'ERROR_7';
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	/**
	 * @var \Ziima\MVC\Wechat\Request
	 */
	$request = null;
	/**
	 * @var \Ziima\MVC\Wechat\Response
	 */
	$response = null;
	$source = file_get_contents('php://input');
	file_put_contents(Z_SITE . '/data/wechat.xml', $source);

	if ($source === false || $source === null || strlen($source) < 1) {
		echo 'ERROR_8';
		return;
	}
	$xmldoc = simplexml_load_string($source);
	if ($xmldoc === null || $xmldoc === false) {
		echo 'ERROR_9';
		return;
	}
	if (!isset($xmldoc->MsgType)) {
		echo 'ERROR_10';
		return;
	}
	$msgType = $xmldoc->MsgType->__toString();
	if (!isset($_REQUEST_MESSAGE_MAP[$msgType])) {
		echo 'ERROR_11';
		return;
	}
	$msgClass = $_REQUEST_MESSAGE_MAP[$msgType];
	$hdlClass = strtolower($msgType) . '_handler';
	$hdlFile = Z_SITE . '/lib/wechat/' . strtolower($msgType) . '.php';
	if (!is_array($msgClass)) {
		$request = new $msgClass($xmldoc);
		include $hdlFile;
		$target = new $hdlClass(WECHAT_NAME, WECHAT_APPID, WECHAT_TOKEN, WECHAT_SECRET, WECHAT_AESKEY);
		//先初始化
		$response = $target->__init($request);
		if($response === null || $response === false){
			//再调用逻辑
			$response = $target->execute($request);
		}
	} else {
		if (!isset($xmldoc->Event)) {
			echo 'ERROR_12';
			return;
		}
		$msgEvent = $xmldoc->Event->__toString();
		if (!isset($msgClass[$msgEvent])) {
			echo 'ERROR_13';
			return;
		}
		$msgClass = $_REQUEST_MESSAGE_MAP[$msgType][$msgEvent];
		$request = new $msgClass($xmldoc);
		include $hdlFile;
		$target = new $hdlClass(WECHAT_NAME, WECHAT_APPID, WECHAT_TOKEN, WECHAT_SECRET, WECHAT_AESKEY);
		//先初始化
		$response = $target->__init($request);
		if($response === null || $response === false){
			//再调用逻辑
			$response = $target->execute($request);
		}
	}
	if ($response !== null) {
		$response->ToUserName = $request->FromUserName;
		$response->FromUserName = $request->ToUserName;
		$response->CreateTime = Z_NOW;
		echo $response->toXml();
	}
} else {
	echo 'ERROR_14';
}
