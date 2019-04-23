<?php
/**
 * 微信对帐单：补回单号、更新状态、建议60分钟执行一次
 * 1、从API中拿到N天内的成功订单列表
 * 2、判断阅米订单，如果未记录《支付回单号》则进行记录
 * 3、记录对账单
 * http://a.ym.cn/lib/daemon/bill_wx.php
 */
include dirname(__FILE__) . '/bill_base.php';
$MySql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);

for ($i = 0; $i < 7; $i ++)
{
	$time = time() - 86400 * $i;
	exe(date("Ymd", $time));
}

/**
 * 执行逻辑
 * @global \Ziima\Data\MySQLConnection $MySql
 * @param type $Date	订单日期
 */
function exe($Date)
{
	global $MySql;
	echo "**************************************** {$Date} **************************************** \n";

	// 下载对账单
	$WeiXinPayment = new WeiXinPayment();
	$ReData = $WeiXinPayment->DownloadBill($Date);

	// 订单数据
	$DataCount = 0;
	if (isset($ReData['count'][1][0]) && intval($ReData['count'][1][0]) > 0) {
		$DataCount = intval($ReData['count'][1][0]);
	}
	if ($DataCount < 1) {
		echo "无记录\n";
		return;
	}
	echo "数据量：{$DataCount}\n";

	// 处理数据
	$DataList = $ReData['list'];
	foreach ($DataList AS $Key => $Data)
	{
		if ($Key < 1) {
			continue;
		}
		save($Data);
	}
}

/**
 * 数据存储
 * @global \Ziima\Data\MySQLConnection $MySql
 * @param type $Data	从API返回的微信订单详情数据
 */
function save($Data)
{
	global $MySql;
	$TableName = "yuemi_sale.bill_ext";
	if (!is_array($Data) || count($Data) < 10) {
		echo "对帐单数据错误 \n";
		return;
	}
	echo "微信订单号: {$Data[5]} 阅米订单号: {$Data[6]} ";
	
	$WxP = $Data[12] * 100; // 微信实际支付金额(分)
	$OrderInfo = $MySql->row("SELECT * FROM yuemi_sale.`order` WHERE id = '{$Data[6]}'");
	if (!isset($OrderInfo['id']) || empty($OrderInfo['id'])) {
		echo "订单信息读取错误 \n";
		return;
	}
	if (!empty($OrderInfo['pay_serial'])) {
		echo "支付回单号已存在 \n";
		$MySql->execute("UPDATE yuemi_sale.spread_userinfo SET `status` = 1 WHERE order_id = '{$OrderInfo['id']}'");
		return;
	}

	// 订单群支付
	if (abs($WxP-$OrderInfo['t_online']*100) < 1) {
		$MySql->execute("UPDATE yuemi_sale.`order` SET pay_serial = '{$Data[5]}', `update_time` = UNIX_TIMESTAMP(), status = 2 WHERE id = '{$OrderInfo['id']}'");
	}
	// 单订单支付
	elseif (abs($WxP-$OrderInfo['c_online']*100) < 1) 
	{
		$MySql->execute("UPDATE yuemi_sale.`order` SET pay_serial = '{$Data[5]}', `update_time` = UNIX_TIMESTAMP(), status = 2 WHERE id = '{$OrderInfo['id']}'");
	}
	else echo " 更新《支付回单号》失败，金额不一致 \n";

	// 查询数据是否已存储
	//	$Sql = "SELECT * FROM {$TableName} WHERE `type` = 1 AND order_id_ext = '{$Data[5]}'";
	//	$temp = $MySql->row($Sql);
	//	if (isset($temp['id']) && $temp['id'] > 0) {
	//		echo "已存在\n";
	//		return;
	//	}

	// 存储到 bill_ext 对账单表
	//	$Sql = "INSERT INTO {$TableName} (type, order_id_ext, order_id, money, details, "
	//			. "create_time, pay_time, update_time) VALUES ( "
	//			. "1, '{$Data[5]}', '{$Data[6]}', '{$Data[12]}', '" . json_encode($Data). "', "
	//			. strtotime($Data[0]) . ", " . strtotime($Data[0]) . ", " . time() .")";
	//	$Istatus = $MySql->execute($Sql);
	//	if ($Istatus) {
	//		echo "微信订单号: {$Data[5]} 阅米订单号: {$Data[6]} 存储成功 \n";
	//	} else {
	//		echo "微信订单号: {$Data[5]} 阅米订单号: {$Data[6]} 存储失败 \n";
	//	}
}
