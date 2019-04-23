<?php

include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Chart.php';
include_once Z_ROOT . '/QR.php';
include_once Z_ROOT . '/Cloud/Kuaidi.php';

/**
 * VIP接口
 */
class system_handler extends ApiHandler {

	/**
	 * VIP信息
	 * @var \yuemi_main\VipEntity
	 */
	private $Vip = null;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 获取短息验证码
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    mobile		string			银行id
	 */
	public function get_code(\Ziima\MVC\REST\Request $request){
		$data = $this->Redis->get($request->body->mobile);
		var_dump($data);exit;
	}

}
