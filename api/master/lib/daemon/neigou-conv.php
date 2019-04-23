<?php

/**
 * 线上数据初始化
 * 2018-5-5
 * 重新同步价格、库存、毛利
 */
include 'neigou-base.php';


$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$CATS = $mysql->column('SELECT `id` FROM yuemi_sale.`catagory`');

$list = $mysql->grid("SELECT `id`,`serial`,`spu_id`,`catagory_id`,`title`,`depot`,`price_base`,`price_sale`,`price_inv`,`price_vip`,`price_ref`,`price_market`,`rebate_vip`,`status` FROM `yuemi_sale`.`sku` WHERE `status` != 4");
foreach ($list as $sku) {
	$spu = $mysql->row("SELECT `id`,`catagory_id`,`title`,`status` FROM `yuemi_sale`.`spu` WHERE `id` = %d", $sku['spu_id']);
	$mat_k = $mysql->scalar("SELECT COUNT(*) FROM yuemi_sale.sku_material WHERE sku_id = %d AND `type` = 0", $sku['id']);
	$mat_p = $mysql->scalar("SELECT COUNT(*) FROM yuemi_sale.spu_material WHERE spu_id = %d AND `type` = 0", $sku['spu_id']);
	$mat_ek = 0;
	$mat_ep = 0;
	$ext_sku = $mysql->row("SELECT `id`,`bn`,`name`,`stock`,`price_base`,`price_ref`,`status`,`update_time` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = %d", $sku['id']);
	$ext_spu = $mysql->row("SELECT `id`,`ext_shop_code`,`bn`,`spu_id` FROM `yuemi_sale`.`ext_spu` WHERE `spu_id` = %d", $spu['id']);

	if (!empty($ext_sku)) {
		$mat_ek = $mysql->scalar("SELECT COUNT(*) FROM yuemi_sale.ext_sku_material WHERE ext_sku_id = %d AND `type` = 0", $ext_sku['id']);
	}

	if (!empty($ext_spu)) {
		$mat_ek = $mysql->scalar("SELECT COUNT(*) FROM yuemi_sale.ext_spu_material WHERE ext_spu_id = %d AND `type` = 0", $ext_spu['id']);
	}
	if (empty($sku['title'])) {
		echo "商品 #{$sku['id']} {$sku['title']} 无标题下架\n";
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 0 WHERE `id` = %d", $sku['id']);
		continue;
	}
	if ($mat_ek + $mat_ep + $mat_k + $mat_p < 1) {
		echo "商品 #{$sku['id']} {$sku['title']} 无图下架\n";
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 0 WHERE `id` = %d", $sku['id']);
		continue;
	}

	//有外部SKU，先同步下price_base/price_sale/stock
	if (!empty($ext_sku)) {
		echo "商品 #{$sku['id']} {$sku['title']} 价格同步\n";
		$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `price_base` = {$ext_sku['price_base']}, `price_ref` = {$ext_sku['price_ref']}, `depot` = {$ext_sku['stock']} WHERE `id` = %d", $sku['id']);
		$sku['price_base'] = $ext_sku['price_base'];
		$sku['price_ref'] = $ext_sku['price_ref'];
		$sku['depot'] = $ext_sku['stock'];

		if ($ext_sku['stock'] < 1) {
			echo "商品 #{$sku['id']} {$sku['title']} 无库存下架\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 3 WHERE `id` = %d", $sku['id']);
			continue;
		}
	}

	//内部商品
	if (empty($ext_sku) || empty($ext_spu)) {
		if ($sku['depot'] < 1) {
			echo "商品 #{$sku['id']} {$sku['title']} 缺货下架\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 3 WHERE `id` = %d", $sku['id']);
			continue;
		}
		if ($sku['price_base'] <= 0 || $sku['price_sale'] <= 0) {
			echo "商品 #{$sku['id']} {$sku['title']} 价格错误下架\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 0 WHERE `id` = %d", $sku['id']);
			continue;
		}
		if (!in_array($sku['catagory_id'], $CATS)) {
			echo "商品 #{$sku['id']} {$sku['title']} 分类错误下架\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 0 WHERE `id` = %d", $sku['id']);
			continue;
		}
		//若毛利小于 5%，下架状态为0
		if (($sku['price_sale'] - $sku['price_base']) / $sku['price_sale'] < 0.05) {
			echo "商品 #{$sku['id']} {$sku['title']} 毛利不够\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 0 WHERE `id` = %d", $sku['id']);
			continue;
		}

		//若SKUID= (174,176,276,156,7248,7253,7254,21,164)，是大礼包
		if (in_array($sku['id'], [174, 176, 276, 156, 7248, 7253, 7254, 21, 164])) {
			echo "商品 #{$sku['id']} {$sku['title']} 大礼包，跳过\n";
			continue;
		}

		//其它情况
		continue;
	}

	//基本毛利，不足5%，成本先加2个点
	$ratio = ($sku['price_ref'] - $sku['price_base']) / $sku['price_ref'];
	if ($ratio < 0.1) {
		$price_base = $sku['price_base'] * 1.02;
	} elseif ($ext_spu['ext_shop_code'] == 'JD' && $ratio >= 0.2) {
		$price_base = $sku['price_base'] * 1.01;
	} else {
		$price_base = $sku['price_base'];
	}
	$price_ref = $sku['price_ref'];
	//市场价统一在对标价基础上加10%
	$price_market = $price_ref * 1.1;
	//第一次计算佣金
	$rebate_vip = ($price_ref - $price_base) * 0.56;
	//初佣扣除10%用于降价
	$price_sale = $price_ref - $rebate_vip * 0.1;
	$rebate_vip = ($price_sale - $price_base) * 0.56;
	$mysql->execute("UPDATE `yuemi_sale`.`sku` SET `price_market` = %f,`price_sale` = %f,price_vip = %f,price_inv = %f,`rebate_vip` = %f WHERE `id` = %d",
			$price_market,
			$price_sale,
			$price_sale,
			$price_sale,
			$rebate_vip,
			$sku['id']);
}
