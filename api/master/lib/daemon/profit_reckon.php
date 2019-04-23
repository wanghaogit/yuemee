<?php
/**
 * 返佣定时任务，处理数据量不会很大，建议五分钟执行一次
 * http://a.ym.cn/lib/daemon/profit_reckon
 * /opt/php/bin/php /data/web/api/lib/daemon/profit_reckon
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

echo "**************************************** 确定收货/完成订单七天的返利记录 **************************************** \n<br/>";
$time = time() - 604800;

$Sql = "SELECT `order_id` FROM yuemi_sale.`rebate` WHERE `status` = 2 AND `time_finish` <= {$time} GROUP BY `order_id`";
//$Sql = "SELECT `order_id` FROM yuemi_sale.`rebate` WHERE `status` = 2 GROUP BY `order_id`";
$OrderList = $MySql->grid($Sql);
foreach ($OrderList AS $OrderIdA)
{
	$OrderId = $OrderIdA['order_id'];
	$Re = \yuemi_sale\ProcedureInvoker::Instance()->profit_reckon($OrderId, ip2long("127.0.0.1"));
	echo ' 订单号 : '.$OrderId.' 处理情况 : '.$Re->ReturnValue.' 流程信息 : '.$Re->ReturnMessage.' \n<br/><br/>';
}
