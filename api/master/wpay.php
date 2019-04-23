<?php
/**
 * 微信支付回调
 */

include "../../_base/config.php";
include Z_ROOT . '/Wechat.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_SITE . '/../../_base/entity/yuemi_main.php';
include Z_SITE . '/../../_base/entity/yuemi_sale.php';

// 初始日志文件
$PathBase = Z_SITE . '/data/pay-wechat';
$PathLog = Z_SITE . '/data/pay-wechat.xml';
$PathLogE = Z_SITE . '/data/pay-wechat-error.xml';
@mkdir($PathBase, 0700, true);

// 校验基本数据
$source = file_get_contents('php://input');
if (empty($source)) {
	return file_put_contents($PathLogE, "Error: 02"); // 返回的XMl数据错误
}
file_put_contents($PathLog, $source);
$xmldoc = simplexml_load_string($source);
if ($xmldoc === null || $xmldoc === false) {
	return file_put_contents($PathLogE, "Error: 03 {$source}"); // 返回的XML结构错误
}
if (!isset($xmldoc->return_code)) {
	return file_put_contents($PathLogE, "Error: 04 {$source}"); // 通信状态错误
}
if (isset($xmldoc->return_msg) && !empty($xmldoc->return_msg)) {
	return file_put_contents($PathLogE, "Error: 05 {$source}"); // 返回消息错误
}
if (strtoupper($xmldoc->return_code) != 'SUCCESS') {
	return file_put_contents($PathLogE, "Error: 06 {$source}"); // 通信失败（微信那边认为通信失败，可是这里又是如何收到数据的呢？？ 感觉微信的文档说出了一个矛盾的逻辑）
}

// 状态失败处理(当return_msg非空时)
if (!empty($xmldoc->return_msg)) {
	return file_put_contents($PathLogE, "Error: 07 {$source}");
}

// 交易失败（当result_code为FAIL时）
if (strtoupper($xmldoc->result_code) != 'SUCCESS') {
	return file_put_contents($PathLogE, "Error: 08 {$source}");
}

// 交易成功
$filepath = $PathBase . '/' . $xmldoc->out_trade_no . '.xml';
file_put_contents($filepath, json_encode($source));

/** *************************************************** *************************************************** **/

// 微信那边返回的数据
$orderid = $xmldoc->out_trade_no; // 阅米订单号
$money = intval($xmldoc->total_fee); // 订单支付金额(微信)，单位：分

// 读取出对应订单信息
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$sql = "SELECT * FROM `yuemi_sale`.`order` WHERE `id` = '{$orderid}'";
$OrderInfo = $mysql->row($sql);
if (!isset($OrderInfo['id'])) {
	$filepath = $PathBase . '/error-' . $xmldoc->out_trade_no . '.xml';
	file_put_contents($filepath, "返回的订单号已不存在 {$orderid} {$sql}\n", FILE_APPEND);
	return file_put_contents($filepath, $source, FILE_APPEND);
}

// 对帐单号(回单号-微信订单号)已存在则不处理
if (!empty($OrderInfo['pay_serial'])) {
	$filepath = $PathBase . '/error-' . $xmldoc->out_trade_no . '.xml';
	file_put_contents($filepath, "返回的订单号已不存在 {$orderid} {$sql}\n", FILE_APPEND);
	return file_put_contents($filepath, $source, FILE_APPEND);
}

// 金额跟当前订单一致，当前订单
if ($money > 0 && abs($OrderInfo['c_online']*100 - $money) < 1) 
{
	$sql = "UPDATE `yuemi_sale`.`order` SET `t_chanel` = 1, `t_chanel` = 1, `pay_serial` = '{$xmldoc->transaction_id}', `pay_time` = UNIX_TIMESTAMP(), `status` = 2, `update_time` = UNIX_TIMESTAMP() WHERE `id` = '{$orderid}'";
	file_put_contents($PathBase . '/succeed-' . $xmldoc->out_trade_no . '.xml', $sql, FILE_APPEND);
	if (!$mysql->execute($sql)){
		return false;
	}

	// 微信回调成为VIP
	// 1.检查是否有阅币赠送，有则进行赠送，无进行2	
	$sqlcheck = "SELECT `sku_id` AS SkuId ,`share_id` AS ShareId FROM `yuemi_sale`.`order_item` WHERE order_id = '{$orderid}'";
	$skuid = $mysql->row($sqlcheck);
	$userid = $mysql->scalar("SELECT `user_id` FROM `yuemi_sale`.`order` WHERE id = '{$orderid}'");
	$sqlcoin = "SELECT `coin_buyer` AS BuyerCoin,`coin_inviter` AS InviterCoin FROM `yuemi_sale`.`sku` WHERE `id` = {$skuid['SkuId']}";
	$coin = $mysql->row($sqlcoin);
	$isgift = $mysql->scalar("SELECT spu.is_gift_set FROM yuemi_sale.sku LEFT JOIN yuemi_sale.spu on sku.spu_id = spu.id WHERE sku.id = {$skuid['SkuId']}");
	if ($isgift == 1){
		$coin['BuyerCoin'] = 1000.00;
	}
	$count = 0;
	$count = $mysql->scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_coin` WHERE `order_id` = '{$orderid}'");
	if ($count == 0){
		// 购买者的阅币赠送
		if ($coin['BuyerCoin'] > 0){
			$Re = \yuemi_main\ProcedureInvoker::Instance()->coin_income($userid,$coin['BuyerCoin'],'BUY',$orderid,'购买赠送',0);
			if ($Re->ReturnValue != 'OK'){
				file_put_contents($PathBase . '/error-coinpro' . $orderid . '.xml', '阅币赠送错误'.$Re->ReturnMessage.json_encode([$userid,$coin['BuyerCoin'],'BUY',$orderid,'购买赠送',0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
			} else {
				file_put_contents($PathBase . '/success-coinpro' . $orderid . '.xml', '阅币赠送记录'.$Re->ReturnMessage.json_encode([$userid,$coin['BuyerCoin'],'BUY',$orderid,'购买赠送',0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
			}
		}
		// 分享者的阅币赠送
		$shareid = $mysql->scalar("SELECT `user_id` FROM `yuemi_mian`.`share` WHERE `id` = {$skuid['ShareId']} LIMIT 1");
		if ($coin['InviterCoin'] > 0 && $shareid > 0){
			$Re = \yuemi_main\ProcedureInvoker::Instance()->coin_income($shareid, $coin['InviterCoin'], 'SHARE', $orderid, '分享赠送', 0);
			if ($Re->ReturnValue != 'OK'){
				file_put_contents($PathBase . '/error-coinpro' . $orderid . '.xml', '阅币赠送错误'.$Re->ReturnMessage.json_encode([$shareid, $coin['InviterCoin'], 'SHARE', $orderid, '分享赠送', 0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
			} else {
				file_put_contents($PathBase . '/success-coinpro' . $orderid . '.xml', '阅币赠送记录'.$Re->ReturnMessage.json_encode([$shareid, $coin['InviterCoin'], 'SHARE', $orderid, '分享赠送', 0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
			}
		}
	}
	// 2.检查阅币是否足够，不足则退出本环节，够进行3
	$getcoin = "SELECT `coin` FROM `yuemi_main`.`user_finance` WHERE `user_id` = {$userid}";
	$coin = $mysql->scalar($getcoin);
	if ($coin < 1000){
		file_put_contents($PathBase . '/error-coinvip' . $orderid . '.xml', '阅币不足'.$getcoin, FILE_APPEND);
		return false;
	}
	// 3.调用存储过程上VIP
	$ReMakeVip = \yuemi_main\ProcedureInvoker::Instance()->make_money_vip($userid, $orderid, 0);
	if ($ReMakeVip->ReturnValue != 'OK'){
		file_put_contents($PathBase . '/error-makevip' . $orderid . '.xml', '购买VIP错误'.$ReMakeVip->ReturnMessage.json_encode([$userid, $orderid,0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
	} else {
		file_put_contents($PathBase . '/success-makevip' . $orderid . '.xml', '购买VIP成功'.$ReMakeVip->ReturnMessage.json_encode([$userid, $orderid,0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
	}
}

// 金额跟订单群一致，所有子订单
if ($money > 0 && abs($OrderInfo['t_online']*100 - $money) < 1) 
{
	$sql = "UPDATE `yuemi_sale`.`order` SET `t_chanel` = 1, `t_chanel` = 1, `pay_serial` = '{$xmldoc->transaction_id}', `pay_time` = UNIX_TIMESTAMP(), `status` = 2, `update_time` = UNIX_TIMESTAMP() WHERE `depend_id` = '{$orderid}'";
	file_put_contents($PathBase . '/succeed-' . $xmldoc->out_trade_no . '.xml', $sql, FILE_APPEND);
	// 查询出主订单下所有的商品的ID 
	$sql = "SELECT Item.sku_id FROM `yuemi_sale`.`order` AS `Order`"
			. "LEFT JOIN `yuemi_sale`.`order_item` AS Item ON Item.order_id = Order.id "
			. "WHERE `depend_id` = '{$orderid}'";
	$skuids = $mysql->column($sql);
	$userid = $mysql->scalar("SELECT `user_id` FROM `yuemi_sale`.`order` WHERE id = '{$orderid}' LIMIT 1 ");
	foreach($skuids as $sku_id)
	{
		$sqlcoin = "SELECT `coin_buyer` AS BuyerCoin,`coin_inviter` AS InviterCoin FROM `yuemi_sale`.`sku` WHERE `id` = {$sku_id}";
		$coin = $mysql->row($sqlcoin);
		// 购买者的阅币赠送
		$count = 0;
		$count = $mysql->scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_coin` WHERE `order_id` = '{$orderid}'");
		if ($count == 0){
			if ($coin['BuyerCoin'] > 0){
				$Re = \yuemi_main\ProcedureInvoker::Instance()->coin_income($userid,$coin['BuyerCoin'],'BUY',$orderid,'购买赠送',0);
				if ($Re->ReturnValue != 'OK'){
					file_put_contents($PathBase . '/error-coinpro' . $orderid . '.xml', '阅币赠送错误'.$Re->ReturnMessage.json_encode([$userid,$coin['BuyerCoin'],'BUY',$orderid,'购买赠送',0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
				} else {
					file_put_contents($PathBase . '/success-coinpro' . $orderid . '.xml', '阅币赠送记录'.$Re->ReturnMessage.json_encode([$userid,$coin['BuyerCoin'],'BUY',$orderid,'购买赠送',0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
				}
			}
		}

		// 2.检查阅币是否足够，不足则退出本环节，够进行3
		$getcoin = "SELECT `coin` FROM `yuemi_main`.`user_finance` WHERE `user_id` = {$userid}";
		$coin = $mysql->scalar($getcoin);
		if ($coin < 1000){
			file_put_contents($PathBase . '/error-coinvip' . $orderid . '.xml', '阅币不足'.$getcoin, FILE_APPEND);
			return false;
		}
		// 3.调用存储过程上VIP
		$ReMakeVip = \yuemi_main\ProcedureInvoker::Instance()->make_money_vip($userid, $orderid, 0);
		if ($ReMakeVip->ReturnValue != 'OK'){
			file_put_contents($PathBase . '/error-makevip' . $orderid . '.xml', '购买VIP错误'.$ReMakeVip->ReturnMessage.json_encode([$userid, $orderid,0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
		} else {
			file_put_contents($PathBase . '/success-makevip' . $orderid . '.xml', '购买VIP成功'.$ReMakeVip->ReturnMessage.json_encode([$userid, $orderid,0],JSON_UNESCAPED_UNICODE), FILE_APPEND);
		}
	}
	return $mysql->execute($sql);
}

// 金额不一致
$filepath = $PathBase . '/error-' . $xmldoc->out_trade_no . '.xml';
file_put_contents($filepath, "金额无法处理\n", FILE_APPEND);
file_put_contents($filepath, $source, FILE_APPEND);
