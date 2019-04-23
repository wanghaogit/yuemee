<?php

include Z_SITE . "/lib/MobileHandler.php";

/**
 * 页面跳转
 */
class goto_handler extends MobileHandler {

	/**
	 * 上一页URL
	 * @var string
	 */
	private $ReUrl = "/mobile.php?call=download.index";

	/**
	 * 构造函数
	 * @param \Ziima\MVC\Context $ctx
	 */
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
		$this->ReUrl = $_SERVER['HTTP_REFERER'];
		$_SESSION['ReUrl'] = $this->ReUrl;
		$this->Context->Response->assign('ReUrl', $this->ReUrl);
	}

	/**
	 * 首页
	 * https://a.yuemee.com/mobile.php?call=goto.index
	 */
	public function index() {
		
	}

	/**
	 * 跳转到用户中心页面
	 * @param int $type	类型：0用户中心首页，1收货地址列表
	 * https://a.yuemee.com/mobile.php?call=goto.user_center&type=1
	 */
	public function user_center(int $type = 0) {
		switch ($type) {
			case 1: $URL = "/mobile.php?call=user_center.address";
				break;
			default: $URL = "/mobile.php?call=user_center.index";
				break;
		}
		throw new \Ziima\MVC\Redirector($URL, 301);
	}
}
