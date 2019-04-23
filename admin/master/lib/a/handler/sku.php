<?php

include "lib/ApiHandler.php";

/**
 * sku管理接口
 */
class sku_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 计算佣金
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		商品ID
	 */
	public function jisuan(\Ziima\MVC\REST\Request $request)
	{
		//所有改变
		$all = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`sku_changes` WHERE id = {$request->body->id}");
		//sku_id
		$sku_id = $all['sku_id'];
		//旧佣金
		$old_rebate = $this->MySQL->scalar("SELECT rebate_vip FROM `yuemi_sale`.`sku` WHERE id = {$sku_id}");
		//sku信息
		$sku = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`sku` WHERE id = {$sku_id}");
		//新佣金
		if($all['chg_price_base'] == 1){		//成本价
			$new = $all['new_price_base'];
			$yj = ($sku['price_sale'] - $new) * 0.56;
		}
		if($all['chg_price_sale'] == 1)			//阅米价 
		{
			$new = $all['new_price_sale'];
			$yj = ($sku['price_sale'] - $new) * 0.56;
		}
		return [
			'old_rebate' => $old_rebate,
			'yj' => $yj
		];
	}
	
	
	/**
	 * 获取商品邀请价
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		skuid		int		商品ID
	 */
	public function get_priceinv(\Ziima\MVC\REST\Request $request){
		$row = $this->MySQL->row("SELECT `price_inv` FROM `yuemi_sale`.`sku` WHERE `id` = {$request->body->skuid}");
		return [
			'price_inv' => $row['price_inv']
		];
	}
}
