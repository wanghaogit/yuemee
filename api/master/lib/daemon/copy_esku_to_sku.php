<?php
/**
 * SKU处理任务：定时变更SKU信息
 * http://a.ym.cn/lib/daemon/copy_esku_to_sku.php
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_main.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$time = time();
//获取所有的SKU
$sql = "SELECT `id` FROM `yuemi_sale`.`sku` ORDER BY `id` ASC";
$ids = $mysql->grid($sql);
$i = 0;
foreach($ids as $id){
	$sku_id = $id['id'];
	$sql = "SELECT `id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$sku_id}";
	$ext_sku_id = $mysql->scalar($sql);
	if ($ext_sku_id === NULL){
		//echo 'SKU-'.$sku_id.' 无外部SKU '."\n";
		continue;
	}
	$sql1 = "SELECT COUNT(EKM.`id`) FROM `yuemi_sale`.`ext_sku_material` AS EKM 
			LEFT JOIN ( SELECT `file_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} ) AS PM ON PM.`file_url` = EKM.`file_url`
			WHERE EKM.`ext_sku_id` = {$ext_sku_id}  AND PM.`file_url` IS NULL";
	$count = $mysql->scalar($sql1);
	if ($count === "0"){
		//echo 'SKU-'.$sku_id.' 无需复制 '."\n";		
		continue;
	}
	$i++;
	$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_esku_to_sku($sku_id, $ext_sku_id,1,3 ,1928461940);
	echo 'SKU-'.$sku_id.' -复制信息->'.$Re->ReturnMessage."\n";
}
$time = time() - $time;
echo '共 '.$i.' 条有效数据 , 耗时'.$time.'秒';
