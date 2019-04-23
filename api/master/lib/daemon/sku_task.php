<?php

/**
 * SKU处理任务：定时变更SKU信息
 * http://a.ym.cn/lib/daemon/bill_wx.php
 * 任务状态：0待审,1拒绝,2删除,3批准,4排队,5启动,6结束,7过期
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_main.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);

// 读取要处理的数据
$list = $mysql->grid("SELECT * FROM `yuemi_sale`.`sku_task` WHERE status IN (3,4,5)");
foreach ($list as $v) {
	$time = time();
	if ($v['s1_time'] > $v['s2_time']) {
		echo "开始时间大于结束时间 \n";
		continue;
	}
	if ($time < $v['s1_time']) {
		//排队中可以修改
//		$type = $mysql->scalar("SELECT status FROM `yuemi_sale`.`sku_task` WHERE id = {$v['id']}");
//		if($type == 4)
//		{
//			echo $v['id']."已经是状态4，无需操作\n";
//			continue;
//		}
//		echo $v['id'] . "还未开始，备份并且状态改为4\n";
		// 审核通过但是还没有到开始时间，状态改为4，并且备份
		$sku = $mysql->row("SELECT * FROM `yuemi_sale`.`sku` WHERE id = {$v['sku_id']}");
		// 是否改标题
		if ($v['uf_title'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET s0_title = '{$sku['title']}',status = 4 WHERE id = {$v['id']}");
			echo $v['id'] . "标题备份完毕\n";
		}
		// 是否改子标题
		if ($v['uf_subtitle'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET s0_subtitle = '{$sku['subtitle']}',status = 4 WHERE id = {$v['id']}");
			echo $v['id'] . "子标题备份完毕\n";
		}
		// 是否修改平台价
		if ($v['uf_price'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET s0_price = {$sku['price_sale']},status = 4 WHERE id = {$v['id']}");
			echo $v['id'] . "平台价备份完毕\n";
		}
		// 是否修改库存
		if ($v['uf_qty'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET s0_qty = {$sku['depot']},status = 4 WHERE id = {$v['id']}");
			echo $v['id'] . "库存备份完毕\n";
		}
		// 是否修改限量
		if ($v['uf_limit'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET s0_limit = {$sku['limit_size']},status = 4 WHERE id = {$v['id']}");
			echo $v['id'] . "限量备份完毕\n";
		}
		// 是否修改返利
		if ($v['uf_rebate'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET s0_rebate = {$sku['rebate_vip']},status = 4 WHERE id = {$v['id']}");
			echo $v['id'] . "返利备份完毕\n";
		}
	}

	//开始
	if ($time > $v['s1_time'] && $time < $v['s2_time']) {
		$type = $mysql->scalar("SELECT status FROM `yuemi_sale`.`sku_task` WHERE id = {$v['id']}");
		if ($type == 5) {
			echo $v['id'] . "已经是状态5，无需操作\n";
			continue;
		}
		//在规定时间内，改对应的SKU，并且状态改为5
		echo $v['id'] . "规定开始时间到，状态改为5，并且修改sku信息\n";
		//是否改标题
		if ($v['uf_title'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 5 WHERE id = {$v['id']}");
			echo $v['id'] . "状态修改5完毕\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET title = '{$v['s1_title']}' WHERE id = {$v['sku_id']}");
			echo $v['sku_id'] . "标题修改完毕\n";
		}
		//是否改子标题
		if ($v['uf_subtitle'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 5 WHERE id = {$v['id']}");
			echo $v['id'] . "状态修改5完毕\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET subtitle = '{$v['s1_subtitle']}' WHERE id = {$v['sku_id']}");
			echo $v['sku_id'] . "子标题修改完毕\n";
		}
		//是否修改平台价
		if ($v['uf_price'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 5 WHERE id = {$v['id']}");
			echo $v['id'] . "状态修改5完毕\n";
			$base = $mysql->scalar("SELECT price_base FROM `yuemi_sale`.`sku` WHERE id = {$v['sku_id']}");
			$rebate = ($v['s1_price'] - $base) * 0.56;
			if ($rebate < 0) {
				$rebate = 0;
			}
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET price_sale = {$v['s1_price']},price_inv = {$v['s1_price']},rebate_vip = {$rebate} WHERE id = {$v['sku_id']}");
			echo $v['sku_id'] . "平台价修改完毕\n";
		}
		//是否修改库存
		if ($v['uf_qty'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 5 WHERE id = {$v['id']}");
			echo $v['id'] . "状态修改5完毕\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET depot = {$v['s1_qty']} WHERE id = {$v['sku_id']}");
			echo $v['sku_id'] . "库存修改完毕\n";
		}
		//是否修改限量
		if ($v['uf_limit'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 5 WHERE id = {$v['id']}");
			echo $v['id'] . "状态修改5完毕\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET limit_size = {$v['s1_limit']},limit_style = 1 WHERE id = {$v['sku_id']}");
			echo $v['sku_id'] . "库存修改完毕\n";
		}
		//是否修改返利
		if ($v['uf_rebate'] == 1) {
			$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 5 WHERE id = {$v['id']}");
			echo $v['id'] . "状态修改5完毕\n";
			$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = {$v['s1_rebate']} WHERE id = {$v['sku_id']}");
			echo $v['sku_id'] . "佣金修改完毕\n";
		}
	}

	//结束
	if ($time > $v['s2_time']) {
		$type = $mysql->scalar("SELECT status FROM `yuemi_sale`.`sku_task` WHERE id = {$v['id']}");
		if ($type == 6) {
			echo $v['id'] . "已经是状态6，无需操作\n";
			continue;
		}
		//结束，改对应的SKU，并且状态改为6
		echo $v['id'] . "规定结束时间到，状态改为6，并且修改sku信息\n";
		//是否改标题
		if ($v['uf_title'] == 1) {
			if ($v['s2_method'] == 0) {
				// 还原
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET title = '{$v['s0_title']}' WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "标题还原完毕\n";
			} elseif ($v['s2_method'] == 1) {
				// 使用s2
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET title = '{$v['s2_title']}' WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "标题设置完毕\n";
			} else {
				// 下架
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "下架完毕\n";
			}
		}
		//是否改子标题
		if ($v['uf_subtitle'] == 1) {
			if ($v['s2_method'] == 0) {
				// 还原
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET subtitle = '{$v['s0_subtitle']}' WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "子标题还原完毕\n";
			} elseif ($v['s2_method'] == 1) {
				// 使用s2
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET subtitle = '{$v['s2_subtitle']}' WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "子标题设置完毕\n";
			} else {
				// 下架
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "下架完毕\n";
			}
		}
		//是否修改平台价
		if ($v['uf_price'] == 1) {
			if ($v['s2_method'] == 0) {
				// 还原
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$base = $mysql->scalar("SELECT price_base FROM `yuemi_sale`.`sku` WHERE id = {$v['sku_id']}");
				$rebate = ($v['s0_price'] - $base) * 0.56;
				if ($rebate < 0) {
					$rebate = 0;
				}
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET price_sale = {$v['s0_price']},price_inv = {$v['s0_price']},rebate_vip = {$rebate} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "平台价还原完毕\n";
			} elseif ($v['s2_method'] == 1) {
				// 使用s2
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$base = $mysql->scalar("SELECT price_base FROM `yuemi_sale`.`sku` WHERE id = {$v['sku_id']}");
				$rebate = ($v['s2_price_base'] - $base) * 0.56;
				if ($rebate < 0) {
					$rebate = 0;
				}
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET price_sale = {$v['s2_price_base']},price_inv = {$v['s2_price']},rebate_vip = {$rebate} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "平台价设置完毕\n";
			} else {
				// 下架
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "下架完毕\n";
			}
		}

		//是否修改库存
		if ($v['uf_qty'] == 1) {
			if ($v['s2_method'] == 0) {
				// 还原
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET depot = {$v['s0_qty']} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "库存还原完毕\n";
			} elseif ($v['s2_method'] == 1) {
				// 使用s2
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET depot = {$v['s2_qty']} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "库存设置完毕\n";
			} else {
				// 下架
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "下架完毕\n";
			}
		}
		//是否修改限量
		if ($v['uf_limit'] == 1) {
			if ($v['s2_method'] == 0) {
				// 还原
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET limit_size = {$v['s0_limit']} , limit_style = 0 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "限购还原完毕\n";
			} elseif ($v['s2_method'] == 1) {
				// 使用s2
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET limit_size = {$v['s2_limit']} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "限购设置完毕\n";
			} else {
				// 下架
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "下架完毕\n";
			}
		}
		//是否修改返利
		if ($v['uf_rebate'] == 1) {
			if ($v['s2_method'] == 0) {
				// 还原
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = {$v['s0_rebate']} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "返佣还原完毕\n";
			} elseif ($v['s2_method'] == 1) {
				// 使用s2
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET rebate_vip = {$v['s2_rebate']} WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "返佣设置完毕\n";
			} else {
				// 下架
				$mysql->execute("UPDATE `yuemi_sale`.`sku_task` SET status = 6 WHERE id = {$v['id']}");
				echo $v['id'] . "状态修改6完毕\n";
				$mysql->execute("UPDATE `yuemi_sale`.`sku` SET status = 3 WHERE id = {$v['sku_id']}");
				echo $v['sku_id'] . "下架完毕\n";
			}
		}
	}
}
