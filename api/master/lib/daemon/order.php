<?php
/**
 * 订单定时任务，处理数据量不会很大，建议死循环或每分钟执行一次
 * http://a.ym.cn/lib/daemon/order.php
 * /opt/php/bin/php /data/web/api/lib/daemon/order.php
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
define("DIR_ROOT", dirname(__FILE__) . '/../../..');
define("DIR_CACHE", DIR_ROOT . '/data/order/neigou_cron');
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_ROOT . '/Cloud/Neigou.php';
include dirname(__FILE__) . '/../../../../_base/WeiXinPayment.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';

$MySql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$NeiGou = new \Cloud\NeiGou(NG_URL_BASE, NG_CLIENTID, NG_SECRET, DIR_CACHE);
$Redis = new \Redis();
$Redis->connect(REDIS_HOST, REDIS_PORT);
if(!empty(REDIS_AUTH)){
	$Redis->auth(REDIS_AUTH);
}
$Redis->select(5);

echo "**************************************** 已支付订单移动到《待发货》，条件为：支付时间超过5分钟 **************************************** \n";
$Sql = "SELECT * FROM yuemi_sale.`order` WHERE `status` = 2";
$OrderList = $MySql->grid($Sql);
foreach ($OrderList AS $OrderInfo)
{
	// 无回单号，跳过
	if (empty($OrderInfo['pay_serial']) && $OrderInfo['c_online'] > 0) {
		continue;
	}
	// 支付时间超过5分钟，更新状态为：待发货
	if (time() > ($OrderInfo['pay_time']+300)) {
		$Sql = "UPDATE yuemi_sale.`order` SET `status` = 4 WHERE id = '{$OrderInfo['id']}'";
		$MySql->execute($Sql);
		echo $Sql."<br />";
	}
}

echo "**************************************** 关闭未支付订单，条件为：超过10分钟未支付 **************************************** \n";
$Sql = "SELECT * FROM yuemi_sale.`order` WHERE `status` = 1";
$OrderList = $MySql->grid($Sql);
foreach ($OrderList AS $OrderInfo)
{
	if (time() > ($OrderInfo['create_time']+600))
	{
		echo "关闭订单{$OrderInfo['id']}：";
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->close_order($OrderInfo['id'], ip2long("127.0.0.1"));
		print_r($Re);
	}
}

echo "**************************************** 从内购拿物流单号，每小时1次（缓存） **************************************** \n";
$CacheLastTime = $Redis->get('OrderLastTime');
if ($CacheLastTime != date("YmdH"))
{
	$Redis->setex("OrderLastTime", time()+3600, date("YmdH"));
	$Sql = "SELECT * FROM yuemi_sale.`order` WHERE supplier_id = 2 AND `status` = 5";
	$OrderList = $MySql->grid($Sql);
	foreach ($OrderList AS $OrderInfo)
	{
		if (!empty($OrderInfo['trans_com'])) {
			echo "跳过，内购物流已存在\n";
			continue;
		}
		// 从缓存中验证最近是否已处理过
		$CacheOrderId = $Redis->get("OrderNgTrans-{$OrderInfo['id']}");
		if (!empty($CacheOrderId)) {
			echo "跳过，1小时内已存在记录\n";
			continue;
		}
		$Redis->setex("OrderNgTrans-{$OrderInfo['id']}", time()+3600, date("Y-m-d H:i:s"));
		// 获取信息、更新订单
		echo "处理订单 {$OrderInfo['id']} ";
		$NgOrderInfo = $NeiGou->order_info($OrderInfo['ext_order_id']);
		if (!empty($NgOrderInfo['Data']['logi_code'])) {
			$OrderId = $OrderInfo['id'];
			$TransCom = $NgOrderInfo['Data']['logi_code'];
			$TransId = $NgOrderInfo['Data']['logi_no'];
			$TransCom = $NeiGou->comcode_to_kuaidi100($TransCom);
			echo $OrderInfo['id'] . ' ' . $TransCom . "\n";
			continue;
			$MySql->execute("UPDATE yuemi_sale.`order` SET trans_com = '{$TransCom}', trans_id = '{$TransId}', `update_time` = UNIX_TIMESTAMP() WHERE id = '{$OrderId}'");
		}
	}
}
else echo "无需处理\n";
