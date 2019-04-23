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
$sql = "SELECT `id` FROM `yuemi_sale`.`spu` ORDER BY `id` ASC";
$ids = $mysql->grid($sql);
$i = 0;
foreach($ids as $id){
	$spu_id = $id['id'];
	$sql = 'SELECT `id` FROM `yuemi_sale`.`ext_spu` WHERE `spu_id` = '.$spu_id;
	$ext_spu_id = $mysql->scalar($sql);
	if ($ext_spu_id === NULL){
		echo 'SPU-'.$spu_id.' 无外部SPU '."\n";
		continue;
	}
	$sql1 = "SELECT COUNT(EPM.`id`) FROM `yuemi_sale`.`ext_spu_material` AS EPM 
				LEFT JOIN ( SELECT `file_url` FROM `yuemi_sale`.`spu_material` WHERE `spu_id` = {$spu_id} ) AS PM ON PM.`file_url` = EPM.`file_url`
				WHERE EPM.`ext_spu_id` = {$ext_spu_id} AND PM.`file_url` IS NULL";
	$count = $mysql->scalar($sql1);
	if ($count === "0"){
		echo 'SPU-'.$spu_id.' 无需复制 '."\n";
		continue;
	}
	$i++;
	$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_espu_to_spu($spu_id, $ext_spu_id,1,3 ,1928461940);
	echo 'SPU-'.$spu_id.' -复制信息->'.$Re->ReturnMessage."\n";
}
$time = time() - $time;
echo '共 '.$i.' 条有效数据 , 耗时'.$time.'秒';
