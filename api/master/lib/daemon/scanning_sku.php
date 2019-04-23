<?php

/**
 * 扫描检测SKU，不符合条件直接下架
 * 检测内容：规格，主图，佣金，价格，库存
 * http://a.ym.cn/lib/daemon/scanning_sku.php
 * /opt/php/bin/php /data/web/api/lib/daemon/scanning_sku.php
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_main.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
// 读取要处理的数据

$list = $mysql->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE status = 2 AND supplier_id NOT IN (2) ORDER BY id DESC");

foreach ($list as $v) {

	//成本价设置错误，0或者负数
	if ($v['price_base'] <= 0) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = 0 WHERE id = {$v['id']}");
		echo $v['id'] . "成本价不符合规则，强制下架\n";
		continue;
	}
	//阅米价 价格是否为0
	if ($v['price_sale'] < 0 || $v['price_sale'] == 0) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "阅米价不符合规则，强制下架\n";
		continue;
	}
//	零售价设置错误，0或者负数 （原市场价，现在改名叫零售价）
	if ($v['price_market'] <= 0) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "零售价不符合规则，强制下架\n";
		continue;
	}

	//判断佣金是否为负数
	if ($v['rebate_vip'] < 0) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = 0 WHERE id = {$v['id']}");
		echo $v['id'] . "佣金小于0，强制把佣金改为0\n";
		continue;
	}

	//大礼包佣金改为0
	if ($v['catagory_id'] == 7 || $v['catagory_id'] == 701) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = 0 WHERE id = {$v['id']}");
		echo $v['id'] . "大礼包商品，强制把佣金改为0\n";
		continue;
	}
	//负毛利把佣金改为0
	if ($v['price_base'] > $v['price_sale']) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = 0 WHERE id = {$v['id']}");
		echo $v['id'] . "负毛利商品，强制把佣金改为0\n";
		continue;
	}

	//库存为0
	if ($v['depot'] <= 0) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "库存为0，强制下架\n";
		continue;
	}

	//是否有主图
	$imgs = array();
	//SKU素材
	if (empty($imgs)) {
		$shm = $mysql->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $v['id'] . " AND `type` = 0");
		if (!empty($shm)) {
			foreach ($shm as $sh) {
				$imgs['Id'] = $sh['id'];
			}
		}
	}

	//spu素材
	if (empty($imgs)) {
		$spuid = $mysql->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $v['id']);
		$shm = $mysql->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 0");
		if (!empty($shm)) {
			foreach ($shm as $sh) {
				$imgs['Id'] = $sh['id'];
			}
		}
	}

	//ext_sku素材
	if (empty($imgs)) {
		$ext_sku_id = $mysql->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = {$v['id']}");
		$shm = $mysql->grid("SELECT type,id,thumb_size,size,thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id}");
		if (!empty($shm)) {
			foreach ($shm as $sh) {
				$imgs['Id'] = $sh['id'];
			}
		}
	}
	//ext_spu素材
	if (empty($imgs)) {
		$spu_id = $v['spu_id'];
		$ext_spu_id = $mysql->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");
		$shm = $mysql->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = {$ext_spu_id}");
		if (!empty($shm)) {
			foreach ($shm as $sh) {
				$imgs['Id'] = $sh['id'];
			}
		}
	}

	if (empty($imgs)) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "没有主图，强制下架\n";
		continue;
	}

	//成本价和阅米价
//	if($v['price_base'] > $v['price_sale'])
//	{
//		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
//		echo $v['id']."成本价大于阅米价，强制下架\n";
//		continue;
//	}
	//阅米价不能大于市场价
	if ($v['price_market'] < $v['price_sale']) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "阅米价大于市场价，强制下架\n";
		continue;
	}

	//成本价不能大于市场价
	if ($v['price_base'] > $v['price_market']) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "成本价大于市场价，强制下架\n";
		continue;
	}
	//规格
	if (!($v['specs'] === NULL) && !($v['specs'] == NULL)) {
		$rn = strpos($v['specs'], ":");
		if (!$rn) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
			echo $v['id'] . "只有规格，没有属性，强制下架\n";
			continue;
		}
	}
	//规格中有undefined
	if (strpos($v['specs'], 'undefined') !== false) {
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['id']}");
		echo $v['id'] . "规格格式错误，强制下架\n";
		continue;
	}
}

//spu=>id 对应的  sku=>spu_id，判断是否有规格，没有的话直接下架
$spu = $mysql->grid("SELECT * FROM `yuemi_sale`.`spu` WHERE supplier_id NOT IN (2) ORDER BY id DESC");

foreach ($spu as $v) {
	//查看这个spu下面的sku
	$sku = $mysql->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE  spu_id= {$v['id']} AND status = 2 ");

	if (count($sku) == 1) {
		//echo $v['id'] . "就一个sku，不做处理\n";
		continue;
	} else {
		foreach ($sku as $val) {
			//规格不能为空
			if (strlen($val['specs']) == 0) {
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$val['id']}");
				echo $val['id'] . "没有规格，强制下架\n";
				continue;
			}
		}
	}
}
