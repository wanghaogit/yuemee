<?php

include "lib/ApiHandler.php";

/**
 * 实名认证专用接口
 */
class user_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

}
