<?php
include Z_SITE . '/lib/WechatHandler.php';

class voice_handler extends WechatHandler {
	function __construct() {
		parent::__construct();
	}
	
	public function execute(\Ziima\MVC\Wechat\Request $request) : ?\Ziima\MVC\Wechat\Response{
	
	}
}