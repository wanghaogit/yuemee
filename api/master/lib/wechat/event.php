<?php
include Z_SITE . '/lib/WechatHandler.php';

class event_handler extends WechatHandler {
	function __construct() {
		parent::__construct();
	}
	
	public function execute(\Ziima\MVC\Wechat\Request $request) : ?\Ziima\MVC\Wechat\Response{
		$call = strtolower($request->Event);
		if(method_exists($this, $call)){
			return $this->$call($request);
		}else{
			$this->error("消息类型 $call 没有对应的处理器");
		}
	}

	private function subscribe(\Ziima\MVC\Wechat\SubscribeEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {
		if(!empty($this->WxUserInfo->nickname)){
			$this->WxUserInfo->nickname = json_encode($this->WxUserInfo->nickname);
			$this->WxUserInfo->nickname = preg_replace("/(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})/i","",$this->WxUserInfo->nickname);
			$this->WxUserInfo->nickname = json_decode($this->WxUserInfo->nickname);
		}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->login_wechat_ex(
				$this->WxUserInfo->openid,
				$this->WxUserInfo->unionid, 
				$this->WxUserInfo->nickname,
				$this->WxUserInfo->headimgurl,
				$this->WxUserInfo->sex,
				0, 0, '',
				$this->Context->Runtime->ticket->ip);
		if($ret === null)
			return $this->error('TODO:注册失败。');
		return $this->error('TODO: 下发引导消息。');
	}

	private function unsubscribe(\Ziima\MVC\Wechat\UnSubscribeEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {
		//退订，啥都不发，啥都不干
		return null;
	}

	private function scan(\Ziima\MVC\Wechat\ScanEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function location(\Ziima\MVC\Wechat\LocationEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function click(\Ziima\MVC\Wechat\ClickEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function view(\Ziima\MVC\Wechat\ViewEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function scancode_push(\Ziima\MVC\Wechat\ScancodePushEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function scancode_waitmsg(\Ziima\MVC\Wechat\ScancodeWaitmsgEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function pic_sysphoto(\Ziima\MVC\Wechat\PicSysPhotoEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function pic_photo_or_album(\Ziima\MVC\Wechat\PicPhotoOrAlbumEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function pic_weixin(\Ziima\MVC\Wechat\PicWeixinEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

	private function location_select(\Ziima\MVC\Wechat\LocationSelectEventRequest $request)  : ?\Ziima\MVC\Wechat\Response {

	}

} 
