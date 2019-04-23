<?php

include "lib/AdminHandler.php";

//set_time_limit (0);
/**
 * 报表中心
 * @auth
 */
class report_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	public function user1(int $p = 0, int $type = 0, int $time = 0) {
		$whr = [];
		if ($type == 0) {
			//普通用户
			$sql = " SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`u`.`reg_time`,`uw`.`name` AS `wname`,`uw`.`avatar` FROM `yuemi_main`.`user` AS `u` " .
					"LEFT JOIN `yuemi_main`.`user_wechat` AS `uw` ON `uw`.`user_id` = `u`.`id` ";
			if ($time == 0) {
				//当日
				$date = date("Y-m-d", time());
				$today_zero = strtotime($date);
				$whr[] = " `u`.`reg_time` > {$today_zero} ";
			} elseif ($time == 1) {
				//本月
				$date = date("Y-m", time());
				$month_zero = strtotime($date);
				$whr[] = " `u`.`reg_time` > {$month_zero} ";
			}
		} elseif ($type == 1) {
			//微信用户
			$sql = " SELECT `uw`.`user_id` AS `id`,`u`.`name`,`u`.`mobile`,`uw`.`create_time` AS `reg_time`,`uw`.`name` AS `wname`,`uw`.`avatar` FROM `yuemi_main`.`user_wechat` AS `uw` " .
					"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `uw`.`user_id` ";
			if ($time == 0) {
				//当日
				$date = date("Y-m-d", time());
				$today_zero = strtotime($date);
				$whr[] = " `uw`.`create_time` > {$today_zero} ";
			} elseif ($time == 1) {
				//本月
				$date = date("Y-m", time());
				$month_zero = strtotime($date);
				$whr[] = " `uw`.`create_time` > {$month_zero} ";
			}
		}
		$sql .= ' WHERE ' . implode(' AND ', $whr);
		$sql .= " ORDER BY `u`.`id` DESC ";

		$res = $this->MySQL->paging($sql, 30, $p);
		return [
			'res' => $res
		];
	}

	public function controller1(int $p = 0, int $type = 0, int $time = 0) {
		$whr = [];
		if ($type == 0) {
			//vip
			$sql = " SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`v`.`status`,`cu`.`name` AS `cname`,`du`.`name` AS `dname`,`v`.`update_time` " .
					" FROM `yuemi_main`.`vip` AS `v` " .
					"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.id  = `v`.`user_id` " .
					"LEFT JOIN `yuemi_main`.`user` AS `cu` ON `cu`.`id` = `v`.`cheif_id` " .
					" LEFT JOIN `yuemi_main`.`cheif` AS `c` ON `c`.`user_id` = `v`.`cheif_id` " .
					"LEFT JOIN `yuemi_main`.`user` AS `du` ON `du`.`id` = `c`.`director_id` ";
			if ($time == 0) {
				//当日
				$date = date("Y-m-d", time());
				$today_zero = strtotime($date);
				$whr[] = " `v`.`update_time` > {$today_zero} ";
			} elseif ($time == 1) {
				//本月
				$date = date("Y-m", time());
				$month_zero = strtotime($date);
				$whr[] = " `v`.`update_time` > {$month_zero} ";
			}
			$sql .= ' WHERE ' . implode(' AND ', $whr);
			$sql .= " ORDER BY `u`.`id` DESC ";

			$res = $this->MySQL->paging($sql, 30, $p);
		} elseif ($type == 1) {
			//总监
			$sql = " SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`du`.`name` AS `dname`,`c`.`update_time`,`c`.`status` " .
					"FROM `yuemi_main`.`cheif` AS `c` " .
					"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`user_id` " .
					"LEFT JOIN `yuemi_main`.`user` AS `du` ON `du`.`id` = `c`.`director_id` ";
			if ($time == 0) {
				//当日
				$date = date("Y-m-d", time());
				$today_zero = strtotime($date);
				$whr[] = " `c`.`update_time` > {$today_zero} ";
			} elseif ($time == 1) {
				//本月
				$date = date("Y-m", time());
				$month_zero = strtotime($date);
				$whr[] = " `c`.`update_time` > {$month_zero} ";
			}
			$sql .= ' WHERE ' . implode(' AND ', $whr);
			$sql .= " ORDER BY `u`.`id` DESC ";

			$res = $this->MySQL->paging($sql, 30, $p);
			foreach ($res->Data AS $k => $v) {
				$res->Data[$k]['cname'] = '';
			}
		} elseif ($type == 2) {
			//总经理
			$sql = " SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`d`.`update_time`,`d`.`status` " .
					"FROM `yuemi_main`.`director` AS `d` " .
					"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `d`.`user_id` ";
			if ($time == 0) {
				//当日
				$date = date("Y-m-d", time());
				$today_zero = strtotime($date);
				$whr[] = " `d`.`update_time` > {$today_zero} ";
			} elseif ($time == 1) {
				//本月
				$date = date("Y-m", time());
				$month_zero = strtotime($date);
				$whr[] = " `d`.`update_time` > {$month_zero} ";
			}
			$sql .= ' WHERE ' . implode(' AND ', $whr);
			$sql .= " ORDER BY `u`.`id` DESC ";

			$res = $this->MySQL->paging($sql, 30, $p);
			foreach ($res->Data AS $k => $v) {
				$res->Data[$k]['cname'] = '';
			}
			foreach ($res->Data AS $k => $v) {
				$res->Data[$k]['dname'] = '';
			}
		}

		return [
			'res' => $res
		];
	}

	public function goods1(int $p = 0, int $time = 0) {
		$whr = [];
		$sql = " SELECT `k`.`id`,`k`.`title`,`k`.`status`,`k`.`create_user`,`k`.`create_time`,`sm`.`file_url`,`k`.`price_sale`,`u`.`name` AS `cname` FROM " .
				"`yuemi_sale`.`sku` AS `k` " .
				" LEFT JOIN `yuemi_sale`.`sku_material` AS `sm` ON `sm`.`sku_id` = `k`.`id` " .
				"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `k`.`create_user` ";
		if ($time == 0) {
			//当日
			$date = date("Y-m-d", time());
			$today_zero = strtotime($date);
			$whr[] = " `k`.`create_time` > {$today_zero} ";
		} elseif ($time == 1) {
			//本月
			$date = date("Y-m", time());
			$month_zero = strtotime($date);
			$whr[] = " `k`.`create_time` > {$month_zero} ";
		}
		$sql .= ' WHERE ' . implode(' AND ', $whr);
		$sql .= " GROUP BY `k`.`id` ORDER BY `k`.`id` DESC ";
		$res = $this->MySQL->paging($sql, 5, $p);
		return [
			'res' => $res
		];
	}

	public function user() {
		$date = date("Y-m-d", time());
		$today_zero = strtotime($date);

		$date = date("Y-m", time());
		$month_zero = strtotime($date);

		$umonth = $this->MySQL->row(" SELECT count(*) AS num FROM `yuemi_main`.`user` WHERE `reg_time` > $month_zero ");
		$wmonth = $this->MySQL->row(" SELECT count(*) AS num FROM `yuemi_main`.`user_wechat` WHERE `create_time` > $month_zero ");
		$mon = [];
		$mon['um'] = $umonth['num'];
		$mon['wm'] = $wmonth['num'];
		$arr = [];
		$tt = [];
		for ($i = 0; $i < 30; $i++) {
			$time = $today_zero - 86400 * $i;
			$dayend = $time + 86400;
			$arr[$i]['user'] = $this->MySQL->row(" SELECT count(*) AS num FROM `yuemi_main`.`user` WHERE `reg_time` >= {$time} AND `reg_time` < {$dayend}");
			$arr[$i]['wechat'] = $this->MySQL->row(" SELECT count(*) AS num FROM `yuemi_main`.`user_wechat` WHERE `create_time` >= $time  AND `create_time` < {$dayend}");
			$str = date("m-d", $time);
			$arr[$i]['time'] = $str;
			$tt[$i] = $str;
		}
		$r = 0;
		$lis = [];
		foreach ($arr as $k => $v) {
			if ($k % 8 == 0) {
				$r++;
			}
			$lis[$r][] = $v;
		}

		return [
			'time' => $tt,
			'mon' => $mon,
			'lis' => $lis
		];
	}

	public function controller() {
		$date = date("Y-m-d", time());
		$today_zero = strtotime($date);

		$date = date("Y-m", time());
		$month_zero = strtotime($date);

		$mv = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero");
		$mv0 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero AND `status` = 0");
		$mv1 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero AND `status` = 1");
		$mv2 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero AND `status` = 2");
		$mv3 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero AND `status` = 3");
		$mv4 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero AND `status` = 4");
		$mv5 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` > $month_zero AND `status` = 5");

		$mc = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` > $month_zero");
		$mc0 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` > $month_zero AND `status` = 0");
		$mc1 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` > $month_zero AND `status` = 1");
		$mc2 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` > $month_zero AND `status` = 2");
		$mc3 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` > $month_zero AND `status` = 3");

		$md = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` > $month_zero ");
		$md0 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` > $month_zero AND `status` = 0");
		$md1 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` > $month_zero AND `status` = 1");
		$md2 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` > $month_zero AND `status` = 2");

		$mon['mv'] = $mv['num'];
		$mon['mv0'] = $mv0['num'];
		$mon['mv1'] = $mv1['num'];
		$mon['mv2'] = $mv2['num'];
		$mon['mv3'] = $mv3['num'];
		$mon['mv4'] = $mv4['num'];
		$mon['mv5'] = $mv5['num'];

		$mon['mc'] = $mc['num'];
		$mon['mc0'] = $mc0['num'];
		$mon['mc1'] = $mc1['num'];
		$mon['mc2'] = $mc2['num'];
		$mon['mc3'] = $mc3['num'];

		$mon['md'] = $md['num'];
		$mon['md0'] = $md0['num'];
		$mon['md1'] = $md1['num'];
		$mon['md2'] = $md2['num'];

		$arr = [];
		$tt = [];
		for ($i = 0; $i < 30; $i++) {
			$time = $today_zero - 86400 * $i;
			$dayend = $time + 86400;
			$arr[$i]['vip'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time AND `update_time` < {$dayend} ");
			$arr[$i]['vip0'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 0 ");
			$arr[$i]['vip1'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 1 ");
			$arr[$i]['vip2'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 2 ");
			$arr[$i]['vip3'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 3 ");
			$arr[$i]['vip4'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 4 ");
			$arr[$i]['vip5'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`vip` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 5 ");
			$arr[$i]['cheif'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` >= $time  AND `update_time` < {$dayend} ");
			$arr[$i]['cheif0'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 0 ");
			$arr[$i]['cheif1'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 1 ");
			$arr[$i]['cheif2'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 2 ");
			$arr[$i]['cheif3'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`cheif` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 3 ");
			$arr[$i]['director'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` >= $time  AND `update_time` < {$dayend} ");
			$arr[$i]['director0'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 0 ");
			$arr[$i]['director1'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 1 ");
			$arr[$i]['director2'] = $this->MySQL->row(" SELECT count(*) AS `num` FROM `yuemi_main`.`director` WHERE `update_time` >= $time  AND `update_time` < {$dayend} AND `status` = 2 ");
			$str = date("m-d", $time);
			$arr[$i]['time'] = $str;
			$tt[$i] = $str;
		}
		$r = 0;
		$lis = [];
		foreach ($arr as $k => $v) {
			if ($k % 8 == 0) {
				$r++;
			}
			$lis[$r][] = $v;
		}
		return [
			'mon' => $mon,
			'lis' => $lis
		];
	}

	public function goods() {
		$date = date("Y-m-d", time());
		$today_zero = strtotime($date);

		$date = date("Y-m", time());
		$month_zero = strtotime($date);

		$mk0 = $this->MySQL->row("SELECT count(*) AS num FROM `yuemi_sale`.`sku` WHERE `create_time` >= $month_zero AND `status` = 0");
		$mk1 = $this->MySQL->row("SELECT count(*) AS num FROM `yuemi_sale`.`sku` WHERE `create_time` >= $month_zero AND `status` = 1");
		$mk2 = $this->MySQL->row("SELECT count(*) AS num FROM `yuemi_sale`.`sku` WHERE `create_time` >= $month_zero AND `status` = 2");
		$mk3 = $this->MySQL->row("SELECT count(*) AS num FROM `yuemi_sale`.`sku` WHERE `create_time` >= $month_zero AND `status` = 3");
		$mk4 = $this->MySQL->row("SELECT count(*) AS num FROM `yuemi_sale`.`sku` WHERE `create_time` >= $month_zero AND `status` = 4");

		$mk[0] = $mk0['num'];
		$mk[1] = $mk1['num'];
		$mk[2] = $mk2['num'];
		$mk[3] = $mk3['num'];
		$mk[4] = $mk4['num'];

		$arr = [];
		$tt = [];
		for ($i = 0; $i < 30; $i++) {
			$time = $today_zero - 86400 * $i;
			$dayend = $time + 86400;
			$str = date("m-d", $time);
			$tt[$i] = $str;
			$row0 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`sku` WHERE `create_time` >= {$time} AND `create_time` < {$dayend} AND `status` = 0");
			$row1 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`sku` WHERE `create_time` >= {$time} AND `create_time` < {$dayend} AND `status` = 1");
			$row2 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`sku` WHERE `create_time` >= {$time} AND `create_time` < {$dayend} AND `status` = 2");
			$row3 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`sku` WHERE `create_time` >= {$time} AND `create_time` < {$dayend} AND `status` = 3");
			$row4 = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`sku` WHERE `create_time` >= {$time} AND `create_time` < {$dayend} AND `status` = 4");
			$arr[$i][0] = $row0['num'];
			$arr[$i][1] = $row1['num'];
			$arr[$i][2] = $row2['num'];
			$arr[$i][3] = $row3['num'];
			$arr[$i][4] = $row4['num'];
			$arr[$i]['time'] = $str;
		}
		$r = 0;
		$lis = [];
		foreach ($arr as $k => $v) {
			if ($k % 8 == 0) {
				$r++;
			}
			$lis[$r][] = $v;
		}

		return [
			'time' => $tt,
			'mon' => $mk,
			'lis' => $lis
		];
	}

	public function order() {
		$date = date("Y-m-d", time());
		$today_zero = strtotime($date);

		$date = date("Y-m", time());
		$month_zero = strtotime($date);

		$arr = [];
		$tt = [];
		for ($i = 0; $i < 30; $i++) {
			$time = $today_zero - 86400 * $i;
			$dayend = $time + 86400;
			$str = date("m-d", $time);
			$arr[$i]['time'] = $str;
			$tt[$i] = $str;
			$arr[$i] = $this->MySQL->grid("SELECT count(*) AS `num`,`status` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$time} AND `create_time` < {$dayend} GROUP BY `status` ORDER BY `status` ");
		}
		foreach ($arr as $k => $v) {
			$time = $today_zero - 86400 * $k;
			$str = date("m-d", $time);
			$arr[$k]['time'] = $str;
			if (!empty($v)) {
				foreach ($v as $kk => $vv) {
					if ($vv['status'] == 1) {
						$arr[$k]['a1'] = $vv['num'];
					}
					if ($vv['status'] == 4) {
						$arr[$k]['a4'] = $vv['num'];
					}
					if ($vv['status'] == 5) {
						$arr[$k]['a5'] = $vv['num'];
					}
					if ($vv['status'] == 6) {
						$arr[$k]['a6'] = $vv['num'];
					}
					if ($vv['status'] == 7) {
						$arr[$k]['a7'] = $vv['num'];
					}
					if ($vv['status'] == 11) {
						$arr[$k]['a11'] = $vv['num'];
					}
					if ($vv['status'] == 12) {
						$arr[$k]['a12'] = $vv['num'];
					}
					if ($vv['status'] == 13) {
						$arr[$k]['a13'] = $vv['num'];
					}
				}
				if (!isset($arr[$k]['a1'])) {
					$arr[$k]['a1'] = 0;
				}
				if (!isset($arr[$k]['a4'])) {
					$arr[$k]['a4'] = 0;
				}
				if (!isset($arr[$k]['a5'])) {
					$arr[$k]['a5'] = 0;
				}
				if (!isset($arr[$k]['a6'])) {
					$arr[$k]['a6'] = 0;
				}
				if (!isset($arr[$k]['a7'])) {
					$arr[$k]['a7'] = 0;
				}
				if (!isset($arr[$k]['a11'])) {
					$arr[$k]['a11'] = 0;
				}
				if (!isset($arr[$k]['a12'])) {
					$arr[$k]['a12'] = 0;
				}
				if (!isset($arr[$k]['a13'])) {
					$arr[$k]['a13'] = 0;
				}
			} else {
				$arr[$k]['a1'] = 0;
				$arr[$k]['a4'] = 0;
				$arr[$k]['a5'] = 0;
				$arr[$k]['a6'] = 0;
				$arr[$k]['a7'] = 0;
				$arr[$k]['a11'] = 0;
				$arr[$k]['a12'] = 0;
				$arr[$k]['a13'] = 0;
			}
		}
		$r = 0;
		$lis = [];
		foreach ($arr as $k => $v) {
			if ($k % 8 == 0) {
				$r++;
			}
			$lis[$r][] = $v;
		}
		//本月
		$mon[1] = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$month_zero} AND `status` = 1");
		$mon[4] = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$month_zero} AND`status` = 4");
		$mon[5] = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$month_zero} AND `status` = 5");
		$mon[6] = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$month_zero} AND `status` = 6");
		$mon[7] = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$month_zero} AND `status` = 7");
		$mon[10] = $this->MySQL->row("SELECT count(*) AS `num` FROM `yuemi_sale`.`order` WHERE `create_time` >= {$month_zero} AND `status` = 11 OR `status` = 12 OR `status` = 13");

		return [
			'lis' => $lis,
			'time' => $tt,
			'mon' => $mon
		];
	}

	public function finance() {
		$date = date("Y-m-d", time());
		$today_zero = strtotime($date);

		$date = date("Y-m", time());
		$month_zero = strtotime($date);

		$arr = [];
		for ($i = 0; $i < 30; $i++) {
			$time = $today_zero - 86400 * $i;
			$dayend = $time + 86400;
			$str = date("m-d", $time);
			$arr[$i]['sell'] = $this->MySQL->row("SELECT sum(t_amount) AS sum FROM `yuemi_sale`.`order`  WHERE `create_time` >= {$time} AND `create_time` < {$dayend} AND `status` > 1 AND `status` < 9"); //TODO
			$ll = $this->MySQL->grid("SELECT `oi`.`sku_id`,`oi`.`qty`,`k`.`price_base` FROM `yuemi_sale`.`order` AS `o` LEFT JOIN `yuemi_sale`.`order_item` AS `oi` ON `oi`.`order_id` = `o`.`id` LEFT JOIN `yuemi_sale`.`sku` AS `k` ON `k`.`id` = `oi`.`sku_id` WHERE `o`.`create_time` >= {$time} AND `o`.`create_time` < {$dayend} AND `o`.`status` > 1 AND `o`.`status` < 9");
			$num = 0;
			foreach ($ll as $k => $v) {
				$num += $v['price_base'] * $v['qty'];
			}
			$arr[$i]['price'] = $num;
			$arr[$i]['time'] = $str;
		}
		$mon['sell'] = $this->MySQL->row("SELECT sum(t_amount) AS sum FROM `yuemi_sale`.`order`  WHERE `create_time` >= {$month_zero} AND `status` > 1 AND `status` < 9");
		$pp = $this->MySQL->grid("SELECT `oi`.`sku_id`,`oi`.`qty`,`k`.`price_base` FROM `yuemi_sale`.`order` AS `o` LEFT JOIN `yuemi_sale`.`order_item` AS `oi` ON `oi`.`order_id` = `o`.`id` LEFT JOIN `yuemi_sale`.`sku` AS `k` ON `k`.`id` = `oi`.`sku_id` WHERE `o`.`create_time` >= {$month_zero} AND `o`.`status` > 1 AND `o`.`status` < 9");
		$num = 0;
		foreach ($pp as $k => $v) {
			$num += $v['price_base'] * $v['qty'];
		}
		$mon['price'] = $num;
		$mon['money'] = $mon['sell']['sum'] - $num;

		$r = 0;
		$lis = [];
		$nn = [];
		foreach ($arr as $k => $v) {
			if ($k % 8 == 0) {
				$r++;
			}
			if (!isset($v['sell']['sum'])) {
				$nn['num'] = 0;
				$nn['price'] = $v['price'];
				$nn['money'] = $nn['num'] - $nn['price'];
				$nn['time'] = $v['time'];
				$lis[$r][] = $nn;
			} else {
				$nn['num'] = $v['sell']['sum'];
				$nn['time'] = $v['time'];
				$nn['price'] = $v['price'];
				$nn['money'] = $nn['num'] - $nn['price'];
				$lis[$r][] = $nn;
			}
		}
		return [
			'lis' => $lis,
			'mon' => $mon
		];
	}

}
