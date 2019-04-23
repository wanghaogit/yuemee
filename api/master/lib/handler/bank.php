<?php
include_once 'lib/ApiHandler.php';

/**
 * 银行API接口
 */
class bank_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 银行接口
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			用户ID
	 * @noauth
	 */
	public function sel_bank(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT * FROM `yuemi_main`.`bank` WHERE `id` = {$request->body->id}";
		$data = $this->MySQL->row($sql);
		return [
			'__code'	=>'OK',
			'__message'	=>'',
			'data'		=>$data
		];
	}
}
