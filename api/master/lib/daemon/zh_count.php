<?php

/**
 * 数据统计（每小时一次）
 * http://a.ym.cn/lib/daemon/zh_count.php
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_main.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
/* * ****************************************************************************分享统计数据*********************************************************************************** */
//获取时间的最大值和最小值

$sql = "SELECT max(o.create_time) AS max, min(o.create_time) AS min FROM `yuemi_sale`.`order_item` AS oi LEFT JOIN `yuemi_sale`.`order` AS o ON oi.order_id = o.id WHERE o.status > 1";
$mytime = $mysql->row($sql);
$max = $mytime['max'];
$min = $mytime['min'];
$small = strtotime(date('Y-m-d H:00:00', $min));
$big = strtotime(date('Y-m-d H:00:00', $max)) + 3599;
$now = strtotime(date('Y-m-d H:00:00', time()));

  //获取log中插入的最大值
  $log_sql = "SELECT max(time_id) FROM `yuemi_log`.`share_counter` ";
  $log_max = $mysql->scalar($log_sql);
  if (empty($log_max)) {
  //如果为空，说明第一次插入，从最小值到当前时间插入
  $da = $small + 3599;
  for ($small; $small < $now; $small += 3600) {
  $da = $small + 3599;
  $sql = "SELECT oi.share_id, COUNT(oi.share_id) AS num FROM `yuemi_sale`.`order_item` AS oi " .
  "LEFT JOIN `yuemi_sale`.`order` AS o ON oi.order_id = o.id " .
  "WHERE o.status > 1 AND oi.share_id !=0   AND {$small} < o.create_time  AND o.create_time < {$da}  Group By oi.`share_id` ";

  $list = $mysql->grid($sql);

  if (!empty($list)) {
  foreach ($list as $k => $v) {
  $share_id = $v['share_id'];
  $num = $v['num'];
  $mysql->execute("INSERT INTO `yuemi_log`.`share_counter` (share_id,time_id,t_sale) VALUES ($share_id,$now,$num)");
  }
  }
  }
  } else {
  //不为空，只插入log_max的值到现在时间的数据
  $da = $log_max + 3599;
  for ($log_max; $log_max < $now; $log_max += 3600) {
  $da = $log_max + 3599;
  $sql = "SELECT oi.share_id, COUNT(oi.share_id) AS num FROM `yuemi_sale`.`order_item` AS oi " .
  "LEFT JOIN `yuemi_sale`.`order` AS o ON oi.order_id = o.id " .
  "WHERE o.status > 1 AND oi.share_id !=0 AND {$log_max} < o.create_time  AND o.create_time < {$da} Group By oi.`share_id`  ";

  $list = $mysql->grid($sql);
  if (!empty($list)) {
  foreach ($list as $k => $v) {
  $share_id = $v['share_id'];
  $num = $v['num'];
  $mysql->execute("INSERT INTO `yuemi_log`.`share_counter` (share_id,time_id,t_sale) VALUES ($share_id,$now,$num)");
  }
  }
  }
  }

/* * ****************************************************************************SKU统计数据*********************************************************************************** */
//先统计分享，后统计卖出
//获取sku_counter最大时间
$new = [];
$log_time = $mysql->scalar("SELECT max(time_id) FROM `yuemi_log`.`sku_counter`");
if ($log_time > 100) {
	//以前插入过记录
	
	for ($log_time; $log_time < $now; $log_time += 3600) {
		//统计分享
		$log_time = $log_time + 3599;
		$sharesql = "SELECT sku_id,COUNT(sku_id) AS share_num,{$log_time} AS time FROM `yuemi_sale`.`share` " .
				"WHERE {$log_time} < create_time  AND create_time < {$endtime}  Group By sku_id ";
		$sharelist = $mysql->grid($sharesql);
		//统计卖出
		$salesql = "SELECT oi.sku_id, sum(oi.qty) AS sale_num ,{$log_time} AS time FROM `yuemi_sale`.`order_item` AS oi " .
				"LEFT JOIN `yuemi_sale`.`order` AS o ON oi.order_id = o.id " .
				"WHERE o.status > 1 AND o.status NOT IN (11,12,13,14,15) AND {$log_time} < o.create_time  AND o.create_time < {$endtime}  Group By oi.`sku_id` ";
		$salelist = $mysql->grid($salesql);
		$aa = [];
		foreach ($sharelist as $k => $v) {
			$aa[] = $v['sku_id'];
		}
		foreach ($salelist as $k => $v) {
			$aa[] = $v['sku_id'];
		}
		$aa = array_unique($aa);
		$arr = [];

		foreach ($aa as $k => $v) {
			foreach ($sharelist as $kk => $vv) {
				$arr[$v]['share'] = 0;
				if ($vv['sku_id'] == $v) {
					$arr[$v]['share'] = $vv['share_num'];
					break;
				} else {
					$arr[$v]['share'] = 0;
				}
			}
			foreach ($salelist as $kk => $vv) {
				$arr[$v]['sale'] = 0;
				if ($vv['sku_id'] == $v) {
					$arr[$v]['sale'] = $vv['sale_num'];
					break;
				} else {
					$arr[$v]['sale'] = 0;
				}
			}
		}
		$arr['time'] = $mintime;
		$new[] = $arr;
	}
	foreach ($new as $key => $arra) {
		if (count($arra) > 1) {
			foreach ($arra as $keys => $arr) {
			
				if (empty($arr['share']) || $arr['share'] == 0) {
					$mysql->execute("INSERT INTO `yuemi_log`.`sku_counter` (sku_id,time_id,t_sale,t_share) VALUES($keys,{$arra['time']},{$arr['sale']},0)");
				} elseif (empty($arr['sale']) || $arr['sale'] == 0) {
					$mysql->execute("INSERT INTO `yuemi_log`.`sku_counter` (sku_id,time_id,t_sale,t_share) VALUES($keys,{$arra['time']},0,{$arr['share']})");
				} else {
					$mysql->execute("INSERT INTO `yuemi_log`.`sku_counter` (sku_id,time_id,t_sale,t_share) VALUES($keys,{$arra['time']},{$arr['sale']},{$arr['share']})");
				}
			}
		}
	}
} else {
	//第一次插入
	$mintime = 1524474000; //插入分享表时间的最小值
	for ($mintime; $mintime < $now; $mintime += 3600) {
		//统计分享
		$endtime = $mintime + 3599;
		$sharesql = "SELECT sku_id,COUNT(sku_id) AS share_num,{$mintime} AS time FROM `yuemi_sale`.`share` " .
				"WHERE {$mintime} < create_time  AND create_time < {$endtime}  Group By sku_id ";
		$sharelist = $mysql->grid($sharesql);
		//统计卖出
		$salesql = "SELECT oi.sku_id, sum(oi.qty) AS sale_num ,{$mintime} AS time FROM `yuemi_sale`.`order_item` AS oi " .
				"LEFT JOIN `yuemi_sale`.`order` AS o ON oi.order_id = o.id " .
				"WHERE o.status > 1 AND o.status NOT IN (11,12,13,14,15) AND {$mintime} < o.create_time  AND o.create_time < {$endtime}  Group By oi.`sku_id` ";
		$salelist = $mysql->grid($salesql);
		$aa = [];
		foreach ($sharelist as $k => $v) {
			$aa[] = $v['sku_id'];
		}
		foreach ($salelist as $k => $v) {
			$aa[] = $v['sku_id'];
		}
		$aa = array_unique($aa);
		$arr = [];

		foreach ($aa as $k => $v) {
			foreach ($sharelist as $kk => $vv) {
				$arr[$v]['share'] = 0;
				if ($vv['sku_id'] == $v) {
					$arr[$v]['share'] = $vv['share_num'];
					break;
				} else {
					$arr[$v]['share'] = 0;
				}
			}
			foreach ($salelist as $kk => $vv) {
				$arr[$v]['sale'] = 0;
				if ($vv['sku_id'] == $v) {
					$arr[$v]['sale'] = $vv['sale_num'];
					break;
				} else {
					$arr[$v]['sale'] = 0;
				}
			}
		}
		$arr['time'] = $mintime;
		$new[] = $arr;
	}
	foreach ($new as $key => $arra) {
		if (count($arra) > 1) {
			foreach ($arra as $keys => $arr) {
			
				if (empty($arr['share']) || $arr['share'] == 0) {
					$mysql->execute("INSERT INTO `yuemi_log`.`sku_counter` (sku_id,time_id,t_sale,t_share) VALUES($keys,{$arra['time']},{$arr['sale']},0)");
				} elseif (empty($arr['sale']) || $arr['sale'] == 0) {
					$mysql->execute("INSERT INTO `yuemi_log`.`sku_counter` (sku_id,time_id,t_sale,t_share) VALUES($keys,{$arra['time']},0,{$arr['share']})");
				} else {
					$mysql->execute("INSERT INTO `yuemi_log`.`sku_counter` (sku_id,time_id,t_sale,t_share) VALUES($keys,{$arra['time']},{$arr['sale']},{$arr['share']})");
				}
			}
		}
	}
}
