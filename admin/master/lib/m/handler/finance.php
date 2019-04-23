<?php

include "lib/AdminHandler.php";

/**
 * 财务平台
 * @auth
 */
class finance_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	public function charge(int $p = 0) {
		
	}

	public function withdraw(int $p = 0) {
		
	}

	public function bonus(int $p = 0) {
		
	}

	public function salary(int $p = 0) {
		
	}

	public function team(int $p = 0) {
		
	}

	public function supplier(int $p = 0) {
		
	}

	public function offline() {
		
	}

	public function card() {
		
	}

	public function tally_coin(int $p = 0, int $uid = 0) {
		$sql = "SELECT `t`.*,`u`.`name`".
				" FROM `yuemi_main`.`tally_coin` AS `t` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `t`.`user_id` ";
		$whr = [];
		if ($uid > 0)
			$whr[] = "`t`.`user_id` = $uid";

		if ($whr) {
			$sql .= " WHERE " . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 30, $p);
		return[
			'Result' => $result
		];
	}

	public function tally_money(int $p = 0, int $u = 0,int $uid = 0) {
		$sql = "SELECT t.*,u.name ".
				"FROM `yuemi_main`.`tally_money` AS `t` ".
				"left join `yuemi_main`.`user` as u ".
				"on u.id=t.user_id";
		$whr = [];
		if ($u > 0){
			$whr[] = "`t`.`user_id` = $u";
		}
		if($uid > 0){
			$whr[] = "`t`.`user_id` = {$uid}";
		}
		
		if ($whr) {
			$sql .= " WHERE " . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 30, $p);
		return[
			'Result' => $result
		];
	}

	public function tally_profit(int $p = 0, int $u = 0, $t = 0) {
		$sql = "SELECT t.*,`u`.`name` " .
				"FROM `yuemi_main`.`tally_profit` AS `t` ".
				"left join `yuemi_main`.`user` as u ".
				"on u.id=t.user_id";
		$whr = [];
		if ($u > 0)
			$whr[] = "`t`.`user_id` = $u";

		if ($whr) {
			$sql .= " WHERE " . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 30, $p);
		return[
			'Result' => $result,
			't' => $t
		];
	}

	public function tally_recruit(int $p = 0, int $u = 0, $t = 0) {
		$sql = "SELECT t.*,u.name ".
				"FROM `yuemi_main`.`tally_recruit` AS `t` ".
				"left join `yuemi_main`.`user` as u ".
				"on u.id=t.user_id";
		$whr = [];
		if ($u > 0)
			$whr[] = "`t`.`user_id` = $u";

		if ($whr) {
			$sql .= " WHERE " . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 30, $p);
		return[
			'Result' => $result,
			't' => $t
		];
	}
	

}
