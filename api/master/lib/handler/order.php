<?php

include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Cloud/Kuaidi.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';
include_once Z_SITE . '/../../_base/WeiXinPayment.php';

/**
 * 订单接口
 * @auth
 */
class order_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 获取优惠券信息
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		ticketid	string		卡券ID
	 */
	public function get_ticket(\Ziima\MVC\REST\Request $request){
		$serial = preg_replace("/(\s|\&nbsp\;| | |　|　|\xc2\xa0)/","",$request->body->ticketid);
		$DiscountCoupon = \yuemi_sale\DiscountCouponFactory::Instance()->load($serial);
		$status = 0;
		if (!$DiscountCoupon){
			return [
				'__code' => 'OK',
				'__message'=>'',
				'tstatus'=>$status
			];
		}
		if ($DiscountCoupon->status == 0){
			$status = 1;
		}
		return [
			'__code' => 'OK',
			'__message'=>'',
			'tstatus'=>$status
		];
	}
	/**
	 * 快速下单
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		share_id		int		分享ID
	 * @request		sku_id			int		SKUID
	 * @request		qty				int		数量
	 * @request		user_address_id	int		收货地址Id：0表示使用默认值(需要从库里拿出默认值，或是第一条，也可能完全没有...)
	 * @request		sel_use_money	int		是否使用余额 1.使用，0不使用
	 * @request		sel_use_profit	int		是否使用佣金 1.使用，0不使用
	 * @request		sel_use_recruit	int		是否使用佣金礼包 1.使用，0不使用
	 * @request		sel_use_ticket	int		是否使用卡券 1.使用，0不使用
	 */
	public function fast_order(\Ziima\MVC\REST\Request $request) {
		if (isset($request->body->message)){
			$message = $request->body->message;
		} else {
			$message = '';
		}
		if (isset($request->body->ticketid)){
			$ticketid = $request->body->ticketid;
		} else {
			$ticketid = '';
		}
		$ReP = $this->check_power($request->body->sku_id, $this->User->id, $request->body->qty);
		if ($ReP['__code'] != 'OK') {
			return [
				'__code' => $ReP['__code'],
				'__message' => $ReP['__message']
			];
		}
		$ReL = $this->limit_size($request->body->sku_id, $this->User->id);
		if (!$ReL){
			return ['__code'=>'ERR','__message'=>'超限购'];
		}
		$serial = '';
		if ($request->body->sel_use_ticket > 0){
			$serial = preg_replace("/(\s|\&nbsp\;| | |　|　|\xc2\xa0)/","",$ticketid);
		}

		$Re = \yuemi_sale\ProcedureInvoker::Instance()->fast_purchase($this->User->id, $request->body->share_id, $request->body->sku_id, $request->body->qty, $request->body->user_address_id, $this->Context->Runtime->ticket->ip, $request->body->sel_use_money, $request->body->sel_use_profit, $request->body->sel_use_recruit, $request->body->sel_use_ticket,$serial, $message);
		if ($Re->ReturnValue !== 'OK') {
			return [
				'status' => 0,
				'__code' => $Re->ReturnValue,
				'__message' => $Re->ReturnMessage,
				'order_id' => ''
			];
		}

		// 读取订单详情并返回
		$Entity = \yuemi_sale\OrderFactory::Instance()->load($Re->OrderId);
		return [
			'status' => $Entity->status,
			'__code' => 'OK',
			'__message' => '',
			'order_id' => $Re->OrderId
		];
	}

	private function check_power(int $sku_id, int $userid, int $qty) {
		$Sku = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
		$IsSpread = $Sku->att_newbie;
		if ($IsSpread == 0) {
			return ['__code' => 'OK'];
		}
		if ($qty > 1) {
			return ['__code' => 'E_QTY', '__message' => '只能购买一个'];
		}
		//判断是否为新用户(24小时内注册)
		$User = \yuemi_main\UserFactory::Instance()->load($userid);
		$RegTime = $User->reg_time;
		if (($RegTime + 86400 ) < Z_NOW) {
			return ['__code' => 'E_USER', '__message' => '本次活动仅限24小时内注册的新用户购买，感谢关注!'];
		}
		//判断是否为有过购买行为
		$Sql2 = "SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE `user_id` = {$userid} AND `status` IN (1,2,4,5,6,7) ";
		$Count1 = $this->MySQL->scalar($Sql2);
		if ($Count1 > 0) {
			return ['__code' => 'E_QTY', '__message' => '本次活动仅限阅米新用户购买，感谢关注!'];
		}
		//判断用户收货手机
		$Sql1 = "SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE `addr_mobile` = '{$User->mobile}' AND `status` IN (1,2,4,5,6,7)";
		if ($this->MySQL->scalar($Sql1) > 0) {
			return ['__code' => 'E_MOBILE', '__message' => '本次活动每个手机号码限购一次，感谢关注!'];
		}
		return ['__code' => 'OK'];
	}
	/**
	 * 限购检查
	 */
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

	/**
	 * 物流详情
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		trans_id	  string		物流单号
	 *
	 */
	public function wuliu(\Ziima\MVC\REST\Request $request) {
		$Platofrm = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN);
		$wl_danhao = $request->body->trans_id;
		$redata = $Platofrm->info($wl_danhao); // 订单号查询快递公司
		$kuaidi_name = $redata[0];
		$data = $Platofrm->trace($kuaidi_name, $wl_danhao); // 订单详情 456027942123
		$com = $this->MySQL->scalar("SELECT `name` FROM `yuemi_main`.`kuaidi` WHERE `alias` = '{$data['com']}'");
		if ($com == '') {
			$com = '简称,' . $data['com'];
		}
		$nu = $data['nu'];
		$arr = [];
		$arr['time'] = '';
		$arr['context'] = '物流公司：' . $com . '&nbsp;&nbsp;快递单号：' . $nu;
		$arr['ftime'] = '';

		array_unshift($data['data'], $arr);
		return [
			'data' => $data,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 创建订单
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		user_address_id	int		收货地址Id：0表示使用默认值(需要从库里拿出默认值，或是第一条，也可能完全没有...)
	 * @request		sel_use_money	int		是否使用余额 1.使用，0不使用
	 * @request		sel_use_profit	int		是否使用佣金 1.使用，0不使用
	 * @request		sel_use_recruit	int		是否使用佣金礼包 1.使用，0不使用
	 * @request		sel_use_ticket	int		是否使用卡券 1.使用，0不使用
	 */
	public function create(\Ziima\MVC\REST\Request $request) {
		$UserId = $this->User->id;
		if (isset($request->body->message)){
			$message = $request->body->message;
		} else {
			$message = '';
		}
		$Sql = "SELECT `sku_id`,`qty` FROM `yuemi_sale`.`cart` WHERE `user_id` = {$UserId} AND `is_checked` = 1";
		$CartSkuId = $this->MySQL->grid($Sql);
		foreach ($CartSkuId AS $key => $val) {
			$ReP = $this->check_power($val['sku_id'], $this->User->id, $val['qty']);
			if ($ReP['__code'] != 'OK') {
				return [
					'__code' => $ReP['__code'],
					'__message' => $ReP['__message']
				];
			}
		}
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->cart_purchase($UserId, $request->body->user_address_id, $this->Context->Runtime->ticket->ip, $request->body->sel_use_money, $request->body->sel_use_profit, $request->body->sel_use_recruit, $request->body->sel_use_ticket, $message);
		$Entity = \yuemi_sale\OrderFactory::Instance()->load($Re->PrimaryOrderId);
		if ($Re->ReturnValue !== 'OK') {
			return [
				'status' => 0,
				'__code' => $Re->ReturnValue,
				'__message' => $Re->ReturnMessage,
				'order_id' => ''
			];
		}
		return [
			'status' => $Entity->status,
			'__code' => 'OK',
			'__message' => '',
			'order_id' => $Re->PrimaryOrderId
		];
	}

	/**
	 * 取消订单
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		order_id	string		订单Id
	 */
	public function cancle(\Ziima\MVC\REST\Request $request) {
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->cancle_order($request->body->order_id, $this->User->id, $this->Context->Runtime->ticket->ip);
		if ($Re->ReturnValue != 'OK') {
			return [
				'OrderId' => $request->body->order_id,
				'__code' => 'E_UP',
				'__message' => $Re->ReturnMessage
			];
		}
		return [
			'OrderId' => $request->body->order_id,
			'__code' => 'OK',
			'__message' => $Re->ReturnMessage
		];
	}

	/**
	 * 申请退款关闭--状态13
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		order_id	int		订单Id
	 */
	public function refund(\Ziima\MVC\REST\Request $request) {
		$order = \yuemi_sale\OrderFactory::Instance()->load($request->body->order_id);
		if ($order->status != 2) {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '不可取消');
		} elseif ($order->update_time < (Z_NOW - 900)) {
			throw new \Ziima\MVC\REST\Exception('E_TIME', '超过15分钟，不可取消');
		}
		$order->status = 13;
		$order->update_time = Z_NOW;
		$re = \yuemi_sale\OrderFactory::Instance()->update($order);
		if (!$re) {
			return [
				'__code' => 'E_UP',
				'__message' => '退款失败'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 确定订单--状态6->7
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		order_id	string		订单Id
	 */
	public function sure(\Ziima\MVC\REST\Request $request) {
		$OrderAccept = \yuemi_sale\ProcedureInvoker::Instance()->order_accept($request->body->order_id, $this->Context->Runtime->ticket->ip);
		if ($OrderAccept->ReturnValue != 'OK') {
			return [
				'__code' => $OrderAccept->ReturnValue,
				'__message' => $OrderAccept->ReturnMessage
			];
		}
		// 确认收货赠送阅币
		$Coin = \yuemi_main\ProcedureInvoker::Instance()->coin_income($this->User->id, 0.05, 'RECEIPT', $request->body->order_id, '确认收货奖励', $this->Context->Runtime->ticket->ip);
		if ($Coin->ReturnValue != 'OK') {
			return [
				'__code' => 'ERR',
				'__message' => '阅币赠送错误'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 订单列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		status		int		订单状态(0-全部.1-代付款，2-待收货，3-已完成，4-退换货)
	 */
	public function list(\Ziima\MVC\REST\Request $request) {
		$whr = [];
		//$whr[0] = 'AND `status` NOT IN (11,12,13,14)';
		$whr[0] = "";
		$whr[1] = " AND `status` = 1 ";
		$whr[2] = " AND `status` IN (2,4,5)";
		$whr[3] = " AND `status` IN (6,7)";
		$whr[4] = " AND `status` IN (21,22,23,24,25,31,32,33,34,35)";
		if (!isset($whr[$request->body->status])) {
			return [
				'Result' => [],
				'__code' => 'ERR_EMPTY',
				'__message' => '订单为空'
			];
		}
		$sql = "SELECT `id`,`status` AS Status FROM `yuemi_sale`.`order` WHERE `user_id` = {$this->User->id} " . $whr[$request->body->status] . " ORDER BY `create_time` DESC";
		$Re = $this->MySQL->grid($sql);
		if (empty($Re)) {
			return [
				'Result' => [],
				'__code' => 'ERR_EMPTY',
				'__message' => '订单为空'
			];
		}
		$Result = [];
		foreach ($Re AS $key => $val) {
			$order_id = $val['id'];
			$sql1 = "SELECT `sku_id` AS SkuId , `title` AS Title ,`supplier_id` AS SupplierId ,`qty` AS Qty ,`price` AS Price ,`money` AS Money, `picture` AS Picture "
					. "FROM `yuemi_sale`.`order_item` WHERE `order_id` = '{$order_id}'";
			$item = $this->MySQL->grid($sql1);
			$totalnum = 0;
			$totalmoney = 0;
			foreach ($item AS $k => $v) {
				$item[$k]['Picture'] = ($v['Picture'] == '') ? '' : URL_RES . '/upload' . $v['Picture'];
				$totalnum += $v['Qty'];
				$totalmoney += $v['Money'];
			}
			$Result[$key]['info'] = $item;
			$Result[$key]['Status'] = $val['Status'];
			$Result[$key]['Id'] = $val['id'];
			$Result[$key]['Num'] = $totalnum;
			$Result[$key]['Sum'] = $totalmoney;
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Result' => $Result
		];
	}

	/**
	 * 更新收货地址
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		order_id	int		订单Id
	 * @request		address_id	int		收货地址Id
	 */
	public function update_address(\Ziima\MVC\REST\Request $request) {
		$useradd = \yuemi_main\UserAddressFactory::Instance()->load($request->body->address_id);
		if (!$useradd) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '无地址');
		}
		$order = \yuemi_sale\OrderFactory::Instance()->load($request->body->order_id);
		if (!$order) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '无订单');
		}
		$order->address_id = $request->body->address_id;
		$order->addr_region = $useradd->region_id;
		$order->addr_detail = $useradd->address;
		$order->addr_mobile = $useradd->mobile;
		$order->addr_name = $useradd->mobile;
		$re = \yuemi_sale\OrderFactory::Instance()->update($order);
		if (!$re) {
			return [
				'__code' => 'ERR_UP',
				'__message' => '更新失败'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 加载订单信息
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		order_id	string		订单Id
	 */
	public function load_info(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$OrderId = $uid = $request->body->order_id;
		$sql1 = "SELECT I.`sku_id` AS SkuId,  I.`title` AS Title, I.`supplier_id` AS SupplierId, I.`qty` AS Qty, I.`price` AS Price, I.`money` AS Money, I.`picture` AS Picture, K.`price_ref` AS Ref, "
				. "K.`price_ref` AS Ref "
				. "FROM `yuemi_sale`.`order_item` AS I "
				. "LEFT JOIN `yuemi_sale`.`order` AS O ON O.`id` = I.`order_id` "
				. "LEFT JOIN `yuemi_sale`.`sku` AS K ON I.`sku_id` = K.`id` "
				. " WHERE `order_id` IN "
				. "(SELECT `id` FROM `yuemi_sale`.`order` WHERE `depend_id` = '{$OrderId}' OR `id` = '{$OrderId}') "
				. "ORDER BY O.`create_time` DESC";
		$Result = [];
		$item = $this->MySQL->grid($sql1);
		if (!empty($item)) {
			$totalnum = 0;
			$totalmoney = 0;
			foreach ($item AS $key => $val) {
				$item[$key]['Picture'] = ($val['Picture'] == '') ? '' : URL_RES . '/upload' . $val['Picture'];
				//$item[$key]['Specs'] = array_filter(explode("\n", $val['Specs']));
				$totalnum += $val['Qty'];
				$totalmoney += $val['Money'];
				$sku_id = $item[$key]['SkuId'];
				$extspuid = $this->MySQL->scalar("SELECT `ext_spu_id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$sku_id}");
				if ($extspuid) {
					$extshopcode = $this->MySQL->row("SELECT `ext_shop_code` FROM `yuemi_sale`.`ext_spu` WHERE `id` = {$extspuid}");
				} else {
					$extshopcode = [];
				}
				$item[$key]['Code'] = empty($extshopcode) ? '' : $extshopcode['ext_shop_code'];
			}
			$sql2 = "SELECT trans_com, trans_id, `status`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`t_amount`,`t_online`,`create_time` FROM `yuemi_sale`.`order` WHERE `id` = '{$OrderId}'";
			$status = $this->MySQL->row($sql2);
			$Result['TransCom'] = $status['trans_com'];
			$Result['TransId'] = $status['trans_id'];
			$Result['Status'] = $status['status'];
			$Result['AddressId'] = $status['address_id'];
			$Result['AddrRegion'] = $status['addr_region'];
			$Result['AddrDetail'] = $status['addr_detail'];
			$Result['AddrName'] = $status['addr_name'];
			$Result['AddrMobile'] = $status['addr_mobile'];
			$Result['TAmount'] = $status['t_amount'];
			$Result['TOnline'] = $status['t_online'];
			$Result['CreateTime'] = date("Y-m-d H:i:s", $status['create_time']);
			$Result['info'] = $item;
			$Result['Id'] = $OrderId;
			$Result['Num'] = $totalnum;
			$Result['Sum'] = $totalmoney;
			$Result['KuaiDi'] = null;
			$TransCom = null;
			$TransId = null;
			
			if (!empty($status['trans_id']) && !empty($status['trans_com'])) {
				$Kd = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN);
				$ReData = $Kd->trace($Result['TransCom'], $Result['TransId']);
				if (isset($ReData['data']) && is_array($ReData['data']) && count($ReData['data']) > 0) {
					$data = $ReData;
					$com = $this->MySQL->scalar("SELECT `name` FROM `yuemi_main`.`kuaidi` WHERE `alias` = '{$data['com']}'");
					if ($com == '') {
						$com = '简称,' . $data['com'];
					}
					$nu = $data['nu'];
					$arr = [];
					$arr['time'] = '';
					$arr['context'] = '物流公司：' . $com . '&nbsp;&nbsp;快递单号：' . $nu;
					$arr['ftime'] = '';

					array_unshift($ReData['data'], $arr);
					$Result['KuaiDi'] = $ReData['data'];
				}
			}
		}
		return ['__code' => 'OK', '__message' => '', 'order_info' => $Result];
	}

	/**
	 * 生成微信订单信息（用于APP支付）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	order_id		string		订单Id
	 * @request	is_merge_pay	int			是否合并支付：0不是(只支付当前订单)，1是（将该订单和其下的所有子订单合并支付）
	 */
	public function make_order_weixin(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id; // 用户Id
		$OrderId = $request->body->order_id; // 订单Id
		$MergePay = intval($request->body->is_merge_pay); // 是否合并支付
		$OrderInfo = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = {$uid} AND `id` = '{$OrderId}'");

		// 合并支付
		$money = 0;
		if ($MergePay > 0) {
			$OrderInfo = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = {$uid} AND `id` = '{$OrderInfo['depend_id']}'");
			if (!isset($OrderInfo['t_online']) || intval($OrderInfo['t_online'] * 100) <= 0) {
				return ['__code' => 'ERROR_MONEY1', '__message' => '该订单无需在线支付', 'data' => null];
			}
			$money = $OrderInfo['t_online'];
		}
		// 不合并支付
		else {
			if (!isset($OrderInfo['c_online']) || intval($OrderInfo['c_online'] * 100) <= 0) {
				return ['__code' => 'ERROR_MONEY2', '__message' => '该订单无需在线支付', 'data' => null];
			}
			$money = $OrderInfo['c_online'];
		}

		$TotalFee = $money; // 价格，单位：元
		$Title = "阅米支付，订单:{$OrderInfo['id']}"; // 支付标题
		$TradeNo = $OrderInfo['id']; // 订单号
		$NonceStr = time() . "-" . rand(10000000, 99999999); // 随机数据
		$ReUrl = "https://a.yuemee.com/wpay.php?order_id={$OrderId}";
		$WeiXinPayment = new WeiXinPayment();
		$WxOrder = $WeiXinPayment->OrderCreate($ReUrl, $TotalFee, $TradeNo, $NonceStr, $Title);
		if (!isset($WxOrder['appid'])) {
			return ['__code' => 'error', '__message' => json_encode($WxOrder)];
		}

		// 组合返回HBuilder处理时能直接认出的数据（其实是调起微信所需的数据）
		//return ['__code' => 'OK','__message' => '', 'xxx'=>$WxOrder];
		$ReData = array();
		$ReData['appid'] = (string) $WxOrder['appid'];
		$ReData['partnerid'] = (string) $WxOrder['mch_id'];
		$ReData['prepayid'] = (string) $WxOrder['prepay_id'];
		$ReData['noncestr'] = (string) $WxOrder['nonce_str'];
		$ReData['package'] = (string) "Sign=WXPay";
		$ReData['timestamp'] = time();
		$ReData['sign'] = $WeiXinPayment->MakeSign($ReData);

		return ['__code' => 'OK', '__message' => '', 'data' => json_encode($ReData), 'xxx' => $WxOrder];
	}

	/**
	 * 生成微信订单信息（公众号支付）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	openid			string	用户在公众平台的OpenId
	 * @request	order_id		string	订单Id
	 * @request	is_merge_pay	int		是否合并支付：0不是(只支付当前订单)，1是（将该订单和其下的所有子订单合并支付）
	 */
	public function make_owx_gongzhonghao(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id; // 用户Id
		$OpenId = $request->body->openid; // 用户Id
		$OrderId = $request->body->order_id; // 订单Id
		$MergePay = intval($request->body->is_merge_pay); // 是否合并支付

		if (empty($OpenId)) {
			return ['__code' => 'E_ParamOpenId', '__message' => '用户OpenId错误，请联系技术人员'];
		}

		// 读取订单信息
		$OrderInfo = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = {$uid} AND `id` = '{$OrderId}'");
		if (!isset($OrderInfo['id'])) {
			return ['__code' => 'error', '__message' => "订单不存在"];
		}

		// 合并支付
		$money = 0;
		if ($MergePay > 0) {
			$OrderInfo = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = {$uid} AND `id` = '{$OrderInfo['depend_id']}'");
			if (!isset($OrderInfo['t_online']) || intval($OrderInfo['t_online'] * 100) <= 0) {
				return ['__code' => 'ERROR_MONEY1', '__message' => '该订单无需在线支付', 'data' => null];
			}
			$money = $OrderInfo['t_online'];
		}
		// 不合并支付
		else {
			if (!isset($OrderInfo['c_online']) || intval($OrderInfo['c_online'] * 100) <= 0) {
				return ['__code' => 'ERROR_MONEY2', '__message' => '该订单无需在线支付', 'data' => null];
			}
			$money = $OrderInfo['c_online'];
		}

		$TotalFee = $money; // 价格，单位：元
		$Title = "阅米支付，订单:{$OrderInfo['id']}"; // 支付标题
		$TradeNo = $OrderInfo['id']; // 订单号
		$NonceStr = time() . "-" . rand(10000000, 99999999); // 随机数据
		$ReUrl = "https://a.yuemee.com/wpay.php?order_id={$OrderId}";
		$WeiXinPayment = new WeiXinPayment();
		$WxOrder = $WeiXinPayment->OrderCreateWeiXin($ReUrl, $TotalFee, $TradeNo, $NonceStr, $OpenId, $Title);
		if (!isset($WxOrder['prepay_id'])) {
			return ['__code' => 'error', '__message' => "处理微信支付失败!"];
		}

		$SignArr['appId'] = WECHAT_APPID; // 公众号名称，由商户传入
		$SignArr['timeStamp'] = Z_NOW; // 时间戳，自1970年以来的秒数
		$SignArr['nonceStr'] = $NonceStr; // 随机串 
		$SignArr['package'] = "prepay_id=" . $WxOrder['prepay_id'];
		$SignArr['signType'] = "MD5"; // 微信签名方式：MD5
		$SignStr = $WeiXinPayment->MakeSign($SignArr);

		// 到此之前的代码，完全可以复制 make_order_weixin 方法里的代码，而且也应该保持代码的一致
		if (!isset($WxOrder['prepay_id'])) {
			return ['__code' => 'error', '__message' => json_encode($WxOrder)];
		}
		return ['__code' => 'OK', '__message' => '',
			'appId' => WECHAT_APPID,
			'timeStamp' => Z_NOW,
			'nonceStr' => $NonceStr,
			'prepay_id' => $WxOrder['prepay_id'],
			'sign' => $SignStr,
		];
	}

	/**
	 * 申请售后
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		order_id		string		订单Id
	 * @request		item_id			int			订单详情Id
	 * @request		qty				int			退款的数量
	 * @request		req_type		int			申请方式
	 * @request		req_reason		int			申请理由：参见文档
	 * @request		req_money		string		申请退款金额
	 * @request		req_message		string		申请消息
	 */
	public function apply_afs(\Ziima\MVC\REST\Request $request) {
		$order = \yuemi_sale\OrderFactory::Instance()->load($request->body->order_id);
		if ($order->status != 6 && $order->status != 7) {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '不可申请售后');
		}
		$orderitem = \yuemi_sale\OrderItemFactory::Instance()->load($request->body->item_id);
		if ($orderitem === null) {
			throw new \Ziima\MVC\REST\Exception('E_ITEM', '无订单详情');
		}
		if ($orderitem->qty < $request->body->qty) {
			throw new \Ziima\MVC\REST\Exception('E_QTY', '超出购买数量');
		}
		$orderasf = new \yuemi_sale\OrderAfsEntity();
		$orderasf->user_id = $this->User->id;
		$orderasf->item_id = $request->body->item_id;
		$orderasf->order_id = $request->body->order_id;
		$orderasf->shelf_id = $orderitem->shelf_id;
		$orderasf->sku_id = $orderitem->sku_id;
		$orderasf->spu_id = $orderitem->spu_id;
		$orderasf->supplier_id = $orderitem->supplier_id;
		$orderasf->qty = $request->body->qty;
		$orderasf->price = $orderitem->price;
		$orderasf->total = $orderitem->price * $request->body->qty;
		$orderasf->title = $orderitem->title;
		$orderasf->picture = $orderitem->picture;
		$orderasf->req_type = $request->body->req_type;
		$orderasf->req_reason = $request->body->req_reason;
		$orderasf->req_money = $request->body->req_money;
		$orderasf->req_message = $request->body->req_message;
		$orderasf->req_addr_id = $order->address_id;
		$orderasf->req_addr_rgn = $order->addr_region;
		$orderasf->req_addr = $order->addr_detail;
		$orderasf->req_name = $order->addr_name;
		$orderasf->req_mobile = $order->addr_mobile;
		$orderasf->create_time = Z_NOW;
		$orderasf->create_from = $this->Context->Runtime->ticket->ip;
		$orderasf->status = 0;
		$re = \yuemi_sale\OrderAfsFactory::Instance()->insert($orderasf);
		if (!$re) {
			return [
				'__code' => 'E_IN',
				'__message' => '申请失败'
			];
		}
		if ($order->status == 6) {
			$order->status = 21;
			$order->update_time = Z_NOW;
			\yuemi_sale\OrderFactory::Instance()->update($order);
		} elseif ($order->status == 7) {
			$order->status = 31;
			$order->update_time = Z_NOW;
			\yuemi_sale\OrderFactory::Instance()->update($order);
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 取消售后
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		afs_id			int			售后数据Id
	 */
	public function cancel_afs(\Ziima\MVC\REST\Request $request) {
		$afs = \yuemi_sale\OrderAfsFactory::Instance()->load($request->body->afs_id);
		$order = \yuemi_sale\OrderFactory::Instance()->load($afs->order_id);
		if ($afs->status != 0) {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '不可取消');
		}
		$re = \yuemi_sale\OrderAfsFactory::Instance()->delete($request->body->afs_id);
		if (!$re) {
			throw new \Ziima\MVC\REST\Exception('E_DEL', '取消失败');
		}
		if ($order->status > 30) {
			$order->status = 7;
		} else {
			$order->status = 6;
		}
		\yuemi_sale\OrderFactory::Instance()->update($order);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}
	
	
	/**
	 * 获取各状态订单数量
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		status			int			订单状态
	 */
	public function order_status_number(\Ziima\MVC\REST\Request $request){
		$uid = $this->User->id;
		$status = $request->body->status;
		$row = $this->MySQL->row("SELECT count(*) AS `sum` FROM `yuemi_sale`.`order` WHERE `status` = {$status} AND `user_id` = {$uid}");
		return [
			'sum' => $row['sum']
		];
	}

}
