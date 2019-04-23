<?php
include "lib/AdminHandler.php";

/**
 * 访问提示页
 */
class visit_hint_handler extends AdminHandler 
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 访问提示页
	 * http://z.ym.cn/?call=visit_hint.index
	 */
	public function index(int $type=0) {

	}

}
