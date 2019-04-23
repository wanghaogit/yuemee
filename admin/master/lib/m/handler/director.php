<?php

include "lib/AdminHandler.php";

/**
 * 总经理管理
 * @auth
 */
class director_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 列表
	 */
	public function index(int $p = 0, string $n = '', string $m = '') {
		$time = Z_NOW;
		
		$sql = "SELECT `d`.*,`u`.`name`,`u`.`mobile`,`uc`.`card_name` AS `cname` " .
				"FROM `yuemi_main`.`director` AS `d` " .
				"LEFT JOIN `yuemi_main`.`user` AS `u` " .
				"ON `u`.`id` = `d`.`user_id`".
				" LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = `d`.`user_id` ".
				" WHERE `d`.`expire_time` > {$time}";
		$whr = [];
		if ($n !== '') {
			$whr[] = " `u`.`name` LIKE '%{$n}%' ";
		}
		if ($m !== '') {
			$whr[] = " `u`.`mobile` = '{$m}' ";
		}
		
		$search_time_start = strtotime($_GET['search_time_start'] ?? ""); // 开始时间
		$search_time_end = strtotime($_GET['search_time_end'] ?? ""); // 结束时间
		if ($search_time_start > 0)
			$whr[] = " d.expire_time >= {$search_time_start} ";
		if ($search_time_end > 0)
			$whr[] = " d.expire_time <= {$search_time_end} ";
		
		$search_time_start2 = strtotime($_GET['search_time_start2'] ?? ""); // 开始时间
		$search_time_end2 = strtotime($_GET['search_time_end2'] ?? ""); // 结束时间
		if ($search_time_start2 > 0){
			$whr[] = " d.create_time >= {$search_time_start2} ";
		}
		if ($search_time_end2 > 0){
			$whr[] = " d.create_time <= {$search_time_end2} ";
		}	
			
			
		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `d`.`expire_time` DESC ";
		$res = $this->MySQL->paging($sql, 20, $p);
		for ($i = 0; $i < count($res->Data); $i++) {
			$res->Data[$i]['expire_time'] = date('Y-m-d H:i:s', $res->Data[$i]['expire_time']);
		}
		return [
			'data' => $res,
			'sum' => $this->MySQL->row("SELECT count(*) AS `sum` FROM `yuemi_main`.`director`")
		];
	}

	/**
	 * 创建总经理
	 */
	public function create() {
		return [
			'OrderId' => \Ziima\Zid::Default()->order('K', 'D'),
			'BankList' => $this->MySQL->map("SELECT `id`,`name` FROM `bank`", 'id', 'name'),
		];
	}

	/**
	 * 卡位订单
	 */
	public function order(int $p = 0, string $n = '', string $m = '',int $status = 0) {
		$sql = "SELECT `ds`.* ,"
				. "`u`.`mobile`,`u`.`name`,`di`.`user_id`,`di`.`user_id` "
				. "FROM `yuemi_main`.`director_buff` AS `ds` "
				. " LEFT JOIN `yuemi_main`.`director` AS `di` ON `di`.`user_id` = `ds`.`user_id`"
				. "LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `di`.`user_id`";
		$whr = [];
		if ($n !== '') {
			$whr[] = " `u`.`name` LIKE '%{$n}%' ";
		}
		if ($m !== '') {
			$whr[] = " `u`.`mobile` = '{$m}' ";
		}
		if ($status != 0 ){
			$status = $status - 1;
			$whr[] = " `ds`.`pay_status` = '{$status}' ";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `ds`.`create_time` DESC ";
		$res = $this->MySQL->paging($sql, 30, $p);
		for ($i = 0; $i < count($res->Data); $i++) {
			$res->Data[$i]['pay_time'] = date('Y-m-d H:i:s', $res->Data[$i]['pay_time']);
			$res->Data[$i]['create_time'] = date('Y-m-d H:i:s', $res->Data[$i]['create_time']);
		}
		return [
			'data' => $res
		];
	}

	/**
	 * 经理账目
	 */
	public function finance(int $p = 0) {
		$sql = "SELECT `df`.* ,"
				. "`u`.`mobile`,`u`.`name` "
				. "FROM `yuemi_main`.`director_finance` AS `df` "
				. "LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `df`.`user_id`";

		$res = $this->MySQL->paging($sql, 30, $p);
		return [
			'data' => $res,
		];
	}

	/**
	 * 直营团队
	 */
	public function team(int $p = 0) {
		$sql = "SELECT t.*,d.expire_time AS d_expiretime, u.mobile AS Mobile, v.expire_time AS v_expiretime " .
				"FROM `yuemi_main`.`team` AS t " .
				"LEFT JOIN `yuemi_main`.`director` AS d ON t.director_id = d.user_id " .
				"LEFT JOIN `yuemi_main`.`user` AS u ON d.user_id = u.id " .
				"LEFT JOIN `yuemi_main`.`vip` AS v ON d.user_id = v.user_id "
		;
		$result = $this->MySQL->paging($sql, 30, $p);
//		PRINT_R($result);DIE;
		return [
			'Result' => $result
		];
	}

	/**
	 * 创建团队
	 */
	public function team_create() {
		if ($this->Context->Runtime->ticket->postback) {
			$director_id = (int)$_POST['director_id'];
			$name = $this->MySQL->encode($_POST['name']);
			$this->MySQL->execute("INSERT INTO `yuemi_main`.`team` (`director_id`,`name`,`create_user`,`create_time`,`create_from`) VALUES (%d,'%s',%d,'%s','%s')",
					$director_id,
					$name,
					$_SESSION['UserId'],
					date('Y-m-d H:i:s', time()),
					$this->Context->Runtime->ticket->ip
			);

			throw new \Ziima\MVC\Redirector('/index.php?call=director.team');
		}
		$sql = 'SELECT d.`id`,u.`name` FROM `yuemi_main`.`director` d ' .
				'LEFT JOIN `yuemi_main`.`user` u ON d.`user_id`=u.`id` ORDER BY d.`id`';
		$res = $this->MySQL->grid($sql);

		return [
			'res' => $res
		];
	}

	/**
	 * 直营员工管理
	 */
	public function staff(int $p = 0, int $tid = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`team_member` ";
		$whr = [];
		if ($tid > 0) {
			$whr[] = "`team_id` = $tid";
		}
		if ($whr) {
			$sql .= 'WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY `id` DESC';
		$res = $this->MySQL->paging($sql, 30, $p);

		return [
			'Result' => $res,
		];
	}

	/**
	 * 直营绩效
	 */
	public function perform(int $p = 0, int $tid = 0) {
		
	}

	/**
	 * 直营工资
	 */
	public function salary(int $p = 0, int $tid = 0) {
		
	}

	

	public function give_director(int $id = 0, int $t = 0) {
		if ($t == 1) {
			//开通
			$this->MySQL->execute("UPDATE `yuemi_main`.`director` SET `status` = 3 WHERE `user_id` = {$id} ");
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `leave_d` = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=director.index');
		} else {
			//取消
			$this->MySQL->execute("UPDATE `yuemi_main`.`director` SET `status` = 0 WHERE `user_id` = {$id} ");
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `leave_d` = 0 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=director.index');
		}
	}

//	public function directorinv_pic(int $uid = 0) {
//		$my = $this->MySQL->row("SELECT `id`,`name` FROM `yuemi_main`.`user` AS `u` WHERE `u`.`id` = {$uid}");
//		$cheif = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`user_id` WHERE `c`.`director_id` = {$uid}");
//		$arr['self']['id'] = $my['id'];
//		$arr['self']['name'] = $my['name'];
//		$arr['child']['id'] = $cheif['id'];
//		$arr['child']['name'] = $cheif['name'];
//		
//		return [
//			'cheif' => $cheif2,
//			'my' => $my
//		];
//	}
	public function directorinv_pic(int $uid = 0) {
		$my = $this->MySQL->row("SELECT `id`,`name`,`mobile` FROM `yuemi_main`.`user` AS `u` WHERE `u`.`id` = {$uid}");
		$cheif = $this->MySQL->grid("SELECT `id`,`name`,`mobile` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`user_id` WHERE `c`.`director_id` = {$uid}");
		$arr = [];
		foreach($cheif AS $k=>$v){
			$arr[$k]['self']['id'] = $v['id'];
			$arr[$k]['self']['name'] = $v['name'];
			$arr[$k]['self']['mobile'] = $v['mobile'];
		}
		$cheif2 = $this->get_viptree($arr);
		foreach($cheif2 AS $k => $v){
			foreach($v['vip'] AS $kk => $vv){
				$cheif2[$k]['vip'][$kk]['self']['id'] = $cheif2[$k]['vip'][$kk]['id'];
				$cheif2[$k]['vip'][$kk]['self']['name'] = $cheif2[$k]['vip'][$kk]['name'];
				$cheif2[$k]['vip'][$kk]['self']['v'] = $cheif2[$k]['vip'][$kk]['level_v'];
				$cheif2[$k]['vip'][$kk]['self']['mobile'] = $cheif2[$k]['vip'][$kk]['mobile'];
				$cheif2[$k]['vip'][$kk]['child'] = $this->get_person($cheif2[$k]['vip'][$kk]['self']['id'],1);
			}
		}
		$a = [];
		foreach($cheif2 AS $k=>$v){
			foreach($v['vip'] AS $kk=>$vv){
//				var_dump($cheif2[$k]['vip'][$kk]);echo '<hr>';
				$a[0] = $cheif2[$k]['vip'][$kk];
				$cheif2[$k]['vip'][$kk]['html'] = $this->get_html($a);
			}
		}

		return [
			'cheif' => $cheif2,
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
	
	private function get_viptree($data) {
		foreach ($data as $k => $v) {
			$data[$k]['vip'] = $this->MySQL->grid("SELECT `u`.`id`,`u`.`name`,`u`.`level_v`,`u`.`mobile` FROM `yuemi_main`.`vip` AS `v` LEFT JOIN `yuemi_main`.`user` AS `u` ON `v`.`user_id` = `u`.`id` WHERE `v`.`cheif_id` = {$v['self']['id']}");
		}
		return $data;
	}

	private function get_html($list) {
		$html = '';
		foreach ($list as $t) {
			if ($t['child'] == null) {
				if($t['self']['v'] > 0){
					$html .= "<li><a href='/index.php?call=user.vipinv_pic&uid=".$t['self']['id']."' style='background-color:#B22222;color:#FFF;' title='手机号：".$t['self']['mobile']."'>" . $t['self']['name'] . '</a>';
				}else{
					$html .= "<li><a href='#' title='".$t['self']['mobile']."'>{$t['self']['name']}</a></li>";
				}
			} else {
				if($t['self']['v'] > 0){
					$html .= "<li><a href='/index.php?call=user.vipinv_pic&uid=".$t['self']['id']."' style='background-color:#B22222;color:#FFF;' title='手机号：".$t['self']['mobile']."'>" . $t['self']['name'] . '</a>';
				}else{
					$html .= "<li><a href='#' title='手机号：".$t['self']['mobile']."'>" . $t['self']['name'] . '</a>';
				}
				$html .= $this->get_html($t['child']);
				$html = $html . "</li>";
			}
		}
		return $html ? '<ul>' . $html . '</ul>' : $html;
	}

	private function get_director_tree($uid, $level) {
		$data = $this->MySQL->grid("SELECT `c`.`user_id` AS `id`,`u`.`name` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`user_id` WHERE `c`.`director_id` = {$uid}");
		$level++;
		if (!empty($data) && $level < 7) {
			$tree = [];
			foreach ($data as $v) {
				$child = $this->get_director_tree($v['id'], $level);
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

}
