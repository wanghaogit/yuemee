<?php

include "lib/ApiHandler.php";
include_once Z_ROOT . '/Cloud/Kuaidi.php';
include_once Z_ROOT . '/Cloud/Neigou.php';
include_once Z_SITE . '/../../_base/WuLiu.php';
include_once Z_SITE . '/../../_base/StateMachine.php';
include_once Z_SITE . '/../../_base/WeiXinPayment.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';


/**
 * 订单管理接口
 */
class order_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 订单物流修改
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $OrderId	订单Id
	 * @param string $TransNum	物流单号
	 * @param string $TransCom	物流公司代码
	 */
	public function change(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->OrderId; // 订单Id
		$TransCode = trim($request->body->TransNum); // 物流公司代码
		$TransCom = trim($request->body->TransCom); // 物流Id
		if (empty($TransCode)) {
			return ['__code' => 'Error', '__message' => '请输入物流单号'];
		}
		if (empty($TransCom)) {
			return ['__code' => 'Error', '__message' => '请选择物流公司'];
		}

		$OrderInfo = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE id = '{$id}'");
		$OrderItem = $this->MySQL->row("SELECT `oi`.`sku_id`,`id` FROM `yuemi_sale`.`order_item` AS `oi` WHERE `oi`.`order_id` = '{$id}'");
		$IsNot = $this->get_IsBig($OrderItem['sku_id']);
		if ($OrderInfo['status'] == 4) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`order` SET `trans_id` = '{$TransCode}', `trans_com` = '{$TransCom}', `status` = 5, `update_time` = UNIX_TIMESTAMP() WHERE id = '{$id}'");
//			if($IsNot > 0){
//				//是大礼包
//				$Re = \yuemi_main\ProcedureInvoker::Instance()->coin_income($OrderInfo['user_id'],1000,'购买大礼包',$OrderItem['id'],'无',$this->Context->Runtime->ticket->ip);
//				if($Re->TallyId > 0){
//					$Ree = \yuemi_main\ProcedureInvoker::Instance()->make_coin_vip($OrderInfo['user_id'],$this->Context->Runtime->ticket->ip);
//					if($Ree->ReturnValue == 'OK'){
//						return 'OK';
//					}
//				}
//			}
		}elseif($OrderInfo['status'] == 5){
			$this->MySQL->execute("UPDATE `yuemi_sale`.`order` SET `trans_id` = '{$TransCode}', `trans_com` = '{$TransCom}', `status` = 5, `update_time` = UNIX_TIMESTAMP() WHERE id = '{$id}'");
		}
		return 'OK';
	}

	/**
	 * 获取是否大礼包 1是 0不是
	 * @param int $kid sku_id
	 */
	private function get_IsBig(int $kid){
		$wli = $this->MySQL->row("SELECT `catagory_id`,`coin_buyer`,`coin_inviter` FROM `yuemi_sale`.`sku` WHERE `id` = {$kid}");
		if(!empty($wli)){
			if($wli['catagory_id'] == 701 && $wli['coin_buyer'] >= 1000){
				$big = 1;
			}else{
				$big = 0;
			}
		}else{
			$big = 0;
		}
		return $big;
	}
	
	/**
	 * 查看物流信息
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $OrderId	订单Id
	 */
	public function trans_info(\Ziima\MVC\REST\Request $request) {
		$OrderId = $request->body->OrderId; // 订单Id
		$OrderInfo = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE id = '{$OrderId}'");

		// 获取物流信息
		$data = null;
		if (!empty($OrderInfo['trans_com']) && !empty($OrderInfo['trans_id'])) {
			$Kd = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN);
			$ReData = $Kd->trace($OrderInfo['trans_com'], $OrderInfo['trans_id']);
			if (isset($ReData['data']) && is_array($ReData['data']) && count($ReData['data']) > 0) {
				$ReStr = null;
				foreach ($ReData['data'] AS $val) {
					$ReStr .= "{$val['time']} {$val['context']}\n";
				}
				return ['__code' => 'OK', '__message' => '', 'data' => $ReStr];
			}
		}
		return ['__code' => 'OK', '__message' => '', 'data' => "暂无物流信息"];
	}

	/**
	 * 物流订单号 -> 物流公司列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $order_id	物流订单号
	 */
	public function transnum_to_transcom_list(\Ziima\MVC\REST\Request $request) {
		$OrderId = $request->body->order_id; // 订单Id
		$OrderId = trim($OrderId);
		$WuLiu = new WuLiu();
		$WlComList = $WuLiu->get_list_by_sn($OrderId);
		$ReData = array();
		if (is_array($WlComList)) {
			foreach ($WlComList AS $key => $val) {
				$ReData[] = array('key' => $key, 'val' => $val);
			}
		}
		return ['__code' => 'OK', '__message' => '', 'data' => $ReData];
	}

	/**
	 * 订单发货
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string  id	 订单号
	 */
	public function deliver_goods(\Ziima\MVC\REST\Request $request) {
		$order = \yuemi_sale\OrderFactory::Instance()->load($request->body->id);
		$order->status = 5;
		$Re = \yuemi_sale\OrderFactory::Instance()->update($order);
		if (!$Re) {
			return ['__code' => 'Error', '__message' => ''];
		}
		return ['__code' => 'OK', '__message' => ''];
	}

	/**
	 * 删除总经理订单
	 *
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $order_id	订单Id
	 */
	public function delorder(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`director_buff` WHERE `id` = {$request->body->order_id}");
		return 'OK';
	}
	/**
	 * 删除总监订单
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $order_id	订单Id
	 */
	public function delorderc(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`cheif_buff` WHERE `id` = {$request->body->order_id}");
		return 'OK';
	}

	/**
	 * 补单 - 微信订单
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $order_id	订单Id
	 */
	public function repair_wx(\Ziima\MVC\REST\Request $request)
	{
		$Msg = null;
		$id = $request->body->order_id;
		$WeiXinPayment = new WeiXinPayment();

		$OrderInfo = $this->MySQL->row("SELECT * FROM yuemi_sale.`order` WHERE id = '{$id}'");
		if (empty($OrderInfo['pay_serial']))
		{
			$WxOrderInfo = $WeiXinPayment->GetOrderInfo('', $OrderInfo['id']);
			if (isset($WxOrderInfo['trade_state']) && strtoupper($WxOrderInfo['trade_state']) == 'SUCCESS')
			{
				// 微信实际支付金额(分)
				$WxP = $WxOrderInfo['cash_fee']; 
				// 订单群支付
				if (abs($WxP-$OrderInfo['t_online']*100) < 1) {
					$this->MySQL->execute("UPDATE yuemi_sale.`order` SET pay_serial = '{$WxOrderInfo['transaction_id']}', pay_time = {$WxOrderInfo['time_end']}, `status`=2 WHERE depend_id = '{$id}'");
					$Msg = "订单群更新成功，用户已成功支付，回单号：{$WxOrderInfo['transaction_id']}";
				}
				// 单订单支付
				elseif (abs($WxP-$OrderInfo['c_online']*100) < 1) 
				{
					$this->MySQL->execute("UPDATE yuemi_sale.`order` SET pay_serial = '{$WxOrderInfo['transaction_id']}', pay_time = {$WxOrderInfo['time_end']}, `status`=2 WHERE id = '{$id}'");
					$Msg = "订单更新成功，用户已成功支付，回单号：{$WxOrderInfo['transaction_id']}";
				}
			} else {
				$Msg = "无微信支付记录";
			}
		} else {
			$Msg = "支付回单号已存在";
		}
		return ['__code' => 'OK', '__message' => $Msg];
	}

	/**
	 * 关闭订单
	 * @param \Ziima\MVC\REST\Request $request
	 * @param string $order_id	订单Id
	 */
	public function off_order(\Ziima\MVC\REST\Request $request)
	{
		$id = $request->body->order_id;
		$data= \yuemi_sale\ProcedureInvoker::Instance()->close_order($id, $this->Context->Runtime->ticket->ip);
		if($data->ReturnValue == 'OK')
		{
			return ['__code' => 'OK', '__message' => '操作成功'];
		}else{
			return ['__code' => 'ERROR', '__message' => $data->ReturnMessage];
		}
		
	}
}
