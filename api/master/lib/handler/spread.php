<?php
include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Cloud/Kuaidi.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';
include_once Z_SITE . '/../../_base/WeiXinPayment.php';

/**
 * 推广专用接口
 */
class spread_handler extends ApiHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 推广单品下单
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	goods_id		int				商品SkuId
	 * @request	name			string			姓名
	 * @request	mobile			string			手机号
	 * @request	code			string			验证码
	 * @request	wx				string			微信号
	 * @request	address			int				地区ID
	 * @request	address_info	string			详细地址
	 * @request	intro			string			留言
	 * @request	return_url		string			支付成功后跳转的URL
	 * @request	spread_code		string			活动的编码
	 * @noauth
	 */
	public function order_goods(\Ziima\MVC\REST\Request $request)
	{
		$GoodsId = $request->body->goods_id;
		$ReturnUrl = $request->body->return_url;
		
		// 注册用户
		$Re = \yuemi_main\ProcedureInvoker::Instance()->login_mobile($request->body->mobile, $request->body->code, $this->Context->Runtime->ticket->ip);
		if ($Re->ReturnValue != 'OK'){
			return[
				'__code'	=> $Re->ReturnValue,
				'__message' => $Re->ReturnMessage,
			];
		}

		// 创建收货地址
		$Address = new \yuemi_main\UserAddressEntity();
		$Address->address = $request->body->address_info;
		$Address->contacts = $request->body->name;
		$Address->create_from = $this->Context->Runtime->ticket->ip;
		$Address->create_time = Z_NOW;
		$Address->is_default = 1;
		$Address->mobile = $request->body->mobile;
		$Address->region_id = $request->body->address;
		$Address->status = 1;
		$Address->user_id = $Re->UserId;
		$ReA = \yuemi_main\UserAddressFactory::Instance()->insert($Address);
		if (!$ReA) {
			return[
				'__code'	=> 'E_ADDR',
				'__message' => '添加地址失败',
			];
		}

		// 记录信息
		$time = Z_NOW;
		$sql = "INSERT INTO `yuemi_sale`.`spread_userinfo`(`source`,`mobile`,`name`,`weixin`,`region_id`,`address`,`create_time`,`create_from`)"
			. "VALUES('{$request->body->spread_code}','{$request->body->mobile}','{$request->body->name}','{$request->body->wx}',{$request->body->address},'{$request->body->address_info}',{$time},{$this->Context->Runtime->ticket->ip})";
		$ReI = $this->MySQL->execute($sql);
		if (!$ReI) {
			return ['__code' => 'ERR', '__message' => '未知错误'];
		}

		// 权限检查
		$ReP = $this->check_power($GoodsId, $Re->UserId,1);
		if ($ReP['__code'] != 'OK') {
			return ['__code'	=> "BuyAlready", '__message'	=> $ReP['__message']];
		}
		$ReL = $this->limit_size($GoodsId, $Re->UserId);
		if (!$ReL){
			return ['__code'=>'ERR','__message'=>'超限购'];
		}

		// 创建阅米订单
		$ReO = \yuemi_sale\ProcedureInvoker::Instance()->fast_purchase($Re->UserId, 0, $GoodsId, 1, $Address->id, $this->Context->Runtime->ticket->ip, 0, 0, 0, 0,'');
		if ($ReO->ReturnValue !== 'OK') {
			return [
				'status'=> 0,
				'__code' => $ReO->ReturnValue,
				'__message' => $ReO->ReturnMessage,
				'order_id' => ''
			];
		}
		$Order = \yuemi_sale\OrderFactory::Instance()->load($ReO->OrderId);
		$this->MySQL->execute("UPDATE yuemi_sale.spread_userinfo SET `order_id` = '{$ReO->OrderId}' WHERE mobile = '{$request->body->mobile}'");
		$PayOnline = $Order->t_online;
		// 创建微信订单
		$OrderId = $ReO->OrderId;
		$WeiXinPayment = new WeiXinPayment();
		$ReUrl = "https://a.yuemee.com/wpay.php?order_id={$OrderId}";
		$TotalFee	= $PayOnline; // 价格，单位：分
		$TradeNo	= $OrderId; // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
		$NonceStr	= time()."-".rand(10000000,99999999); // 随机字符串，不长于32位，推荐随机数生成算法
		$Title		= "阅米支付，订单:{$OrderId}"; // 商品或支付单简要描述
		$WxOrder = $WeiXinPayment->OrderCreateH5($ReUrl, $TotalFee, $TradeNo, $NonceStr,$Title);
		if (!isset($WxOrder['mweb_url'])) {
			return ['__code' =>'error','__message' => json_encode($WxOrder)];
		}
		return ['__code' => 'OK','__message' => '', 'pay_url' => $WxOrder['mweb_url'] . "&redirect_url=" . urlencode($ReturnUrl."?order_id={$OrderId}&001")];
	}
	
	/**
	 * 获取订单支付状态
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	order_id	string	订单Id
	 * @noauth
	 */
	public function get_pay_status(\Ziima\MVC\REST\Request $request)
	{
		$id = trim($request->body->order_id);
		if (empty($id)) {
			return ['__code' => 'E01', '__message' => "订单不存在"];
		}

		// 读取订单信息
		$OrderInfo = $this->MySQL->row("SELECT * FROM yuemi_sale.`order` WHERE id = '{$id}'");
		if (!isset($OrderInfo['id'])) {
			return ['__code' => 'E01', '__message' => "订单不存在"];
		}
		if (!empty($OrderInfo['pay_serial'])) {
			$UserId = $OrderInfo['user_id'];
			$Mobile = $this->MySQL->scalar("SELECT `mobile` FROM `yuemi_main`.`user` WHERE `id` = {$UserId}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spread_userinfo` SET `status` = 1 WHERE `mobile` = '{$Mobile}'");
			return ['__code' => 'OK', '__message' => "支付成功"];
		}
		
		// 获取微信支付信息
		$WeiXinPayment = new WeiXinPayment();
		$WxOrderInfo = $WeiXinPayment->GetOrderInfo('', $OrderInfo['id']);
		if (!isset($WxOrderInfo['trade_state']) || strtoupper($WxOrderInfo['trade_state']) != 'SUCCESS') {
			return ['__code' => 'E02', '__message' => "尚未支付"];
		}
		$WxP = $WxOrderInfo['cash_fee']; // 微信实际支付金额(分)
		
		// 订单群支付
		if (abs($WxP-$OrderInfo['t_online']*100) < 1) {
			$this->MySQL->execute("UPDATE yuemi_sale.`order` SET pay_serial = '{$WxOrderInfo['transaction_id']}', pay_time = {$WxOrderInfo['time_end']}, `status`=2 WHERE depend_id = '{$id}'");
			$UserId = $OrderInfo['user_id'];
			$Mobile = $this->MySQL->scalar("SELECT `mobile` FROM `yuemi_main`.`user` WHERE `id` = {$UserId}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spread_userinfo` SET `status` = 1 WHERE `mobile` = '{$Mobile}'");
			return ['__code' => 'OK', '__message' => "支付成功"];
		}
		// 单订单支付
		elseif (abs($WxP-$OrderInfo['c_online']*100) < 1) 
		{
			$this->MySQL->execute("UPDATE yuemi_sale.`order` SET pay_serial = '{$WxOrderInfo['transaction_id']}', pay_time = {$WxOrderInfo['time_end']}, `status`=2 WHERE id = '{$id}'");
			$UserId = $OrderInfo['user_id'];
			$Mobile = $this->MySQL->scalar("SELECT `mobile` FROM `yuemi_main`.`user` WHERE `id` = {$UserId}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spread_userinfo` SET `status` = 1 WHERE `mobile` = '{$Mobile}'");
			return ['__code' => 'OK', '__message' => "支付成功"];
		}
		// 支付失败
		else {
			return ['__code' => 'E03', '__message' => "金额不一致"];
		}
	}
	
	private function check_power(int $sku_id,int $userid,int $qty)
	{
		if ($qty > 1){
			return [ '__code'	=> 'E_QTY', '__message'	=> '只能购买一个'];
		}
		//判断是否为新用户(24小时内注册)
		$User = \yuemi_main\UserFactory::Instance()->load($userid);
		$RegTime = $User->reg_time;
		if (($RegTime + 86400 ) < Z_NOW){
			return [ '__code'	=> 'E_USER', '__message'	=> '本次活动仅限24小时内注册的新用户购买，感谢关注!'];
		}
		//判断是否为有过购买行为
		$Sql2 = "SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE `user_id` = {$userid} AND `status` IN (1,2,4,5,6,7) ";
		$Count1 = $this->MySQL->scalar($Sql2);
		if ($Count1 > 0 ) {
			return [ '__code'	=> 'E_QTY', '__message'	=> '本次活动仅限阅米新用户购买，感谢关注!' ];
		}
		// 判断用户收货手机
		if ($User->mobile != "18610448275") {
			$Sql1 = "SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE `addr_mobile` = '{$User->mobile}' AND `status` IN (1,2,4,5,6,7)";
			if ($this->MySQL->scalar($Sql1) > 0 ){
				return [ '__code'	=> 'E_MOBILE', '__message'	=> '本次活动每个手机号码限购一次，感谢关注!' ];
			}
		}
		return ['__code'=>'OK'];
	}
	private function limit_size(int $sku_id,int $userid)
	{
		$Sku = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
		if ($Sku->limit_style == 1){
			$sql = "SELECT `order`.qty "
					. " FROM `yuemi_sale`.`order`  "
					. " LEFT JOIN `yuemi_sale`.order_item ON `order`.id = order_item.order_id "
					. "WHERE `order`.`user_id` = {$userid} AND `order_item`.`sku_id` = {$sku_id} AND `order`.`status` IN (1,2,4,5,6,7,8)";
			$count = $this->MySQL->scalar($sql);
			if ($count >= $Sku->limit_size){
				return false;
			}
		}
		return ['__code'=>'OK'];
	}

}
