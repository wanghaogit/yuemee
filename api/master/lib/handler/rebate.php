<?php

include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Chart.php';
include_once Z_ROOT . '/QR.php';
include_once Z_ROOT . '/Cloud/Kuaidi.php';

/**
 * 佣金接口
 */
class rebate_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}
	
	/**
	 * 自买佣金 -- 将来可得
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */
	public function will_rebate_slef(\Ziima\MVC\REST\Request $request){
		$sql1 = "SELECT SUM(`self_profit`) FROM `yuemi_sale`.`rebate` WHERE `owner_id` = {$this->User->id} AND `status` IN (0,2)";
		$SelfPro = $this->MySQL->scalar($sql1);
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'selfpro'	=> ($SelfPro?:0)
		];
	}
	/**
	 * 分享佣金 -- 将来可得
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function will_rebate_share(\Ziima\MVC\REST\Request $request){
		$sql1 = "SELECT SUM(`share_profit`) FROM `yuemi_sale`.`rebate` WHERE `share_id` = {$this->User->id} AND `status` IN (0,2)";
		$SharePro = $this->MySQL->scalar($sql1);
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'selfpro'	=> ($SharePro?:0)
		];
	}
	/**
	 * 自买佣金 -- 已经获得
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */
	public function has_rebate_slef(\Ziima\MVC\REST\Request $request){
		$sql1 = "SELECT SUM(`self_profit`) FROM `yuemi_sale`.`rebate` WHERE `owner_id` = {$this->User->id} AND `status` = 3";
		$SelfPro = $this->MySQL->scalar($sql1);
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'selfpro'	=> ($SelfPro?:0)
		];
	}
	/**
	 * 分享佣金 -- 已经获得
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function has_rebate_share(\Ziima\MVC\REST\Request $request){
		$sql1 = "SELECT SUM(`share_profit`) FROM `yuemi_sale`.`rebate` WHERE `share_id` = {$this->User->id} AND `status` = 3";
		$SharePro = $this->MySQL->scalar($sql1);
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'selfpro'	=> ($SharePro?:0)
		];
	}
	/**
	 * 自买佣金列表
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function rebate_list(\Ziima\MVC\REST\Request $request){
		$sql1 = "SELECT item_id,order_id,cheif_id,director_id,sku_id,self_profit,status,IF(status = 3 ,1,0) AS OS FROM `yuemi_sale`.`rebate` WHERE `owner_id` = {$this->User->id} AND `status` != 1";
		$SelfPro = $this->MySQL->grid($sql1);
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'selfpro'	=> $SelfPro
		];
	}
	/**
	 * 自买佣金详情
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		item_id		int			模板ID
	 */
	public function rebate_item(\Ziima\MVC\REST\Request $request){
		$sql1 = "SELECT * FROM `yuemi_sale`.`rebate` WHERE item_id = {$request->body->item_id}";
		$SelfPro = $this->MySQL->row($sql1);
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'selfpro'	=> $SelfPro
		];
	}

}
