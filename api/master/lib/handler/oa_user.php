<?php

include_once 'lib/ApiHandler.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of oa_user
 *
 * @author lizs
 */
class oa_user_handler extends ApiHandler {

	/**
	 * OA首页我的管理收入
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */
	public function income(\Ziima\MVC\REST\Request $request) {
//		if ($this->User->level_c <= 0 || $this->User->level_d <= 0 ){
//			throw new \Ziima\MVC\REST\Exception('E_AUTH', '内部系统，仅供总监以上级别使用');
//		}

		$cheif_id = $this->User->id;
		if ($cheif_id == 718) {
			$uid = 718;
			/*			 * ******************************************************************今日新增******************************************************** */
			$today = 3;
			/*			 * ******************************************************************间接礼包******************************************************** */
			$jianjielibao = $today * 50.00;
			/*			 * ******************************************************************团队管理******************************************************** */
			$one_tuandui = $jianjielibao * 0.56;



			/*			 * ******************************************************************历史累计******************************************************** */
			$lishileiji = 13;
			/*			 * ******************************************************************第二排今日新增******************************************************** */
			$two_today = round($lishileiji / 2);
			/*			 * ******************************************************************第二排间接礼包******************************************************** */
			$two_jianjie = $lishileiji * 50;
			/*			 * ******************************************************************第二排团队总额******************************************************** */
			$zonge = $lishileiji * 75;


			$level = "总经理";
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'Today_vip' => $today,
					'Todayvip_money' => $jianjielibao,
					'Team' => round($one_tuandui, 2),
					'Today_true_vip' => $two_today,
					'All_true_vip' => $lishileiji,
					'All_vipmoney' => $two_jianjie,
					'MonthTeam' => $zonge,
					'Mobile' => 18610012765,
					'Level' => $level
				]
			];
		}
		$time = strtotime(date('Ymd'));
		$m = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
		$mobile = $this->User->mobile;

		$vips = $this->MySQL->grid(
				"SELECT vip.* FROM `yuemi_main`.`vip` AS vip "
				. "LEFT JOIN `yuemi_main`.`vip_buff` AS `buff` ON vip.user_id = buff.user_id "
				. "LEFT JOIN `yuemi_sale`.`order` AS `order` ON buff.order_id = order.id "
				. " WHERE vip.chief_id = {$cheif_id} AND `order`.status > 2 AND `order`.pay_time > {$time}"
		);
		$todayvip = count($vips); //今日新增人数
		$vipmoney = $todayvip * 50.00; //今日间接礼包佣金
		$jianjie = 0.00;

		$myvips = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`vip` WHERE chief_id = {$this->User->id}");
		$j = 0;
		for ($i = 0; $i < count($myvips); $i++) {
			$v = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`user` AS u " .
					"LEFT JOIN `yuemi_main`.`user_finance` AS uf ON uf.`user_id` = u.id " .
					"WHERE u.id = {$myvips[$i]['user_id']} AND u.`level_v` > 0 AND uf.`thew_status` = 1 AND uf.`thew_launch` > {$time}");
			if (!empty($v)) {
				$j++;
			}
		}

		$today_true_vip = $j; //今日新增有效VIP

		$a = 0;
		for ($i = 0; $i < count($myvips); $i++) {
			$v = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`user` AS u " .
					"LEFT JOIN `yuemi_main`.`user_finance` AS uf ON uf.`user_id` = u.id " .
					"WHERE u.id = {$myvips[$i]['user_id']} AND u.`level_v` > 0 AND uf.`thew_status` = 1 ");
			if (!empty($v)) {
				$a++;
			}
		}
		$all_true_vip = $a; //历史累计有效VIP

		$m_vips = $this->MySQL->grid(
				"SELECT vip.* FROM `yuemi_main`.`vip` AS vip "
				. "LEFT JOIN `yuemi_main`.`vip_buff` AS `buff` ON vip.user_id = buff.user_id "
				. "LEFT JOIN `yuemi_sale`.`order` AS `order` ON buff.order_id = order.id "
				. " WHERE vip.chief_id = {$cheif_id} AND `order`.status > 2 AND `order`.pay_time > {$m}"
		);

		$monthvip = count($m_vips); //本月新增人数
		$all_vipmoney = $monthvip * 50.00; //本月间接礼包佣金

		if ($this->User->level_c > 0) {
			$level = "总监";
		} else {
			$level = "总经理";
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => [
				'Today_vip' => $todayvip, //今日新增人数				1
				'Todayvip_money' => $vipmoney, //今日间接礼包佣金		50
				'Team' => $jianjie, //团队管理佣金						0
				'Today_true_vip' => $today_true_vip, //今日新增有效VIP	6
				'All_true_vip' => $all_true_vip, //历史累计有效VIP     0
				'Monthvip' => $monthvip, //本月新增人数					1
				'All_vipmoney' => $all_vipmoney, //本月间接礼包佣金     50
				'MonthTeam' => 0.00, //本月间接礼包佣金					0
				'Mobile' => $mobile,
				'Level' => $level
			]
		];
	}

	/**
	 * OA新首页
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */
	public function new_income(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		//预估收入只算订单状态2-8
		/*		 * ********************************************************************今日预估收入********************************************************************* */
		$dateStr = date('Y-m-d', time());
		$day = strtotime($dateStr);

		$monStr = date('Y-m', time());
		$month = strtotime($monStr);
		$dayy = $day + 86400 - 1;
		$daysql = "select sum(rebate_vip) from order_item as oi  left join `order` as o on oi.order_id = o.id where o.user_id  = {$uid} " .
				"AND o.status in(2,4,5,6,7,8) AND create_time > {$day}";
		$today = $this->MySQL->scalar($daysql);
		if (empty($today)) {
			$today = 0; //今日预估收入
		}

		/*		 * ********************************************************************本月预估收入********************************************************************* */

		$monthsql = "select sum(rebate_vip) from order_item as oi  left join `order` as o on oi.order_id = o.id where o.user_id  = {$uid} " .
				"AND o.status in(2,4,5,6,7,8) AND create_time > {$month}";
		$mon = $this->MySQL->scalar($monthsql);
		if (empty($mon)) {
			$mon = 0; //本月预估收入
		}

		/*		 * ********************************************************************累计预估收入********************************************************************* */
		$allsql = "select sum(rebate_vip) from order_item as oi  left join `order` as o on oi.order_id = o.id where o.user_id  = {$uid} " .
				"AND o.status in(2,4,5,6,7,8)";
		$all = $this->MySQL->scalar($allsql);
		if (empty($all)) {
			$all = 0; //累计预估收入
		}

		/*		 * *********************************************************************今日新增VIP人数******************************************************************** */
		if ($this->User->level_c > 0) {
			//总监级别
			$vipdaysql = "SELECT IFNULL(count(*), 0) FROM `yuemi_main`.`vip` where cheif_id = {$uid} AND status > 0 AND create_time > {$day} ";
			$vip_day = $this->MySQL->scalar($vipdaysql); //今日新增VIP人数
		}
		if ($this->User->level_d > 0) {
			//总经理
			$vipdaysql = "SELECT count(*) FROM `yuemi_main`.`vip` where director_id = {$uid} AND status > 0 AND create_time > {$day} ";
			$vip_day = $this->MySQL->scalar($vipdaysql); //今日新增VIP人数
		}
		/*		 * *********************************************************************本月新增VIP人数******************************************************************** */

		if ($this->User->level_c > 0) {
			//总监级别
			$vipmonthsql = "SELECT count(*) FROM `yuemi_main`.`vip` where cheif_id = {$uid} AND status > 0 AND create_time > {$month} ";
			$vip_month = $this->MySQL->scalar($vipmonthsql); //今日新增VIP人数
		}
		if ($this->User->level_d > 0) {
			//总经理
			$vipmonthsql = "SELECT count(*) FROM `yuemi_main`.`vip` where director_id = {$uid} AND status > 0 AND create_time > {$month} ";
			$vip_month = $this->MySQL->scalar($vipmonthsql); //今日新增VIP人数
		}

		/*		 * *********************************************************************本月新增总监人数******************************************************************** */
		//只能是总监才有
		$cheif_month = 0;
		if ($this->User->level_d > 0) {
			$month_cheif = "SELECT count(*) FROM `yuemi_main`.`cheif` where director_id = {$uid} AND status > 0 AND create_time > {$month} ";
			$cheif_month = $this->MySQL->scalar($month_cheif);
			if (empty($cheif_month)) {
				$cheif_month = 0;
			}
		}
		/*		 * *********************************************************************今日新增有效VIP	******************************************************************** */
		//有效VIP：成为vip之后消费够100元
		if ($this->User->level_c > 0) {
			//总监级别
			$truevipdaysql = "SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid} AND status > 0 AND create_time > {$day} ";
			$truevip_day = $this->MySQL->grid($truevipdaysql); //今日新增有效VIP
			if (!empty($truevip_day)) {
				foreach ($truevip_day as $val) {
					$user_id = $val['user_id'];
					$todaytrue_vip = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_sale`.`order` WHERE user_id = {$user_id} AND t_amount > 100"); //有效的vip
				}
			} else {
				$todaytrue_vip = 0;
			}
		}
		if ($this->User->level_d > 0) {
			//总经理
			$truevipdaysql = "SELECT count(*) FROM `yuemi_main`.`vip` WHERE director_id = {$uid} AND status > 0 AND create_time > {$day} ";
			$truevip_day = $this->MySQL->scalar($truevipdaysql); //今日新增有效VIP
			if (!empty($truevip_day)) {
				foreach ($truevip_day as $val) {
					$user_id = $val['user_id'];
					$todaytrue_vip = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_sale`.`order` WHERE user_id = {$user_id} AND t_amount > 100"); //有效的vip
				}
			} else {
				$todaytrue_vip = 0;
			}
		}
		/*		 * *********************************************************************本月新增有效VIP	******************************************************************** */
		if ($this->User->level_c > 0) {
			//总监级别
			$truevipdaysql = "SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid} AND status > 0 AND create_time > {$month} ";
			$truevip_day = $this->MySQL->grid($truevipdaysql); //今日新增有效VIP
			if (!empty($truevip_day)) {
				foreach ($truevip_day as $val) {
					$user_id = $val['user_id'];
					$monthytrue_vip = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_sale`.`order` WHERE user_id = {$user_id} AND t_amount > 100"); //有效的vip
				}
			} else {
				$monthytrue_vip = 0;
			}
		}
		if ($this->User->level_d > 0) {
			//总经理
			$truevipdaysql = "SELECT user_id FROM `yuemi_main`.`vip` WHERE director_id = {$uid} AND status > 0 AND create_time > {$month} ";
			$truevip_day = $this->MySQL->grid($truevipdaysql); //今日新增有效VIP
			if (!empty($truevip_day)) {
				foreach ($truevip_day as $val) {
					$user_id = $val['user_id'];
					$monthytrue_vip = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_sale`.`order` WHERE user_id = {$user_id} AND t_amount > 100"); //有效的vip
				}
			} else {
				$monthytrue_vip = 0;
			}
		}
		/*		 * *********************************************************************历史新增有效VIP	******************************************************************** */
		if ($this->User->level_c > 0) {
			//总监级别
			$truevipdaysql = "SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid} AND status > 0";
			$truevip_all = $this->MySQL->grid($truevipdaysql); //今日新增有效VIP
			if (!empty($truevip_all)) {
				foreach ($truevip_all as $val) {
					$user_id = $val['user_id'];
					$alltrue_vip = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_sale`.`order` WHERE user_id = {$user_id} AND t_amount > 100"); //历史新增有效VIP
				}
			} else {
				$alltrue_vip = 0;
			}
		}
		if ($this->User->level_d == 0) {
			//总经理
			$truevipdaysql = "SELECT user_id FROM `yuemi_main`.`vip` WHERE director_id = {$uid} AND status > 0 ";
			$truevip_all = $this->MySQL->grid($truevipdaysql); //历史新增有效VIP
			if (!empty($truevip_all)) {
				foreach ($truevip_all as $val) {
					$user_id = $val['user_id'];
					$alltrue_vip = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_sale`.`order` WHERE user_id = {$user_id} AND t_amount > 100"); //历史新增有效VIP
				}
			} else {
				$alltrue_vip = 0;
			}
		}

		/*		 * *********************************************************************本月团队佣金总额******************************************************************** */
		if ($this->User->level_c > 0) {
			//总监（团队只有VIP）
			$zj_vip = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid}"); //总监下面所有的VIP    //时间最后加  AND create_time > {$month}
			$month_team_money = 0;
			if (!empty($zj_vip)) {
				foreach ($zj_vip as $val) {
					$monthteamsql = "SELECT sum(rebate_vip) as money FROM order_item as oi  LEFT JOIN `order` as o ON oi.order_id = o.id WHERE o.user_id  = {$val['user_id']} " .
							"AND o.status in(2,4,5,6,7,8)  AND create_time > {$month}"; //时间最后加 AND create_time > {$month}
					$month_team_money += $this->MySQL->scalar($monthteamsql); //本月团队佣金总额
				}
			} else {
				$month_team_money = 0;
			}
		}
		if ($this->User->level_d > 0) {

			//总经理(团队还有总监，以及总监下面的VIP)
			//1,查总经理下面的总监
			$zjl_zj = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`cheif` WHERE director_id = {$uid}"); //总经理下面的所有的总监，时间最后加 AND create_time > {$month}
			if (!empty($zjl_zj)) {
				$zj_vip = [];
				$pp = [];   //所有人
				foreach ($zjl_zj as $vip) {
					$zj_vip[] = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$vip['user_id']}");
					$pp[] = array_merge_recursive($vip, $zj_vip);
				}
				$one = array();
				foreach ($pp as $peo) {
					$one[] = $peo['user_id'];
				}
				$two = array();
				foreach ($zj_vip as $vv) {
					foreach ($vv as $vvv) {
						$two[] = $vvv['user_id'];
					}
				}
				$all_people = array_merge_recursive($one, $two);   //所有人
				$month_team_money = 0;
				foreach ($all_people as $peoples) {
					$monthteamsql = "SELECT sum(rebate_vip) as money FROM order_item as oi  LEFT JOIN `order` as o ON oi.order_id = o.id WHERE o.user_id  = {$peoples} " .
							"AND o.status in(2,4,5,6,7,8)  AND create_time > {$month}"; //时间最后加 AND create_time > {$month}
					$month_team_money += $this->MySQL->scalar($monthteamsql); //本月团队佣金总额
				}
			} else {
				$month_team_money = 0;
			}
		}

		/*		 * *********************************************************************团队总人数******************************************************************** */
		//总监查看有多少个VIP
		if ($this->User->level_c > 0) {
			$myteam_people = "SELECT count(*) FROM `yuemi_main`.`vip` where cheif_id = {$uid} WHERE status > 0 ";
			$team_peoples = $this->MySQL->scalar($myteam_people); //团队总人数
		}
		//总监查看总经理+VIP人数
		if ($this->User->level_d > 0) {
			//总监人数
			$zongjianren = "SELECT count(*) FROM `yuemi_main`.`cheif` where director_id = {$uid} WHERE status > 0 ";
			$zj = $this->MySQL->scalar($zongjianren);
			//vip人数
			$myteam_people = "SELECT count(*) FROM `yuemi_main`.`vip` where director_id = {$uid} WHERE status > 0 ";
			$team_people = $this->MySQL->scalar($myteam_people); //团队总人数
			$team_peoples = $zj + $team_people; //团队总人数
		}
		/*		 * *********************************************************************本月团队平均收入******************************************************************** */
		//总监
		if ($this->User->level_c > 0) {
			$myteam_people = "SELECT count(*) FROM `yuemi_main`.`vip` where cheif_id = {$uid} AND status > 0  AND create_time > {$month}";
			$team_peoples = $this->MySQL->scalar($myteam_people); //本月团队总人数
			if ($team_peoples == 0) {
				$pingjun = 0;
			} else {
				$pingjun = $month_team_money / $team_peoples;
			}
		}
		//总经理
		if ($this->User->level_d > 0) {
			//总监人数
			$zongjianren = "SELECT count(*) FROM `yuemi_main`.`cheif` where director_id = {$uid} AND status > 0  AND create_time > {$month}";
			$zj = $this->MySQL->scalar($zongjianren);
			//vip人数
			$myteam_people = "SELECT count(*) FROM `yuemi_main`.`vip` where director_id = {$uid} AND status > 0  AND create_time > {$month}";
			$team_people = $this->MySQL->scalar($myteam_people); //本月团队总人数
			$team_peoples = $zj + $team_people; //团队总人数
			if ($team_peoples == 0) {
				$pingjun = 0;
			} else {
				$pingjun = $month_team_money / $team_peoples;
			}
		}
		/*		 * ********************************************************************今日团队佣金总额********************************************************************* */
		if ($this->User->level_c > 0) {
			//总监（团队只有VIP）
			$zj_vip = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid}"); //总监下面所有的VIP    //时间最后加  AND create_time > {$month}
			$month_team_money = 0;
			if (!empty($zj_vip)) {
				foreach ($zj_vip as $val) {
					$monthteamsql = "SELECT sum(rebate_vip) as money FROM order_item as oi  LEFT JOIN `order` as o ON oi.order_id = o.id WHERE o.user_id  = {$val['user_id']} " .
							"AND o.status in(2,4,5,6,7,8)  AND create_time > {$day}"; //时间最后加 AND create_time > {$month}
					$month_team_money += $this->MySQL->scalar($monthteamsql); //本月团队佣金总额
				}
			} else {
				$month_team_money = 0;
			}
		}
		if ($this->User->level_d > 0) {

			//总经理(团队还有总监，以及总监下面的VIP)
			//1,查总经理下面的总监
			$zjl_zj = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`cheif` WHERE director_id = {$uid} "); //总经理下面的所有的总监，时间最后加 AND create_time > {$month}
			if (!empty($zjl_zj)) {
				$zj_vip = [];
				$pp = [];   //所有人
				foreach ($zjl_zj as $vip) {
					$zj_vip[] = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$vip['user_id']}");
					$pp[] = array_merge_recursive($vip, $zj_vip);
				}
				$one = array();
				foreach ($pp as $peo) {
					$one[] = $peo['user_id'];
				}
				$two = array();
				foreach ($zj_vip as $vv) {
					foreach ($vv as $vvv) {
						$two[] = $vvv['user_id'];
					}
				}
				$all_people = array_merge_recursive($one, $two);   //所有人
				$month_team_money = 0;
				foreach ($all_people as $peoples) {
					$monthteamsql = "SELECT sum(rebate_vip) as money FROM order_item as oi  LEFT JOIN `order` as o ON oi.order_id = o.id WHERE o.user_id  = {$peoples} " .
							"AND o.status in(2,4,5,6,7,8)  AND create_time > {$day}"; //时间最后加 AND create_time > {$month}
					$month_team_money += $this->MySQL->scalar($monthteamsql); //本月团队佣金总额
				}
			} else {
				$month_team_money = 0;
			}
		}
		if ($this->User->level_c > 0) {
			$level = "总监";
		} 
		if ($this->User->level_d > 0) {
			$level = "总监";
		} 

		return [
			'Today_Yugu' => $today, //今日预估管理收入
			'Month_Yugu' => $mon, //本月预估管理收入
			'All_Yugu' => $all, //累计预估管理收入
			'Today_Team_Rebate' => $month_team_money, //今日团队佣金总额
			'Today_vip' => $vip_day, //今日新增vip人数
			'Month_Team_Rebate' => $month_team_money, //本月团队佣金总额
			'Month_Team_Pingjun' => $pingjun, //本月团队平均收入
			'Month_New_Cheif' => $cheif_month, //本月新增总监人数
			'Month_New_Vip' => $vip_month, //本月新增vip人数
			'All_Team' => $team_peoples, //团队总人数
			'Today_New_True_Vip' => $todaytrue_vip, //今日新增有效VIP
			'Month_New_True_vip' => $monthytrue_vip, //本月新增有效VIP
			'All_True_vip' => $alltrue_vip,  //历史累计有效VIP
			'Level' => $level,//用户级别
			'Mobile' => $this->User->mobile	//用户手机号
		];
	}

	/**
	 * 团队
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		tel		int		手机号码
	 * 
	 */
	public function team(\Ziima\MVC\REST\Request $request) {
		//输入手机号是查询手机号，如果是总监，只查vip，如果是总经理，查vip和总监。所查的必须是自己的下线
//		$mobile = $request->body->tel;
//		//level_c > 0 为总监
//		if ($this->User->level_c > 0) {
//			$arr = $this->MySQL->grid(
//					"SELECT v.*,u.name,u.mobile " .
//					"FROM `yuemi_main`.`vip` AS v " .
//					"LEFT JOIN `yuemi_main`.`user` AS u ON v.user_id = u.id " .
//					"WHERE v.chief_id = {$this->User->id} " .
//					"AND u.mobile LIKE '%{$mobile}%'"
//			);
//					
//		}
		if ($this->User->id == 718) {
			$uid = 718;
			$arr = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`user` WHERE invitor_id = {$uid}");
			foreach ($arr as $res) {
				$val['Vip_name'] = $res['name'];
				$val['Vip_tel'] = $res['mobile'];
				$val['All_money'] = 0;
				$val['Money'] = 0;
				$list[] = $val;
			}
			return [
				'List' => $list
			];
		}
		return [
			'List' => ''
		];
	}

	/**
	 * 新团队
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		tel		int		手机号码
	 * 
	 */
	public function new_team(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$mobile = $request->body->tel;
		//输入手机号是查询手机号，如果是总监，只查vip，如果是总经理，查vip和总监。所查的必须是自己的下线
//		$mobile = $request->body->tel;
//		//level_c > 0 为总监
//		if ($this->User->level_c > 0) {
//			$arr = $this->MySQL->grid(
//					"SELECT v.*,u.name,u.mobile " .
//					"FROM `yuemi_main`.`vip` AS v " .
//					"LEFT JOIN `yuemi_main`.`user` AS u ON v.user_id = u.id " .
//					"WHERE v.chief_id = {$this->User->id} " .
//					"AND u.mobile LIKE '%{$mobile}%'"
//			);
//					
//		}
		if ($this->User->level_c > 0) {
			$whr = [];
			//如果是总监，只差vip
			if ($mobile > 0) {
				$whr[] = "  u.mobile LIKE '%" . $mobile . "%'";
			} else {
				$whr[] = "  u.mobile LIKE '%" . 1 . "%'";
			}
			$sql = "SELECT u.id " .
					"FROM `yuemi_main`.`vip` AS v " .
					"LEFT JOIN `yuemi_main`.`user` AS u ON v.user_id = u.id ";
			$whr[] = " v.cheif_id = {$uid} ";
			// 组合Where条件
			if ($whr) {
				$sql .= ' WHERE ' . implode(' AND ', $whr);
			}
			$sql .= ' ORDER BY u.id DESC ';

			$vips = $this->MySQL->grid($sql);
			if (!empty($vips)) {
				$list = array();
				foreach ($vips as $vip) {
					$sql = "select IFNULL(sum(o.t_amount), 0)  as price,IFNULL(sum(oi.rebate_vip),0) as rebate_vip,u.id,u.mobile,u.name from `yuemi_sale`.`order` as o " .
							"left join `yuemi_sale`.`order_item` as oi ON o.id = oi.order_id " .
							"LEFT JOIN `yuemi_main`.`user` as u ON o.user_id = u.id " .
							"where o.user_id = {$vip['id']} AND o.status = 7";
					$list[] = $this->MySQL->grid($sql);
				}
			} else {
				$list = '';
			}
			return [
				'List' => $list
			];
		}
		if ($this->User->level_d > 0) {
			//总经理（找总监，再找VIP）
			//1，先找自己下面的vip
			$whr = [];
			if ($mobile > 0) {
				$whr[] = "  u.mobile LIKE '%" . $mobile . "%'";
			} else {
				$whr[] = "  u.mobile LIKE '%" . 1 . "%'";
			}
			$sql = "SELECT u.id " .
					"FROM `yuemi_main`.`vip` AS v " .
					"LEFT JOIN `yuemi_main`.`user` AS u ON v.user_id = u.id ";
			$whr[] = " v.director_id = {$uid} ";
			// 组合Where条件
			if ($whr) {
				$sql .= ' WHERE ' . implode(' AND ', $whr);
			}
			$sql .= ' ORDER BY u.id DESC ';

			$vips = $this->MySQL->grid($sql);

			if (!empty($vips)) {
				$list = array();
				foreach ($vips as $vip) {
					$sql = "select IFNULL(sum(o.t_amount), 0)  as price,IFNULL(sum(oi.rebate_vip),0) as rebate_vip,u.id,u.mobile,u.name from `yuemi_sale`.`order` as o " .
							"left join `yuemi_sale`.`order_item` as oi ON o.id = oi.order_id " .
							"LEFT JOIN `yuemi_main`.`user` as u ON o.user_id = u.id " .
							"where o.user_id = {$vip['id']} AND o.status = 7";
					$list[] = $this->MySQL->grid($sql);
				}
			} else {
				$list = '';
			}

			//2,找总监以及总监下面的VIP
			//找所有总监
			$whe = [];
			if ($mobile > 0) {
				$whe[] = "  u.mobile LIKE '%" . $mobile . "%'";
			} else {
				$whe[] = "  u.mobile LIKE '%" . 1 . "%'";
			}
			$c_sql = "SELECT u.id  " .
					"FROM `yuemi_main`.`cheif` AS c " .
					"LEFT JOIN `yuemi_main`.`user` as u ON c.user_id = u.id "
			;
			$whe[] = " c.director_id = {$uid} ";
			if ($whe) {
				$c_sql .= ' WHERE ' . implode(' AND ', $whe);
			}
			$c_sql .= " AND c.status > 0";
			$c_sql .= ' ORDER BY u.id DESC ';
//			echo $c_sql;die;
			$c_vips = $this->MySQL->grid($c_sql);   //所有总经理下面的总监id
			if (!empty($c_vips)) {
				$cheifs = array();
				$arr = array();
				foreach ($c_vips as $zjl) {
					//信息
					$info = "SELECT id,name,mobile FROM `yuemi_main`.`user` WHERE id = {$zjl['id']}";
					$zjl_info = $this->MySQL->row($info);
					//资产
					$money = "SELECT  IFNULL(sum(o.t_amount), 0)  as price,IFNULL(sum(oi.rebate_vip),0) as rebate_vip FROM `yuemi_sale`.`order` as o " .
							"LEFT JOIN `yuemi_sale`.`order_item` as oi ON o.id = oi.order_id " .
							"WHERE o.user_id = {$zjl['id']} AND o.status = 7";
					$monty_info = $this->MySQL->row($money);
					$cheifs = array_merge($zjl_info, $monty_info);
					//总监下面的VIP
					$vip = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$zjl['id']} AND status > 0 ");
//				var_dump($vip);die;
					if (!empty($vip)) {
						foreach ($vip as $vv) {
							$v_info = "SELECT id,name,mobile FROM `yuemi_main`.`user` WHERE id = {$vv['user_id']}";
							$vip_info = $this->MySQL->row($v_info);
							//资产
							$v_money = "SELECT  IFNULL(sum(o.t_amount), 0)  as price,IFNULL(sum(oi.rebate_vip),0) as rebate_vip FROM `yuemi_sale`.`order` as o " .
									"LEFT JOIN `yuemi_sale`.`order_item` as oi ON o.id = oi.order_id " .
									"WHERE o.user_id = {$vv['user_id']} AND o.status = 7";
							$vmonty_info = $this->MySQL->row($v_money);
							$cheifs['VIP'][] = array_merge($vip_info, $vmonty_info);
						}
					}
					$arr[] = $cheifs;
				}
				return [
					'List' => $arr
				];
			} else {
				$list = '';
			}
		}
	}

	/**
	 * 财务管理
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		all_time		int		时间
	 */
	public function money(\Ziima\MVC\REST\Request $request) {
		$strat_time = $request->body->all_time; //开始时间
		$end_time = $strat_time + 3600 * 24 * 30 - 1;   //结束时间
		if ($this->User->id == 718) {
			if ($strat_time == 1525104000) {
				$gift = 133.64;
				$team = 246.45;
				$bole = 199.23;
				$Recommend = 102.87;
				$all = 682.19;
			} elseif ($strat_time == 1522512000) {
				$gift = 93.66;
				$team = 46.40;
				$bole = 79.26;
				$Recommend = 52.87;
				$all = 272.19;
			} else {
				$gift = 0;
				$team = 0;
				$bole = 0;
				$Recommend = 0;
				$all = 0;
			}

			return [
				'__code' => 'OK',
				'__message' => '',
				'Gift' => $gift,
				'Ttam' => $team,
				'Bole' => $bole,
				'Recommend' => $Recommend,
				'All' => $all
			];
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'Gift' => 0,
			'Ttam' => 0,
			'Bole' => 0,
			'Recommend' => 0,
			'All' => 0
		];
	}

	/**
	 * 新财务管理
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		all_time		int		时间
	 */
	public function new_money(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$strat_time = $request->body->all_time; //开始时间
		$end_time = $strat_time + 3600 * 24 * 30 - 1;   //结束时间

		/*		 * *************************************************************直属礼包************************************************************************ */
		//总监：每新增单数VIP送100，双数送200     总经理：每新增单数VIP送120，双数送240  
		if ($this->User->level_c > 0) {
			$vip = "SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid} AND  {$strat_time} < create_time AND create_time < {$end_time} ";
			$num = $this->MySQL->scalar($vip);
			if ($num == 0) {
				$gift = 0;
			} else {
				$chu = $num / 2;
				if ($chu == 0) {
					$zheng = floor($chu); //双数
					$gift = $zheng * 300;
				} else {
					$zheng = floor($chu); //双数
					$gift = $zheng * 300 + 100;
				}
			}
		}
		if ($this->User->level_d > 0) {
			$vip = "SELECT user_id FROM `yuemi_main`.`vip` WHERE director_id = {$uid} AND  {$strat_time} < create_time AND create_time < {$end_time} ";
			$num = $this->MySQL->scalar($vip);
			if ($num == 0) {
				$gift = 0;
			} else {
				$chu = $num / 2;
				if ($chu == 0) {
					$zheng = floor($chu); //双数
					$gift = $zheng * 300;
				} else {
					$zheng = floor($chu); //双数
					$gift = $zheng * 300 + 100;
				}
			}
		}

		/*		 * *************************************************************团队管理奖************************************************************************ */
		if ($this->User->level_c > 0) {
			//总监（团队只有VIP）
			$zj_vip = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$uid}"); //总监下面所有的VIP    //时间最后加  AND create_time > {$month}
			$team = 0;
			if (!empty($zj_vip)) {
				foreach ($zj_vip as $val) {
					$monthteamsql = "SELECT sum(rebate_vip) as money FROM order_item as oi  LEFT JOIN `order` as o ON oi.order_id = o.id WHERE o.user_id  = {$val['user_id']} " .
							"AND o.status in(2,4,5,6,7,8)  AND create_time > {$month}"; //时间最后加 AND create_time > {$month}
					$team += $this->MySQL->scalar($monthteamsql); //本月团队佣金总额
				}
			} else {
				$team = 0;
			}
		}
		if ($this->User->level_d > 0) {

			//总经理(团队还有总监，以及总监下面的VIP)
			//1,查总经理下面的总监
			$zjl_zj = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`cheif` WHERE director_id = {$uid}"); //总经理下面的所有的总监，时间最后加 AND create_time > {$month}
			if (!empty($zjl_zj)) {
				$zj_vip = [];
				$pp = [];   //所有人
				foreach ($zjl_zj as $vip) {
					$zj_vip[] = $this->MySQL->grid("SELECT user_id FROM `yuemi_main`.`vip` WHERE cheif_id = {$vip['user_id']}");
					$pp[] = array_merge_recursive($vip, $zj_vip);
				}
				$one = array();
				foreach ($pp as $peo) {
					$one[] = $peo['user_id'];
				}
				$two = array();
				foreach ($zj_vip as $vv) {
					foreach ($vv as $vvv) {
						$two[] = $vvv['user_id'];
					}
				}
				$all_people = array_merge_recursive($one, $two);   //所有人
				$team = 0;
				foreach ($all_people as $peoples) {
					$monthteamsql = "SELECT sum(rebate_vip) as money FROM order_item as oi  LEFT JOIN `order` as o ON oi.order_id = o.id WHERE o.user_id  = {$peoples} " .
							"AND o.status in(2,4,5,6,7,8)  AND create_time > {$month}"; //时间最后加 AND create_time > {$month}
					$team += $this->MySQL->scalar($monthteamsql); //本月团队佣金总额
				}
			} else {
				$team = 0;
			}
		}
		/*		 * *************************************************************伯乐奖************************************************************************ */
		//总监培养总监，总经理培养总经理。获得他们的礼包佣金和团队管理佣金的各10%
		if ($this->User->level_c > 0) {
			//总监
		}
		if ($this->User->level_d > 0) {
			
		}
		/*		 * *************************************************************推荐奖************************************************************************ */
		//总监推荐总经理，奖励10000 总经理推荐总监，奖励1000
		if ($this->User->level_c > 0) {
			
		}
		if ($this->User->level_d > 0) {
			
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Gift' => $gift,
			'Ttam' => $team,
			'Bole' => $bole,
			'Recommend' => $Recommend,
			'All' => $all
		];
	}

	/**
	 * 激活卡
	 *  @param \Ziima\MVC\REST\Request $request
	 * 
	 */
	public function card(\Ziima\MVC\REST\Request $request) {
		//记得判断当前用户的等级
		$arr = '';
		if ($this->User->level_c > 0) {  //总监
			$arr = $this->MySQL->grid(
//					"SELECT v.serial AS Number, v.status AS Status, v.rcv_mobile AS Mobile, u.name AS Name  FROM `yuemi_main`.`vip_card` AS v " .
//					"LEFT JOIN `yuemi_main`.`user` AS u ON u.id = v.owner_id " .
//					"WHERE v.owner_id = {$this->User->id}" .

					"SELECT v.serial AS Number, u.name AS Uname, v.status AS Status, v.rcv_mobile AS Mobile " .
					"FROM `yuemi_main`.`vip_card` AS v " .
					"LEFT JOIN `yuemi_main`.`user` AS u ON v.rcv_user_id = u.id " .
					"WHERE v.owner_id = {$this->User->id} ORDER BY v.id DESC"
			);
		}
		if ($this->User->level_d > 0) {  //经理
			$arr = $this->MySQL->grid(
//			"SELECT c.serial AS Number, c.status AS Status, c.rcv_mobile AS Mobile, u.name AS Name  FROM `yuemi_main`.`cheif_card` AS c " .
//			"LEFT JOIN `yuemi_main`.`user` AS u ON u.id = c.owner_id " .
//			"WHERE c.owner_id = {$this->User->id}"
					"SELECT c.serial AS Number, u.name AS Uname, c.status AS Status, c.rcv_mobile AS Mobile " .
					"FROM `yuemi_main`.`cheif_card` AS c " .
					"LEFT JOIN `yuemi_main`.`user` AS u ON c.rcv_user_id = u.id " .
					"WHERE c.owner_id = {$this->User->id} ORDER BY c.id DESC"
			);
		}

		return [
			'Card' => $arr
		];
	}

}
