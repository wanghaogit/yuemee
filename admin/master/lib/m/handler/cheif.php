<?php

include "lib/AdminHandler.php";

/**
 * 总监管理
 * @auth
 */
class cheif_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 总监列表
	 */
	public function index(int $p = 0, string $n = '', string $m = '') {
		$time = Z_NOW;

		$sql = "SELECT `c`.*,`u`.`mobile`,`u`.`name`,`uu`.`name` AS `iname`,`uc2`.card_name AS `drcname`,`uc`.`card_name` AS `cname`  FROM `yuemi_main`.`cheif` AS `c` " .
				"LEFT JOIN `yuemi_main`.`user`  AS `u` ON `u`.`id` = `c`.`user_id` " .
				" LEFT JOIN `yuemi_main`.`user_cert` AS `uc` on `c`.`user_id` = `uc`.`user_id` " .
				" LEFT JOIN `yuemi_main`.`user_cert` AS `uc2` ON `uc2`.`user_id` = `c`.`director_id` " .
				"LEFT JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `c`.`director_id` WHERE `c`.`expire_time` > {$time}";

		$whr = [];
		if ($n !== '') {
			$whr[] = " `u`.`name` LIKE '%{$n}%' ";
		}
		if ($m !== '') {
			$whr[] = " `u`.`mobile` = '{$m}' ";
		}

		$search_time_start = strtotime($_GET['search_time_start'] ?? ""); // 开始时间
		$search_time_end = strtotime($_GET['search_time_end'] ?? ""); // 结束时间
		if ($search_time_start > 0){
			$whr[] = " c.expire_time >= {$search_time_start} ";
		}
		if ($search_time_end > 0){
			$whr[] = " c.expire_time <= {$search_time_end} ";
		}

		$search_time_start2 = strtotime($_GET['search_time_start2'] ?? ""); // 开始时间
		$search_time_end2 = strtotime($_GET['search_time_end2'] ?? ""); // 结束时间
		if ($search_time_start2 > 0){
			$whr[] = " c.create_time >= {$search_time_start2} ";
		}
		if ($search_time_end2 > 0){
			$whr[] = " c.create_time <= {$search_time_end2} ";
		}



		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `c`.`expire_time` DESC ";
		$res = $this->MySQL->paging($sql, 20, $p);

		return [
			'data' => $res,
			'sum' => $this->MySQL->row("SELECT count(*) AS `sum` FROM `yuemi_main`.`cheif`")
		];
	}

	/**
	 * 创建总监
	 */
	public function create() {
		return [
			'BankList' => $this->MySQL->map("SELECT `id`,`name` FROM `bank`", 'id', 'name'),
			'OrderId' => \Ziima\Zid::Default()->order('K', 'C')
		];
	}

	/**
	 * 卡位订单
	 */
	public function order(int $p = 0, string $n = '', string $m = '', int $status = 0) {
		$sql = "SELECT `cs`.*,"
				. "`u`.`mobile`,`u`.`name`,`cc`.`user_id` ,`cc`.`user_id` "
				. "FROM `yuemi_main`.`cheif_buff` AS `cs` "
				. " LEFT JOIN `yuemi_main`.`cheif` AS `cc` ON `cc`.`user_id` = `cs`.`user_id` "
				. "LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `cs`.`user_id`";
		$whr = [];
		if ($n !== '') {
			$whr[] = " `u`.`name` LIKE '%{$n}%' ";
		}
		if ($m !== '') {
			$whr[] = " `u`.`mobile` = '{$m}' ";
		}
		if ($status != 0) {
			$status = $status - 1;
			$whr[] = " `cs`.`pay_status` = '{$status}' ";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `cs`.`create_time` DESC ";
		$res = $this->MySQL->paging($sql, 30, $p);
		for ($i = 0; $i < count($res->Data); $i++) {
			$res->Data[$i]['pay_time'] = date('Y-m-d H:i:s', $res->Data[$i]['pay_time']);
			$res->Data[$i]['create_time'] = date('Y-m-d H:i:s', $res->Data[$i]['create_time']);
		}
		return [
			'data' => $res,
		];
	}

	/**
	 * 总监账目
	 */
	public function finance(int $p = 0) {
		$sql = "SELECT `cf`.*,"
				. "`u`.`mobile`,`u`.`name` "
				. "FROM `yuemi_main`.`cheif_finance` AS `cf` "
				. "LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `cf`.`user_id`";

		$res = $this->MySQL->paging($sql, 30, $p);
		for ($i = 0; $i < count($res->Data); $i++) {
			$res->Data[$i]['thew_time'] = date('Y-m-d H:i:s', $res->Data[$i]['thew_time']);
		}
		return [
			'data' => $res,
		];
	}

	public function give_cheif(int $id = 0, int $t = 0) {
		if ($t == 1) {
			//开通
			$this->MySQL->execute("UPDATE `yuemi_main`.`cheif` SET `status` = 3 WHERE `user_id` = {$id} ");
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `leave_c` = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=cheif.index');
		} else {
			//取消
			$this->MySQL->execute("UPDATE `yuemi_main`.`cheif` SET `status` = 0 WHERE `user_id` = {$id} ");
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `leave_c` = 0 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=cheif.index');
		}
	}

	public function delorder(int $id = 0) {
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`cheif_buff` WHERE `id` = {$id} ");
		throw new \Ziima\MVC\Redirector('/index.php?call=cheif.order');
	}

	/*
	 * 总监邀请卡片
	 */

	public function cheif_card(int $uid = 0, int $p = 0) {
		$sql = "SELECT c.*, u.name AS Uname " .
				"FROM `yuemi_main`.`cheif_card` AS c " .
				"LEFT JOIN `yuemi_main`.`user` AS u ON c.rcv_user_id = u.id ";
		if ($uid > 0) {
			$sql .= " WHERE c.owner_id = {$uid} ";
		}
		$res = $this->MySQL->paging($sql, 30, $p);
		return [
			'Data' => $res
		];
	}

	public function vip_card(int $uid = 0) {

		$sql = "SELECT v.*, u.name AS Uname " .
				"FROM `yuemi_main`.`vip_card` AS v " .
				"LEFT JOIN `yuemi_main`.`user` AS u ON v.rcv_user_id = u.id ";

		if ($uid > 0) {
			$sql .= " WHERE v.owner_id = {$uid} ";
		}
		$res = $this->MySQL->paging($sql);
		return [
			'Data' => $res
		];
	}

	public function cheifinv_pic(int $uid = 0) {
		$my = $this->MySQL->row("SELECT `id`,`name`,`mobile` FROM `yuemi_main`.`user` AS `u` WHERE `u`.`id` = {$uid}");
		$vip = $this->MySQL->grid("SELECT `id`,`name`,`level_v`,`mobile` FROM `yuemi_main`.`vip` AS `v` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `v`.`user_id` WHERE `v`.`cheif_id` = {$uid} ");
		$director = $this->MySQL->row("SELECT `u`.`name`,`u`.`id`,`u`.`mobile` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`director_id` WHERE `c`.`user_id` = {$uid}");
		if (empty($director['id'])) {
			$director = '';
		}
		$list = $this->get_cheif_tree($uid, 1);
//		if (!empty($list)) {
//			$html = $this->get_html($list);
//		} else {
//			$html = '';
//		}
		foreach ($vip AS $k => $v) {
			$vip[$k]['self']['id'] = $v['id'];
			$vip[$k]['self']['name'] = $v['name'];
			$vip[$k]['self']['v'] = $v['level_v'];
			$vip[$k]['self']['mobile'] = $v['mobile'];
			$vip[$k]['child'] = $this->get_person($v['id'], 1);
		}

		$a = [];
		foreach ($vip AS $k => $v) {
//				var_dump($cheif2[$k]['vip'][$kk]);echo '<hr>';
			$a[0] = $vip[$k];
			$vip[$k]["html"] = $this->get_html($a);
		}
		return [
			'vip' => $vip,
			'director' => $director,
			'my' => $my
		];
	}

	private function get_person($uid, $level) {
		$data = $this->MySQL->grid("SELECT `id`,`name`,`level_v` AS `v`,`mobile` FROM `yuemi_main`.`user` WHERE `invitor_id` = {$uid}");
		$level++;
		if (!empty($data) && $level < 7) {
			$tree = [];
			foreach ($data as $v) {
				$child = $this->get_person($v['id'], $level);
				$tree[] = array('self' => $v, 'child' => $child);
			}
			return $tree;
		}
	}

	private function get_html($list) {
		$html = '';
		foreach ($list as $t) {
			if ($t['child'] == null) {
				if ($t['self']['v'] > 0) {
					$html .= "<li><a href='/index.php?call=user.vipinv_pic&uid=" . $t['self']['id'] . "' style='background-color:#B22222;color:#FFF;' title='手机号：" . $t['self']['mobile'] . "'>" . $t['self']['name'] . '</a>';
				} else {
					$html .= "<li><a href='#' title='手机号：" . $t['self']['mobile'] . "'>{$t['self']['name']}</a></li>";
				}
			} else {
				if ($t['self']['v'] > 0) {
					$html .= "<li><a href='/index.php?call=user.vipinv_pic&uid=" . $t['self']['id'] . "' style='background-color:#B22222;color:#FFF;' title='手机号：" . $t['self']['mobile'] . "'>" . $t['self']['name'] . '</a>';
				} else {
					$html .= "<li><a href='#' title='" . $t['self']['mobile'] . "'>" . $t['self']['name'] . '</a>';
				}
				$html .= $this->get_html($t['child']);
				$html = $html . "</li>";
			}
		}
		return $html ? '<ul>' . $html . '</ul>' : $html;
	}

	private function get_cheif_tree($uid, $level) {
		$data = $this->MySQL->grid("SELECT `v`.`user_id` AS `id`,`u`.`name` FROM `yuemi_main`.`vip` AS `v` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `v`.`user_id` WHERE `v`.`cheif_id` = {$uid}");
		$level++;
		if (!empty($data) && $level < 7) {
			$tree = [];
			foreach ($data as $v) {
				$child = $this->get_cheif_tree($v['id'], $level);
				$tree[] = array('self' => $v, 'child' => $child);
			}
			return $tree;
		}
	}

	/**
	 * 获取邀请关系父级（总监）
	 * @param type $uid
	 */
	private function get_inv_parent($uid) {
		//user
		$row1 = $this->MySQL->row("SELECT `uu`.`id`,`uu`.`name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`user` AS `uu` ON `u`.`invitor_id` = `uu`.`id` WHERE `u`.`id` = {$uid}");
		//vip
		$row2 = $this->MySQL->row("SELECT u.`id`,`u`.`name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`vip` AS `v` ON `v`.`cheif_id` = `u`.`id` WHERE `v`.`user_id` = {$uid}");
		//cheif
		$row3 = $this->MySQL->row("SELECT `u`.`id`,`u`.name FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`cheif` AS `c` ON `c`.`director_id` = `u`.`id` WHERE `c`.`user_id` = {$uid}");
		//director
		if (!empty($row1['id'])) {
			$arr = $row1;
		} else {
			if (!empty($row2['id'])) {
				$arr = $row2;
			} else {
				if (!empty($row3['id'])) {
					$arr = $row3;
				} else {
					$arr = '';
				}
			}
		}
		return $arr;
	}

	public function share_order(int $uid = 0, int $p = 0) {
		$list = $this->MySQL->paging("SELECT `oi`.*,`ca`.`name` AS `catname`,`u`.`name` AS `rename`,`uu`.`name` AS `buyname`,`su`.`name` AS `suname` FROM `yuemi_sale`.`order_item` AS `oi` " .
				"LEFT JOIN `yuemi_sale`.`share` AS `s` ON `s`.`id` = `oi`.`share_id` " .
				" LEFT JOIN `yuemi_sale`.`catagory` AS `ca` ON `ca`.`id` = `oi`.`catagory_id`" .
				" LEFT JOIN `yuemi_main`.`supplier` AS `su` ON `su`.`id` = `oi`.`supplier_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `oi`.`rebate_user` " .
				" LEFT JOIN `yuemi_sale`.`order` AS `o` ON `o`.`id` = `oi`.`order_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `o`.`user_id` " .
				"WHERE `s`.`user_id` = {$uid}", 25, $p);
		return [
			'res' => $list
		];
	}

	public function share_good(int $uid = 0, int $p = 0) {
		$my = $this->MySQL->row("SELECT `name`,`id` FROM `yuemi_main`.`user` WHERE `id` = {$uid}");
		$sql = "SELECT `s`.`id`,`s`.`title`,`s`.`image_url`,`s`.`create_time`,`s`.`user_id`,`u`.`name`,`k`.`id` AS `sku_id` FROM `yuemi_sale`.`share` AS `s`" .
				" LEFT JOIN `yuemi_sale`.`sku` AS `k` ON `k`.`id` = `s`.`sku_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `s`.`user_id` " .
				" WHERE `s`.`user_id` = {$uid} GROUP BY `k`.`id`";
		$sql = "SELECT `id`,`sku_id`,`create_time`,`image_url`,`title` FROM `yuemi_sale`.`share` WHERE `user_id` = {$uid} ";
		$list = $this->MySQL->grid($sql);
		$arr = [];
		$res = [];
		foreach ($list as $v) {
			if (in_array($v['sku_id'], $arr)) {
				
			} else {
				$arr[] = $v['sku_id'];
				$res[] = $v;
			}
		}
		return [
			'my' => $my,
			'res' => $res
		];
	}

	public function share_good_order(int $uid = 0, $sku_id = 0, $p = 0) {
		$list = $this->MySQL->paging("SELECT `oi`.*,`ca`.`name` AS `catname`,`uu`.name AS `buyname`,`u`.`name` AS `rename`,`o`.`create_time`,`su`.`name` AS `suname` FROM `yuemi_sale`.`share` AS `s`" .
				" LEFT JOIN `yuemi_sale`.`order_item` AS `oi` ON `oi`.`share_id` = `s`.`id` " .
				" LEFT JOIN `yuemi_sale`.`catagory` AS `ca` ON `ca`.`id` = `oi`.`catagory_id`" .
				" LEFT JOIN `yuemi_main`.`supplier` AS `su` ON `su`.`id` = `oi`.`supplier_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `oi`.`rebate_user` " .
				" LEFT JOIN `yuemi_sale`.`order` AS `o` ON `o`.`id` = `oi`.`order_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `o`.`user_id` " .
				" WHERE `s`.`sku_id` = {$sku_id} AND `s`.`user_id` = {$uid} AND `oi`.`sku_id` = {$sku_id}", 25, $p);
		return [
			'res' => $list
		];
	}

	public function share_good_get_money(int $uid = 0, $sku_id = 0, $p = 0) {
		$ll = $this->MySQL->row("SELECT `title` FROM `yuemi_sale`.`sku` WHERE `id` = {$sku_id}");
		$sql = " SELECT `r`.* FROM `yuemi_sale`.`rebate` AS `r` " .
				"LEFT JOIN `yuemi_sale`.`share` AS `s` ON `s`.`id` = `r`.`share_id` " .
				"WHERE `s`.`user_id` = {$uid} AND `s`.`sku_id` = {$sku_id} ";
		$list = $this->MySQL->paging($sql, 20, $p);
		return [
			'res' => $list,
			'good' => $ll['title']
		];
	}

}
