<?php

include_once 'lib/ApiHandler.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';
/**
 * 购物车接口
 */
class cart_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 插入购物车
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		sku_id		int			商品ID
	 * @request		qty			int			商品数量
	 */
	public function add(\Ziima\MVC\REST\Request $request) {
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->cart_add($this->User->id,$ShareId = 0, $request->body->sku_id, $request->body->qty, $this->Context->Runtime->ticket->ip);
		if ($Re->ReturnValue !== 'OK'){
			return [
				'__code'	=>	$Re->ReturnValue,
				'__message'	=>	$Re->ReturnMessage,
				'cart_id'	=>	0
			];
		}
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'cart_id'	=> $Re->CartId
		];
	}

	/**
	 * 购物车内条目数量增加
	 * @param \Ziima\MVC\REST\Request $request
	 * @request			sku_id	int		商品ID
	 * @request			qty			int		商品数量
	 */
	public function inc(\Ziima\MVC\REST\Request $request) {
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->cart_add($this->User->id,$ShareId = 0, $request->body->sku_id, $request->body->qty, $this->Context->Runtime->ticket->ip);
		if ($Re->ReturnValue !== 'OK'){
			return [
				'__code'	=>	$Re->ReturnValue,
				'__message'	=>	$Re->ReturnMessage,
				'cart_id'	=>	0
			];
		}
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'cart_id'	=> $Re->CartId
		];
	}

	/**
	 * 购物车内条目数量减少
	 * @param \Ziima\MVC\REST\Request $request
	 * @request			cart_id	int		购物车ID
	 */
	public function dec(\Ziima\MVC\REST\Request $request) {
		$CartEntity = \yuemi_sale\CartFactory::Instance()->load($request->body->cart_id);
		$CartEntity->qty = $CartEntity->qty - 1;
		if ($CartEntity->user_id != $this->User->id){
			return[
				'__code' => 'ERR_CART',
				'__message' => '不是本人'
			];
		}
		if ($CartEntity->qty == 0){
			$sql = "DELETE FROM `yuemi_sale`.`cart` WHERE `id` = {$request->body->cart_id}";
			$this->MySQL->execute($sql);
			return[
				'__code' => 'OK',
				'__message' => ''
			];
		}
		$Re = \yuemi_sale\CartFactory::Instance()->update($CartEntity);
		if ($Re){
			return [
				'__code' => 'ERR_OR',
				'__message' => ''
			];
		}
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 购物车内条目删除
	 * @param \Ziima\MVC\REST\Request $request
	 * @request			cart_id	int		购物车ID
	 */
	public function del(\Ziima\MVC\REST\Request $request) {
		$CartEntity = \yuemi_sale\CartFactory::Instance()->load($request->body->cart_id);
		if ($CartEntity->user_id != $this->User->id){
			return[
				'__code' => 'ERR_CART',
				'__message' => '不是本人'
			];
		}
		$sql = "DELETE FROM `yuemi_sale`.`cart` WHERE `id` = {$request->body->cart_id}";
		$this->MySQL->execute($sql);
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 购物车清空
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			用户ID
	 */
	public function clear(\Ziima\MVC\REST\Request $request) {
		$sql = "DELETE FROM `yuemi_sale`.`cart` WHERE `user_id` = {$this->User->id}";
		if(!$this->MySQL->execute($sql)){
			return[
				'__code' => 'ERR_DEL',
				'__message' => '删除失败'
			];
		}
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 购物车列表
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 */
	public function list(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT * FROM `yuemi_sale`.`cart` WHERE `user_id` = {$this->User->id}";
		$Re = $this->MySQL->grid($sql);
		if (empty($Re)){
			return [
				'__code'	=>'OK',
				'__message'	=>'',
				'Re'		=>[],
				'sum'		=>0,
				'count'		=>0
			];
		}
		foreach($Re as $key=>$val){
			$sku_id = $val['sku_id'];
			$Sku = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
			$Re[$key]['Ref'] = $Sku->price_ref;
			$specs = $this->MySQL->scalar("SELECT specs FROM `yuemi_sale`.`sku` WHERE id = {$sku_id}");
			$Re[$key]['Specs'] = array_filter(explode("\n", $specs));
			$Re[$key]['sku_thumb'] = ($val['sku_thumb'] == '')?'':URL_RES . '/upload' .$val['sku_thumb'];
		}
		$sql1 = "SELECT SUM(`sku_price` * `qty`) AS sum FROM `yuemi_sale`.`cart` WHERE `user_id` = {$this->User->id} AND `is_checked` = 1";
		$Re1 = $this->MySQL->row($sql1);
		if ($Re1['sum']){
			$sum = $Re1['sum'];
		} else {
			$sum = 0;
		}
		return [
			'__code'	=>'OK',
			'__message'	=>'',
			'Re'		=>$Re,
			'sum'		=>$sum,
			'count'		=> count($Re)
		];
	}

	/**
	 * 购物车已选中列表
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 */
	public function list_checked(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT * FROM `yuemi_sale`.`cart` WHERE `user_id` = {$this->User->id} AND `is_checked` = 1";
		$Re = $this->MySQL->grid($sql);
		if (empty($Re)){
			return [
				'__code'	=>'OK',
				'__message'	=>'',
				'Re'		=>[],
				'sum'		=>0
			];
		}
		foreach($Re as $key=>$val){
			$sku_id = $val['sku_id'];
			$SkuEntity = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
			$specs = $this->MySQL->scalar("SELECT specs FROM `yuemi_sale`.`sku` WHERE id = {$sku_id}");
			$Re[$key]['Specs'] = array_filter(explode("\n", $specs));
			$Re[$key]['Ref'] = $SkuEntity->price_ref;
			$Re[$key]['sku_thumb'] = ($val['sku_thumb'] == '')?'':URL_RES . '/upload' .$val['sku_thumb'];
			$extspuid = $this->MySQL->scalar("SELECT `ext_spu_id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$sku_id}");
			if ($extspuid){
				$extshopcode = $this->MySQL->row("SELECT `ext_shop_code` FROM `yuemi_sale`.`ext_spu` WHERE `id` = {$extspuid}");
			} else {
				$extshopcode = [];
			}
			$Re[$key]['Code'] = empty($extshopcode)?'':$extshopcode['ext_shop_code'];
		}
		$sql1 = "SELECT SUM(`sku_price` * `qty`) AS sum FROM `yuemi_sale`.`cart` WHERE `user_id` = {$this->User->id} AND `is_checked` = 1";
		$Re1 = $this->MySQL->row($sql1);
		if ($Re1['sum']){
			$sum = $Re1['sum'];
		} else {
			$sum = 0;
		}
		return [
			'__code'	=>'OK',
			'__message'	=>'',
			'Re'		=>$Re,
			'sum'		=>$sum
		];
	}

	/**
	 * 购物车选中与取消
	 * @param \Ziima\MVC\REST\Request $request
	 * @request			cart_id		int		购物车ID
	 * @request			ischecked	int		选中状态
	 */
	public function checked(\Ziima\MVC\REST\Request $request) {
		$CartEntity = \yuemi_sale\CartFactory::Instance()->load($request->body->cart_id);
		if ($CartEntity->is_checked == 1){
			$sql = "UPDATE `yuemi_sale`.`cart` SET `is_checked` = 0 WHERE `id` = {$request->body->cart_id}";
		} else {
			$sql = "UPDATE `yuemi_sale`.`cart` SET `is_checked` = 1 WHERE `id` = {$request->body->cart_id}";
		}
		$this->MySQL->execute($sql);
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 购物车全选
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function checked_all(\Ziima\MVC\REST\Request $request) {
		$sql = "UPDATE `yuemi_sale`.`cart` SET `is_checked` = 1 WHERE `user_id` = {$this->User->id}";
		$this->MySQL->execute($sql);
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 购物车全不选
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function checked_no(\Ziima\MVC\REST\Request $request) {
		$sql = "UPDATE `yuemi_sale`.`cart` SET `is_checked` = 0 WHERE `user_id` = {$this->User->id}";
		$this->MySQL->execute($sql);
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}
	
	/**
	 * 获取购物车数量
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_number(\Ziima\MVC\REST\Request $request){
		$row = $this->MySQL->row("SELECT count(*) AS `sum` FROM `yuemi_sale`.`cart` WHERE `user_id` = {$this->User->id}");
		return [
			'sum' => $row['sum']
		];
	}
}
