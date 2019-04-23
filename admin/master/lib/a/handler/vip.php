<?php

include "lib/ApiHandler.php";

/**
 * VIP管理接口
 */
class vip_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}


	/**
	 * 给予测试VIIP身份
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		user_id		int		用户ID
	 * @request		days		int		测试天数
	 */
	public function test(\Ziima\MVC\REST\Request $request) {
		$rst = \yuemi_main\ProcedureInvoker::Instance()->make_test_vip(
				$request->body->user_id,
				$request->body->days,
				$this->Context->Runtime->ticket->ip);
		if($rst === null){
			throw new \Ziima\MVC\REST\Exception('E_DATABASE','存储过程无返回');
		}
		if($rst->ReturnValue != 'OK'){
			throw new \Ziima\MVC\REST\Exception('E_LOGICAL',$rst->ReturnMessage);
		}
		return 'OK';
	}
}
