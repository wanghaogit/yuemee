<?php
/**
 * 订单定时任务(内购下单)，建议每分钟执行一次
 * http://a.ym.cn/lib/daemon/order_neigou.php
 * /opt/php/bin/php /data/web/api/lib/daemon/order_neigou.php
 */
define("DIR_ROOT", dirname(__FILE__) . '/../../..');
define("DIR_CACHE", DIR_ROOT . '/data/order/neigou_cron');

include dirname(__FILE__) . '/../../../../_base/config.php';
include dirname(__FILE__) . '/../../../../_base/WeiXinPayment.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_ROOT . '/Cloud/Neigou.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';

// 初始化
$MySql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$NeiGou = new \Cloud\NeiGou(NG_URL_BASE, NG_CLIENTID, NG_SECRET, DIR_CACHE);
$Redis = new \Redis();
$Redis->connect(REDIS_HOST, REDIS_PORT);
if(!empty(REDIS_AUTH)){
	$Redis->auth(REDIS_AUTH);
}
$Redis->select(5);
$Redis->setex("OrderNeiGouLastTime", time()+86400*30, date("Y-m-d H:i:s"));
// 已支付订单，内购商品由此下单
$Sql = "SELECT * FROM yuemi_sale.`order` WHERE supplier_id = 2 AND `status` = 4";
$OrderList = $MySql->grid($Sql);
foreach ($OrderList AS $OrderInfo)
{
   
	echo "\n**************************************** 订单号 {$OrderInfo['id']} **************************************** \n";
	// 判断是否已处理，如果未处理则进行并记录
	//$CacheTime = $Redis->hGet("OrderNeiGou", $OrderInfo['id']);
	//if (!empty($CacheTime)) {
	//	echo "已处理 - 订单号：{$OrderInfo['id']}\n"; 
	//	continue;
	//}
	//$RedisWrite = $Redis->hSet("OrderNeiGou", $OrderInfo['id'], time());
	//if (!$RedisWrite) {
	//	echo "Redis出错 - 订单号：{$OrderInfo['id']}\n";
	//	continue;
	//}

	// 获取联系地址信息
	$RegInfo = $MySql->row("SELECT * FROM yuemi_main.region WHERE id = '{$OrderInfo['addr_region']}'");
	if (!isset($RegInfo['id']) || $RegInfo['id'] < 1) {
		continue;
	}

	
	// 获取商品列表
	$Sql = "SELECT OI.qty, ES.* FROM yuemi_sale.`order_item` AS OI
				INNER JOIN yuemi_sale.sku AS S ON OI.sku_id = S.id
				INNER JOIN yuemi_sale.ext_sku AS ES ON S.id = ES.sku_id
			WHERE order_id = '{$OrderInfo['id']}'";
	$SkuList = $MySql->grid($Sql);
	if (!is_array($SkuList) || count($SkuList) < 1) {
		continue;
	}
	// 往内购下单
	$OrderId = $OrderInfo['id']; // 内部订单号
	$Name = $OrderInfo['addr_name']; // 姓名
	$Mobile = $OrderInfo['addr_mobile']; // 收货人手机号
	$ProvinceId = $NeiGou->region_name_to_id($RegInfo['province'], 0); // 省
	$CityId = $NeiGou->region_name_to_id($RegInfo['city'], $ProvinceId); // 市
	$CountyId = $NeiGou->region_name_to_id($RegInfo['country'], $CityId); // 县/区
	$AddrDetail = $OrderInfo['addr_detail']; // 详细地址
	$GoodsList = array(); // 商品列表
	foreach ($SkuList AS $SkuInfo) {
		$GoodsList[] = array('bn' => $SkuInfo['bn'], 'nums' => $SkuInfo['qty']);
	}
	$NgOrderInfo = $NeiGou->order_create($OrderId, $Name, $Mobile, $ProvinceId, $CityId, $CountyId, $AddrDetail, $GoodsList);
	echo "{$OrderInfo['addr_name']} {$OrderInfo['addr_mobile']}\n";
	//print_r($NgOrderInfo);
	
	// 验证返回的结果
	if (!isset($NgOrderInfo['Data']['order_id'])) {
		$Redis->hSet("OrderNeiGou", $OrderInfo['id'], "内购下单失败\n");
		continue;
	}
	if (isset($NgOrderInfo['Data']['ErrorId']) && intval($NgOrderInfo['Data']['ErrorId']) > 0) {
		//$Redis->hSet("OrderNeiGou", $OrderInfo['id'], "{$NgOrderInfo['Data']['ErrorMsg']}\n");
		if (isset($NgOrderInfo['Data']['order_id']) && empty($OrderInfo['ext_order_id'])) {
			$MySql->execute("UPDATE yuemi_sale.`order` SET ext_order_id='{$NgOrderInfo['Data']['order_id']}', `status` = 5 WHERE id = '{$OrderId}'");
			echo "重复下单，更新外部订单号";
		}
		continue;
	}

	// 组合数据
	$Data['SkuList'] = $SkuList;
	$Data['NgOrderInfo'] = $NgOrderInfo;
	$Data['GoodsList'] = $GoodsList;

	// 存储订单号，更新状态
	$SaveStatus = $MySql->execute("UPDATE yuemi_sale.`order` SET ext_order_id='{$NgOrderInfo['Data']['order_id']}', `status` = 5, `update_time` = UNIX_TIMESTAMP() WHERE id = '{$OrderId}'");
	if ($SaveStatus) {
		$Data['Result'] = "状态处理成功";
		// $RedisWrite = $Redis->hSet("OrderNeiGou", $OrderInfo['id'], json_encode($Data)."\n"); // 成功订单是否记录再考虑
	} else {
		$Data['Result'] = "阅米 yuemi_sale.order 更新状态失败";
		$RedisWrite = $Redis->hSet("OrderNeiGou", $OrderInfo['id'], json_encode($Data)."\n"); // 失败订单一定要记录
	}

	
	return ['__code' => 'OK', '__message' => '', 'NgOrderId' => $NgOrderInfo['Data']['order_id']];
}
