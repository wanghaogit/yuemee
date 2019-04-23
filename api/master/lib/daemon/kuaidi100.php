<?php
/**
 * 快递100定时任务，建议每60分钟执行一次
 * http://a.ym.cn/lib/daemon/kuaidi100.php
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include dirname(__FILE__) . '/../../../../_base/WuLiu.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_ROOT . '/Cloud/Neigou.php';
$MySql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);

// 处理已发货订单
$sql = "SELECT * FROM `yuemi_sale`.`order` WHERE status = 5"; // 读取已发货订单,订单状态只能是5时候改变状态
$OrderList = $MySql->grid($sql);
$KuaiDi100 = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN); // 从物流公司拿到订单状态数据
foreach($OrderList as $OrderInfo)
{
	// 内购信息（从内购拿到物流单号）
	if (isset($OrderInfo['supplier_id']) && $OrderInfo['supplier_id'] == 2 && !empty($OrderInfo['ext_order_id'])) {
		$NeiGou = new \Cloud\NeiGou(NG_URL_BASE, NG_CLIENTID, NG_SECRET, Z_SITE.'/data/NeiGou/');
		$NeiGouInfo = $NeiGou->order_info($OrderInfo['ext_order_id']);
		if (isset($NeiGouInfo['Data']['order_id'])) 
		{
			$UpdateSet = null;
			if (empty($OrderInfo['ext_order_id'])) {
				$UpdateSet .= "ext_order_id={$NeiGouInfo['Data']['order_id']},";
			}
			if (empty($OrderInfo['trans_com']) && isset($NeiGouInfo['Data']['logi_code']) && !empty($NeiGouInfo['Data']['logi_code'])) {
				$KdComCode = $NeiGou->comcode_to_kuaidi100($NeiGouInfo['Data']['logi_code']);
				$UpdateSet .= "trans_com='{$KdComCode}',";
				$UpdateSet .= "trans_id='{$NeiGouInfo['Data']['logi_no']}',";
			}
			$UpdateSet = trim($UpdateSet, ",");
			if (!empty($UpdateSet)) {
				$MySql->execute("UPDATE yuemi_sale.`order` SET {$UpdateSet} WHERE id = '{$OrderInfo['id']}'");
			}
		}
	}

	// 快递信息
	$ReData = $KuaiDi100->trace($OrderInfo['trans_com'], $OrderInfo['trans_id']);

	// 更新物流信息
	if (isset($ReData['data'])) {
		$TransTrace = null;
		foreach ($ReData['data'] AS $val) {
			$TransTrace .= "{$val['time']} {$val['context']}<br />";
		}
		$MySql->execute("UPDATE `yuemi_sale`.`order` SET trans_trace = '{$TransTrace}' WHERE id = '{$OrderInfo['id']}'");
	}
	
	// 设为已收货(状态6)
	if (isset($ReData['state']) && $ReData['state'] == 3) {
		$Sql = "UPDATE `yuemi_sale`.`order` SET status = 6, update_time = " . Z_NOW . " WHERE id = '{$OrderInfo['id']}' AND status = 5";
		$MySql->execute($Sql);
	}
}
