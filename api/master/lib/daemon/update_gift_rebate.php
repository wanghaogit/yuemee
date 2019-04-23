<?php

include dirname(__FILE__) . '/../../../../_base/config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_main.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$list = $mysql->grid("SELECT `id` FROM `yuemi_sale`.`sku` WHERE `catagory_id` = 701");
foreach ($list as $k => $v){
	$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `rebate_vip` = 0 WHERE `id` = {$v['id']}");
}

