<?php

include_once 'lib/ApiHandler.php';

/**
 * 品牌API接口
 */
class brand_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}
	/**
	 * 品牌接口
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			品牌ID
	 */
	public function sel_brand(\Ziima\MVC\REST\Request $request) {
	
		$sql = "SELECT * FROM `yuemi_sale`.`brand` WHERE `id` = {$request->body->id}";
		$data = $this->MySQL->row($sql);
		return [
			'__code'	=>'OK',
			'__message'	=>'',
			'data'		=>$data
		];
	}
	
}
