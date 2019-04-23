<?php

include_once 'lib/ApiHandler.php';

/**
 * API首页
 */
class default_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 获取IP
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		ip			string			用户ID
	 */
	public function get_ip(\Ziima\MVC\REST\Request $request){
		$ip = long2ip($request->body->ip);
		return[
			'ip' => $ip
		];
	}
	
	/**
	 * 修改我的密码
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			用户ID
	 * @request		op			string		旧密码
	 * @request		np			string		新密码
	 */
	public function passwd(\Ziima\MVC\REST\Request $request) {
		if ($this->Admin === null) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '尚未登录');
		}
		$uid = $request->body->id;
		if ($request->body->id <= 0) {
			$uid = $this->User->id;
		}
		if (!empty($request->body->op)) {
			$op1 = sha1(SECURITY_SALT_USER . '/' . $request->body->op);
			$op2 = $this->MySQL->scalar("SELECT `password` FROM `user` WHERE `id` = %d", $uid);
			if ($op1 !== $op2) {
				throw new \Ziima\MVC\REST\Exception('E_AUTH', '旧密码不对');
			}
		}
		if (empty($request->body->np) || strlen($request->body->np) < 6 || strlen($request->body->np) > 32) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '新密码格式不对');
		}
		if ($this->MySQL->execute("UPDATE `user` SET `password` = '%s' WHERE `id`= %d", sha1(SECURITY_SALT_USER . '/' . $request->body->np), $uid)) {
			return 'OK';
		} else {
			return 'E_DATABASE';
		}
	}

}
