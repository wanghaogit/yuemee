<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 购物车
 */
class shopping_cart_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 首页
	 * http://a.ym.cn/mobile.php?call=shopping_cart.index
	 */
	public function index()
	{

	}

}
