<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 订单
 */
class order_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 首页
	 * http://a.ym.cn/mobile.php?call=order.index
	 */
	public function index()
	{

	}

}
