<?php

include "lib/AdminHandler.php";
include_once Z_ROOT . '/Cloud/Kuaidi.php';
include_once Z_ROOT . '/Cloud/Neigou.php';
include_once Z_SITE . '/../../_base/WuLiu.php';
include_once Z_SITE . '/../../_base/StateMachine.php';
include_once Z_SITE . '/../../_base/WeiXinPayment.php';

/**
 * 订单管理
 * @auth
 */
class order_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 订单列表
	 * @param string $ActionName
	 * @param int $p 页码
	 * @param int $type 类型
	 * @param string $search_name 姓名
	 * @param string $search_mobile 手机号
	 * @param string $search_order_id 订单号
	 * @param string $search_trans_id 物流单号
	 */
	public function index(int $p = 0, int $type = 0, string $ActionName = '', string $search_name = '', string $search_mobile = '',
			string $search_order_id = '', string $search_trans_id = '', int $supplier_id = 0) {
		$whr = [];
		// 表名连接
		$sql = "SELECT O.*, U2.`name` AS `buy_user`,U2.`mobile` AS `buy_mobile`,U2.`id` AS `buy_id`, SP.`name` AS `supplier`, " .
				"R.province AS Province, R.city AS City, R.country AS Country " .
				"FROM yuemi_sale.`order` AS O " . // O 订单信息
				"LEFT JOIN yuemi_main.region AS R ON O.addr_region = R.id " . // R 地区信息
				"LEFT JOIN yuemi_main.`user` AS U2 ON O.`user_id` = U2.`id` " . // U2 购买者用户信息
				"LEFT JOIN yuemi_main.`supplier` AS SP ON SP.`id` = O.`supplier_id` " // SP 供应商信息
		;

		// 查询条件，搜索相关参数
		$search_time_start = strtotime($_GET['search_time_start'] ?? ""); // 开始时间
		$search_time_end = strtotime($_GET['search_time_end'] ?? ""); // 结束时间
		if ($search_time_start > 0)
			$whr[] = " O.create_time >= {$search_time_start} ";
		if ($search_time_end > 0)
			$whr[] = " O.create_time <= {$search_time_end} ";

		$search_order_id = trim($search_order_id); // 订单号
		$search_trans_id = trim($search_trans_id); // 物流单号
		if (!empty($search_order_id))
			$whr[] = " (O.id LIKE '%{$search_order_id}%' || O.depend_id LIKE '%{$search_order_id}%') ";
		if (!empty($search_trans_id))
			$whr[] = " O.trans_id LIKE '%{$search_trans_id}%' ";
		if ($supplier_id > 0)
			$whr[] = " `O`.`supplier_id` = {$supplier_id} ";

		$search_name = trim($search_name); // 姓名
		$search_mobile = trim($search_mobile); // 手机号
		if (!empty($search_name))
			$whr[] = " (U2.name LIKE '%{$search_name}%' || O.addr_name LIKE '%{$search_name}%') ";
		if (!empty($search_mobile))
			$whr[] = " (U2.mobile LIKE '%{$search_mobile}%' || O.addr_mobile LIKE '%{$search_mobile}%') ";


		// 查询条件 status
		switch ($type) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
			case 11:
			case 13:
			case 14: $whr[] = " O.`status` = {$type} ";
				break;
			case 20: $whr[] = " O.`status` = 21 OR O.`status` = 22 OR O.`status` = 23 OR O.`status` = 24 OR O.`status` = 25 OR O.`status` = 31 OR O.`status` = 32 OR O.`status` = 33 OR O.`status` = 34 OR O.`status` = 35 ";
				break;
			case 21: $whr[] = "O.`status` = 16 OR O.`status` = 17 OR O.`status` = 18";
				break;
			case 22: $whr[] = " O.`status` = 11 OR O.`status` = 12 ";
				break;
			case 23: $whr[] = "O.`status` = 13 OR O.`status` = 14 OR O.`status` = 15";
				break;
			default: break;
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= "ORDER BY O.`create_time` DESC";

		// 读取数据，并处理转换
		$DataList = $this->MySQL->paging($sql, 10, $p);
		$StatusList = StateMachine::order();

		foreach ($DataList->Data AS $Key => $Data) {
			// 商品列表
			$sql = "SELECT OI.*, " .
					"TI.`ticket_id` AS `tiid`, TI.`price` AS `tiprice`, " .
					"SK.`title` AS SkuName, SK.`specs` AS specs, SK.`title` AS `sktit`, SK.`specs` AS `specs`, " .
					// "SP.`title` AS SpuName, SP.`title` AS `sptit`, " .
					" CASE left(SK.serial,2)
						WHEN 'JD' THEN '京东'
						WHEN 'YX' THEN '严选'
						ELSE SU.`name`
					   END AS SupplierName," .
//					"SU.`name` AS `SupplierName`, " .
					"CAT.`name` AS `CatagoryName` " .
					"FROM yuemi_sale.`order_item` AS OI " . // OI (order_item)
					"LEFT JOIN yuemi_sale.`sku` AS SK ON OI.`sku_id` = SK.`id` " . // SK (sku)
					// "LEFT JOIN yuemi_sale.`spu` AS SP ON SP.`id` = OI.`spu_id` " . // SP (spu)
					"LEFT JOIN `yuemi_main`.`supplier` AS SU ON OI.`supplier_id` = SU.`id` " . // SU (supplier)
					"LEFT JOIN yuemi_sale.`catagory` AS CAT ON CAT.id = OI.`catagory_id` " . // CAT (catagory)
					"LEFT JOIN yuemi_sale.`order_ticket` AS TI ON TI.`order_id` = OI.`order_id`" . // TI (order_ticket)
					"WHERE OI.`order_id` = '{$Data['id']}'";

			$ItemList = $this->MySQL->grid($sql);
//			var_dump($ItemList);die;
			// 数据转换、处理
			$DataList->Data[$Key]['order_status'] = $DataList->Data[$Key]['status'];
			$DataList->Data[$Key]['status'] = $StatusList[$DataList->Data[$Key]['status']];
			$DataList->Data[$Key]['ItemList'] = $ItemList; // 商品列表
		}
		// 导出数据
		if ($ActionName == "按搜索条件导出订单") {
			$str = '';
			$str .= '供应商' . ',' . '创建时间' . ',' . '订单号' . ',' . '商品名称' . ',' . '商品价格' . ',' . '购买数量' . ',' . '订单余额' . ',' . '收货人所在省' . ',' . '收货人所在市' . ',' . '收货人所在区/县' . ',' . '收货人详细地址' . ',' . '收货人' . ',' . '收货人电话' . ',' . '支付状态' . ',' . '物流单号' . ',' . '物流公司' . "\r\n";

			foreach ($DataList->Data as $val) {
				$Platofrm = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN);
				$redata = $Platofrm->info($val['trans_id']); // 订单号查询快递公司

				$s = "select name from  `yuemi_main`.`kuaidi` where alias='$redata[1]'";

				$result = $this->MySQL->row($s);
				if (empty($result)) {
					$result['name'] = '';
				}
				$time = $val['create_time'];
				$str .= $val['supplier'] . ',' . ' ' . date("Y-m-d h:i:s", $time) . ',' . $val['depend_id'] . ',';
				if (count($val['ItemList']) > 1) {
					$i = 0;
					foreach ($val['ItemList'] as $v) {
						$i == 1;
						if ($i == 0) {
							$str .= $v['SkuName'] . ',' . ' ' . $v['price'] . ',' . ' ' . $v['qty'];
							$str .= ',' . $val['t_amount'] . ',' . $val['Province'] . ',' . $val['City'] . ',' . $val['Country'] . ',' . $val['addr_detail'] . ',' . $val['addr_name'] . ',' . $val['addr_mobile'] . ',' . $val['status'] . ',' . $val['trans_id'] . ',' . $result['name'] . "\r\n";
						} else {
							$str .= $val['supplier'] . ',' . ' ' . date("Y-m-d h:i:s", $time) . ',' . $val['depend_id'] . ',';
							$str .= $v['SkuName'] . ',' . ' ' . $v['price'] . ',' . ' ' . $v['qty'];
							$str .= ',' . $val['t_amount'] . ',' . $val['Province'] . ',' . $val['City'] . ',' . $val['Country'] . ',' . $val['addr_detail'] . ',' . $val['addr_name'] . ',' . $val['addr_mobile'] . ',' . $val['status'] . ',' . $val['trans_id'] . ',' . $result['name'] . "\r\n";
						}
						$i ++;
					}
				} else {
					foreach ($val['ItemList'] as $v) {
						$str .= $v['SkuName'] . ',' . ' ' . $v['price'] . ',' . ' ' . $v['qty'];
					}
					$str .= ',' . $val['t_amount'] . ',' . $val['Province'] . ',' . $val['City'] . ',' . $val['Country'] . ',' . $val['addr_detail'] . ',' . $val['addr_name'] . ',' . $val['addr_mobile'] . ',' . $val['status'] . ',' . $val['trans_id'] . ',' . $result['name'] . "\r\n";
				}
			}
			$filename = date('Y-m-d', Z_NOW) . '.' . 'csv';
			header("Content-Type: application/vnd.ms-excel;");
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename=' . $filename);
			$filename = iconv("utf-8", "gb2312//IGNORE", $str);
			echo $filename;
			die;
		}

		// 处理状态列表
		foreach ($StatusList AS $Key => $Val) {
			if ($Key < 1) {
				unset($StatusList[$Key]);
			}
		}
		$StatusList[0] = "全部订单";
		$supplier = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`supplier`");

		foreach ($DataList->Data as $k => $v) {
			if ($v['City'] == null) {
				$Region_id = $v['addr_region'];
				$newstr = substr($Region_id, 0, strlen($Region_id) - 1);
				$newli = $this->MySQL->row("SELECT * FROM `yuemi_main`.`region` WHERE `id` LIKE '{$newstr}%'");
				$DataList->Data[$k]['Province'] = $newli['province'];
				$DataList->Data[$k]['City'] = $newli['city'];
			}
		}
		return ['DataList' => $DataList, 'StatusList' => $StatusList,
			'search_time_start' => $search_time_start, 'search_time_end' => $search_time_end, 'supplier' => $supplier];
	}

	/**
	 * 订单确认
	 * @param string $id
	 */
	public function check(string $id, int $type = 0) {
		$this->MySQL->execute("UPDATE yuemi_sale.`order` SET `status` = 4 WHERE `id` = '{$id}'");
		throw new \Ziima\MVC\Redirector("/index.php?call=order.index&type={$type}");
	}

	/**
	 * 订单详情
	 * @param string $id
	 */
	public function detail(string $id = '', int $p = 0, int $type = 0) {
		$WeiXinPayment = new WeiXinPayment();
		$OrderInfo = $this->MySQL->row("SELECT * FROM yuemi_sale.`order` WHERE id = '{$id}'");

		// 内购订单信息
		if ($OrderInfo['supplier_id'] == 2 && !empty($OrderInfo['ext_order_id'])) {
			$NeiGou = new \Cloud\NeiGou(NG_URL_BASE, NG_CLIENTID, NG_SECRET, Z_SITE . '/data/NeiGou/');
			$NeiGouInfo = $NeiGou->order_info($OrderInfo['ext_order_id']);
			if (isset($NeiGouInfo['Data']['order_id'])) {
				$OrderInfo['NeiGouInfo'] = $NeiGouInfo['Data'];
			}
			// 更新订单信息、物流信息
			if (isset($NeiGouInfo['Data']['order_id'])) {
				$NeiGouInfo = $NeiGouInfo['Data'];
				$UpdateSet = "ext_order_id={$NeiGouInfo['order_id']}";
				if (isset($OrderInfo['NeiGouInfo']['logi_code']) && !empty($NeiGouInfo['logi_code'])) {
					$KdComCode = $NeiGou->comcode_to_kuaidi100($NeiGouInfo['logi_code']);
					$UpdateSet .= ",trans_com='{$KdComCode}'";
					$UpdateSet .= ",trans_id='{$NeiGouInfo['logi_no']}'";
				}
				$this->MySQL->execute("UPDATE yuemi_sale.`order` SET {$UpdateSet} WHERE id = '{$OrderInfo['id']}'");
			}
		}

		// 地区信息
		$OrderInfo['Region'] = $this->MySQL->row("SELECT * FROM yuemi_main.region WHERE id = '{$OrderInfo['addr_region']}'");

		// 物流信息
		$OrderInfo['KuaiDi'] = "";
		if (!empty($OrderInfo['trans_com']) && !empty($OrderInfo['trans_id'])) {
			$Kd = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN);
			$ReData = $Kd->trace($OrderInfo['trans_com'], $OrderInfo['trans_id']);
			if (isset($ReData['data']) && is_array($ReData['data']) && count($ReData['data']) > 0) {
				$ReStr = null;
				foreach ($ReData['data'] AS $val) {
					$ReStr .= "{$val['time']} {$val['context']}<br />\n";
				}
				$OrderInfo['KuaiDi'] = $ReStr;
				$this->MySQL->execute("UPDATE yuemi_sale.`order` SET trans_trace = '{$ReStr}' WHERE id = '{$id}'");
			}
		}
		return ['data' => $OrderInfo];
	}

	/**
	 * 添加快递单号
	 * @param int $id
	 */
	public function add_ordernumber(string $id = '') {
		if ($this->Context->Runtime->ticket->postback) {
			$tr = (int) $_POST['trans_id'];
			$id = $this->MySQL->encode($_POST['id']);
			$time = time();
			$this->MySQL->execute("UPDATE yuemi_sale.`order` SET `trans_id` = '{$tr}',`update_time` = {$time} WHERE id = '{$id}'");
			throw new \Ziima\MVC\Redirector('/index.php?call=order.index');
		}
		$res = $this->MySQL->row("SELECT `trans_id`,`id` FROM yuemi_sale.`order` WHERE `id` = '{$id}'");
		return [
			'res' => $res
		];
	}

	public function logistics(string $id = '', int $p = 0, int $type = 0) {
		$whr = [];
		if ($id !== '') {
			$whr[] = "depend_id = '{$id}'";
		}
		$sql = "SELECT O.*,U2.`name` AS `buy_user`,SP.`name` AS `supplier` " .
				"FROM yuemi_sale.`order` AS `O` " .
				"LEFT JOIN `yuemi_main`.`user` AS U2 ON O.`user_id` = U2.`id` " .
				"LEFT JOIN `yuemi_main`.`supplier` AS SP ON SP.`id` = O.`supplier_id` ";
		if ($type > 0) {
			switch ($type) {
				case 1:
					$whr[] = " O.`status` = 0 OR O.`status` = 1 ";

					break;
				case 2:
					$whr[] = " O.`status` = 2 OR O.`status` = 4 ";

					break;
				case 3:
					$whr[] = " O.`status` = 5 ";

					break;
				case 4:
					$whr[] = "O.`status` = 6 OR O.`status` = 7";

					break;
				case 5:
					$whr[] = " O.`status` = 21 OR O.`status` = 22 OR O.`status` = 23 OR O.`status` = 24 OR O.`status` = 25 OR O.`status` = 31 OR O.`status` = 32 OR O.`status` = 33 OR O.`status` = 34 OR O.`status` = 35 ";

					break;
				case 6:
					$whr[] = "O.`status` = 16 OR O.`status` = 17 OR O.`status` = 18";

					break;
				case 7:
					$whr[] = " O.`status` = 11 OR O.`status` = 12 ";

					break;
				case 8:
					$whr[] = "O.`status` = 13 OR O.`status` = 14 OR O.`status` = 15";

					break;

				default:
					break;
			}
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$res = $this->MySQL->paging($sql, 30, $p);
		for ($i = 0; $i < count($res->Data); $i++) {
			$res->Data[$i]['create_time'] = date('Y-m-d H:i:s', $res->Data[$i]['create_time']);
			$res->Data[$i]['update_time'] = date('Y-m-d H:i:s', $res->Data[$i]['update_time']);
			$res->Data[$i]['pay_time'] = date('Y-m-d H:i:s', $res->Data[$i]['pay_time']);
			$res->Data[$i]['trans_time'] = date('Y-m-d H:i:s', $res->Data[$i]['trans_time']);
			switch ($res->Data[$i]['status']) {
				case -1:
					$res->Data[$i]['status'] = "购物车";
					break;
				case 0:
					$res->Data[$i]['status'] = "新订单";
					break;
				case 1:
					$res->Data[$i]['status'] = "待支付";
					break;
				case 2:
					$res->Data[$i]['status'] = "已支付";
					break;
				case 3:
					$res->Data[$i]['status'] = "未知";
					break;
				case 4:
					$res->Data[$i]['status'] = "待发货";
					break;
				case 5:
					$res->Data[$i]['status'] = "运输中";
					break;
				case 6:
					$res->Data[$i]['status'] = "已签收";
					break;
				case 7:
					$res->Data[$i]['status'] = "已确认";
					break;
				case 8:
					$res->Data[$i]['status'] = "已评价";
					break;
				case 11:
					$res->Data[$i]['status'] = "主动关闭";
					break;
				case 12:
					$res->Data[$i]['status'] = "后台关闭";
					break;
				case 13:
					$res->Data[$i]['status'] = "退款关闭";
					break;
				case 14:
					$res->Data[$i]['status'] = "后台取消";
					break;
				case 15:
					$res->Data[$i]['status'] = "供应商关闭";
					break;
				case 16:
					$res->Data[$i]['status'] = "物流丢件";
					break;
				case 17:
					$res->Data[$i]['status'] = "丢件确认";
					break;
				case 18:
					$res->Data[$i]['status'] = "丢件退款";
					break;
				case 21:
					$res->Data[$i]['status'] = "售后申请";
					break;
				case 22:
					$res->Data[$i]['status'] = "同意退货";
					break;
				case 23:
					$res->Data[$i]['status'] = "拒绝退货";
					break;
				case 24:
					$res->Data[$i]['status'] = "售后完成";
					break;
				case 25:
					$res->Data[$i]['status'] = "售后评价";
					break;
				case 31:
					$res->Data[$i]['status'] = "售后申请";
					break;
				case 32:
					$res->Data[$i]['status'] = "同意退货";
					break;
				case 33:
					$res->Data[$i]['status'] = "拒绝退货";
					break;
				case 34:
					$res->Data[$i]['status'] = "售后完成";
					break;
				case 35:
					$res->Data[$i]['status'] = "售后评价";
					break;
				default :
					$res->Data[$i]['status'] = "未知";
			}
		}
		return [
			'res' => $res,
			'type' => $type
		];
	}

	public function cancel() {
		
	}

	public function comment() {
		
	}

	/**
	 * 关闭订单
	 * @param string $id
	 * @param int $type
	 * @throws \Ziima\MVC\Redirector
	 */
	public function off(string $id, int $type = 0, int $time = 0) {
		$data = \yuemi_sale\ProcedureInvoker::Instance()->close_order($id, $this->Context->Runtime->ticket->ip);
//		if (!empty($data) || $data->ReturnValue == 'OK')
//		{
//			if ($time > 0) {
//				$list = $this->MySQL->row("SELECT `pay_time` FROM yuemi_sale.`order` WHERE `id` = '{$id}' ");
//
//				if ($list) {
//					$time = $list['pay_time'];
//					$now = time();
//					$dif = $now - $time;
//					if ($dif > 900) {
//						throw new \Ziima\MVC\Redirector("/index.php?call=order.index&type=$type");
//					}
//				} else {
//					$this->MySQL->execute("UPDATE yuemi_sale.`order` SET `status` = 12 WHERE `id` = '{$id}'");
//					throw new \Ziima\MVC\Redirector("/index.php?call=order.index&type=$type");
//				}
//			}
//
//			$this->MySQL->execute("UPDATE yuemi_sale.`order` SET `status` = 12 WHERE `id` = '{$id}'");
//			throw new \Ziima\MVC\Redirector("/index.php?call=order.index&type=$type");
//		}
		throw new \Ziima\MVC\Redirector("/index.php?call=order.index&type=$type");
	}

	/**
	 * 订单售后
	 * @param string $id
	 */
	public function afsinfo(string $id = '', string $item = '', int $p = 0) {
		if ($id !== '') {
			$row = $this->MySQL->paging("SELECT `os`.*,`u`.`name` AS `username`,SU.`name` AS `supplier` FROM yuemi_sale.`order_afs`  AS `os` " .
					"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `os`.`user_id` " .
					"LEFT JOIN `yuemi_main`.`supplier` AS SU ON SU.`id` = `os`.`supplier_id` " .
					"WHERE `os`.`order_id` = '{$id}'", 30, $p);
		}
		if ($item !== '') {
			$row = $this->MySQL->paging("SELECT `os`.*,`u`.`name` AS `username`,SU.`name` AS `supplier` FROM yuemi_sale.`order_afs`  AS `os` " .
					"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `os`.`user_id` " .
					"LEFT JOIN `yuemi_main`.`supplier` AS SU ON SU.`id` = `os`.`supplier_id` " .
					"WHERE `os`.`item_id` = '{$item}'", 30, $p);
		}

		return [
			'res' => $row
		];
	}

	/**
	 * 售后补发
	 * @param string $id
	 */
	public function regive(string $id = '') {
		if ($this->Context->Runtime->ticket->postback) {
			var_dump($_POST);
			die;
		}
		$row = $this->MySQL->row("SELECT * FROM yuemi_sale.`order_afs` WHERE `id` = '{$id}'");
		return [
			'res' => $row
		];
	}

	/**
	 * 自发礼包列表
	 * @param int $p
	 */
	public function gift(int $p = 0, $m = '') {
		$sql = "SELECT `oi`.`sku_id`,`u`.`name` AS `buyname`,`u`.`level_v`,`u`.`level_c`,`u`.`level_d`,`k`.`name` AS `kuaidi_name`,`o`.`comment_user`,`o`.`create_time`,`o`.`id` AS `order_id`,`oi`.`title`,`oi`.`qty`,`o`.`addr_region`,`r`.`province`,`r`.`city`,`r`.`country`,`o`.`addr_detail`,`o`.`addr_name`,`o`.`addr_mobile`,`o`.`trans_com`,`o`.`trans_id`,`o`.`user_id` FROM " .
				"`yuemi_sale`.`order` AS `o` " .
				"LEFT JOIN `yuemi_sale`.`order_item` AS `oi` ON `oi`.`order_id` = `o`.`id` " .
				"LEFT JOIN `yuemi_main`.`region` AS `r` ON `r`.`id` = `o`.`addr_region` " .
				"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `o`.`user_id` LEFT JOIN `yuemi_main`.`kuaidi` AS `k` ON `k`.`alias` = `o`.`trans_com`" .
				" LEFT JOIN `yuemi_main`.`vip` AS `v` ON `v`.`user_id` = `o`.`user_id` " .
				" LEFT JOIN `yuemi_main`.`cheif` AS `c` ON `c`.`user_id` = `v`.`cheif_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `uye` ON `uye`.`id` = `c`.`director_id` " .
				" WHERE `o`.`type` = 1 ";
		if ($m !== '') {
			$sql .= " AND `uye`.`mobile` LIKE '%{$m}%' ";
		}

		$res = $this->MySQL->paging($sql, 30, $p);
		foreach ($res->Data as $k => $v) {
			$pic = $this->MySQL->scalar("SELECT `file_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$v['sku_id']}");
			$res->Data[$k]['pic'] = $pic;
			$arr = [];
			$if = 0;
			if ($v['level_v'] + $v['level_c'] + $v['level_d'] == 0) {
				//普通用户
				$row = $this->MySQL->row("SELECT `u`.`name` AS `uname`,`vu`.`name` AS `vname`,`cu`.`name` AS `cname`,`du`.`name` AS `dname` FROM `yuemi_main`.`user` AS `u` " .
						"LEFT JOIN `yuemi_main`.`user` AS `vu` ON `vu`.`id` = `u`.`invitor_id`" .
						" LEFT JOIN `yuemi_main`.`vip` AS `v` ON `v`.`user_id` = `vu`.`invitor_id` " .
						"LEFT JOIN `yuemi_main`.`user` AS `cu` ON `cu`.`id` = `v`.`cheif_id` " .
						"LEFT JOIN `yuemi_main`.`cheif` AS `c` ON `c`.`user_id` = `v`.`cheif_id` LEFT JOIN `yuemi_main`.`user` AS `du` ON `du`.`id` = `c`.`director_id` WHERE `u`.`id` = {$v['user_id']}");
				if (!empty($row)) {
					$res->Data[$k]['vname'] = $row['vname'];
					$res->Data[$k]['cname'] = $row['cname'];
					$res->Data[$k]['dname'] = $row['dname'];
				} else {
					$res->Data[$k]['vname'] = '';
					$res->Data[$k]['cname'] = '';
					$res->Data[$k]['dname'] = '';
				}
				$res->Data[$k]['body'] = 'u';
				$if = 1;
			}
			if ($v['level_d'] > 0 && $if == 0) {
				//总经理
				$res->Data[$k]['vname'] = '';
				$res->Data[$k]['cname'] = '';
				$res->Data[$k]['dname'] = '';
				$res->Data[$k]['body'] = 'd';
				$if = 1;
			}
			if ($v['level_c'] > 0 && $if == 0) {
				//总监
				$row = $this->MySQL->row("SELECT `u`.`name` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`director_id` WHERE `c`.`user_id` = {$v['user_id']}");
				$res->Data[$k]['vname'] = '';
				$res->Data[$k]['cname'] = '';
				if (!empty($row)) {
					$res->Data[$k]['dname'] = $row['name'];
				} else {
					$res->Data[$k]['dname'] = '';
				}
				$res->Data[$k]['body'] = 'c';
				$if = 1;
			}
			if ($v['level_v'] > 0 && $if == 0) {
				//VIP
				$crow = $this->MySQL->row("SELECT `u`.`name` AS `cname`,`u`.`id` AS `cid` FROM `yuemi_main`.`vip` AS `v` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `v`.`cheif_id` WHERE `v`.`user_id` = {$v['user_id']}");
				if (!empty($crow)) {
					$drow = $this->MySQL->row("SELECT `u`.`name` AS `dname` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`director_id` WHERE `c`.`user_id` = {$crow['cid']}");
					$res->Data[$k]['vname'] = '';
					$res->Data[$k]['cname'] = $crow['cname'];
					if (!empty($drow)) {
						$res->Data[$k]['dname'] = $drow['dname'];
					} else {
						$res->Data[$k]['dname'] = '';
					}
				} else {
					$res->Data[$k]['vname'] = '';
					$res->Data[$k]['cname'] = '';
					$res->Data[$k]['dname'] = '';
				}

				$res->Data[$k]['body'] = 'v';
				$if = 1;
			}
		}
		return [
			'res' => $res
		];
	}

}
