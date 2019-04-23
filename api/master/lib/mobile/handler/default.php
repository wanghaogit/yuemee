<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 首页
 */
class default_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 移动端首页（目前保留）
	 */
	public function index(string $code = '',string $state = '') {
		return [
			'Wechat'	=> $this->Wechat,
			'User'		=> $this->User
		];
	}

	/**
	 * 登录
	 */
	public function login()
	{

	}

	/**
	 * 邀请入驻页面
	 * @param string $v			邀请码
	 * @slient
	 */
	public function invite(string $v){

		throw new \Ziima\MVC\Redirector(
				$this->getAuthUrl('default', 'join', ['v' => $v]),
				301
		);
	}

	/**
	 *
	 * @param string $v			邀请码
	 * @param string $code		微信授权码
	 * @param string $state		微信状态码
	 */
	public function join(string $v,string $code = '',string $state = ''){

	}

}
