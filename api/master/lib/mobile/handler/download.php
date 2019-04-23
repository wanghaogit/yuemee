<?php

include Z_SITE . "/lib/MobileHandler.php";

/**
 * APP下载
 */
class download_handler extends MobileHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 下载首页
	 * http://a.ym.cn/mobile.php?call=download.index
	 */
	public function index() {
		
	}

	/**
	 * Android下载
	 * http://a.ym.cn/mobile.php?call=download.android
	 */
	public function android() {
		// TODO 下载统计
		throw new \Ziima\MVC\Redirector('http://a.app.qq.com/o/simple.jsp?pkgname=com.yuemee.app.main');
	}

	/**
	 * Ios下载
	 * http://a.ym.cn/mobile.php?call=download.ios
	 */
	public function ios() {
		// TODO 下载统计
		throw new \Ziima\MVC\Redirector('https://itunes.apple.com/cn/app/id1374107895?mt=8');
	}

}
