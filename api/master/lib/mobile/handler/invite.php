<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 邀请
 */
class invite_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 邀请入驻页面 
	 * @param string $v	邀请码
	 * @slient
	 * https://a.yuemee.com/mobile.php?call=invite.index&v=IzxQqqI9
	 */
	public function index(string $v)
	{
		// 用户已存在，则直接跳转到下载页
		if (isset($this->User->id)) {
			throw new \Ziima\MVC\Redirector('/mobile.php?call=download.index', 301);
			return;
		}
		// 未绑定手机号，则跳往绑定页
		throw new \Ziima\MVC\Redirector($this->getAuthUrl('invite', 'join', ['v' => $v]), 301);
	}

	/**
	 * 邀请加入
	 * @param string $v			邀请码
	 * @param string $code		微信授权码
	 * @param string $state		微信状态码
	 * https://a.yuemee.com/mobile.php?call=invite.join&v=IzxQqqI9
	 */
	public function join(string $v) 
	{	
		$invite_code = trim($v);
		$invite_feed = trim($v);
		return [
			'Wechat' => $this->Wechat,
			'User' => $this->User,
			'Invite' => [
				'Code' => $invite_code,
				'Feed' => $invite_feed,
				'Param' => 'wechat'
			]
		];
	}

}
