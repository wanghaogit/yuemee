<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 认证相关
 */
class auth_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
		parent::__init();
	}

	/**
	 * 注册
	 * https://a.yuemee.com/mobile.php?call=auth.reg
	 */
	public function reg(int $type = 0, string $Parms = null) 
	{
		$ReturnUrl = "/";
		switch ($type)
		{
			case 1: $ReturnUrl = "https://a.yuemee.com/mobile.php?call=mall.item&share_id={$Parms}"; break;
		}
		
		return ["ReturnUrl" => $ReturnUrl];
	}

}
