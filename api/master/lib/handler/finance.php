<?php

include_once 'lib/ApiHandler.php';

/**
 * 用户财务接口
 */
class finance_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 余额流水查询 tally_money（每页20条）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		dir			int			方向（0任意,1收入,2支出)
	 * @request		src			string		来源（空任意，其它，匹配）
	 * @request		page		int			页码
	 */
	public function tally_money(\Ziima\MVC\REST\Request $request) {

		//第一个修改点：SQL语句，换行对齐
		$sql = "SELECT `source`,`val_before`,`val_delta`,`val_after`,`message`,`create_time` " .
				"FROM `yuemi_main`.`tally_money` ";
		//第二个修改点：$whr 可以用于任意多个条件
		$whr = [];
		$whr[] = "`user_id` = '" . $this->User->id . "'";
		if ($request->body->dir == 1) {
			$whr[] = "`val_delta` >  0";
		} elseif ($request->body->dir == 2) {
			$whr[] = "`val_delta` < 0";
		}
		if (!empty($request->body->src)) {
			//非常重要：@request 的 string 类型，一定要用 encode
			$whr[] = "`source` = '" . $this->MySQL->encode($request->body->src) . "'";
		}
		//第三个修改点：不要修改
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		//最后ORDER BY
		$sql .= ' ORDER BY `id` DESC ';

		$result = $this->MySQL->paging($sql, 20, $request->body->page);

		if (empty($result)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => ''
			];
		} else {
			$arr = array();
			foreach ($result->Data as $res) {
				$list['Money'] = $res['val_delta'];
				$list['Source'] = $res['source'];
				$list['Time'] = $res['create_time'];
				$list['Message'] = $res['message'];
				$arr[] = $list;
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => [
					'DataCount' => $result->DataCount,
					'PageSize' => $result->PageSize,
					'PageCount' => $result->PageCount,
					'PageIndex' => $result->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 阅币流水查询 tally_coin（每页20条）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		dir			int			方向（0任意,1收入,2支出)
	 * @request		src			string		来源（空任意，其它，匹配）
	 * @request		page		int			页码
	 */
	public function tally_coin(\Ziima\MVC\REST\Request $request) {

		$sql = "SELECT `source`,`val_before`,`val_delta`,`val_after`,`message`,`create_time` " .
				"FROM `yuemi_main`.`tally_coin`";
		$whr = [];
		$whr[] = "`user_id` = '" . $this->User->id . "'";
		if ($request->body->dir == 1) {
			$whr[] = "`val_delta` >  0";
		} elseif ($request->body->dir == 2) {
			$whr[] = "`val_delta` < 0";
		}
		if (!empty($request->body->src)) {
			//非常重要：@request 的 string 类型，一定要用 encode
			$whr[] = "`source` = '" . $this->MySQL->encode($request->body->src) . "'";
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		$sql .= ' ORDER BY `id` DESC ';
		$result = $this->MySQL->paging($sql, 20, $request->body->page);

		if (empty($result)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => ''
			];
		} else {
			$arr = array();
			foreach ($result->Data as $res) {
				$list['Money'] = $res['val_delta'];
				$list['Source'] = $res['source'];
				$list['Time'] = $res['create_time'];
				$list['Message'] = $res['message'];
				$arr[] = $list;
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => [
					'DataCount' => $result->DataCount,
					'PageSize' => $result->PageSize,
					'PageCount' => $result->PageCount,
					'PageIndex' => $result->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 销售佣金流水查询 tally_profit（每页20条）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		type		string		类型（''|self|share|team)
	 * @request		dir			int			方向（0任意,1收入,2支出)
	 * @request		src			string		来源（空任意，其它，匹配）
	 * @request		page		int			页码
	 */
	public function tally_profit(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `target`, `source`,`val_before`,`val_delta`,`val_after`,`message`,`create_time` " .
				"FROM `yuemi_main`.`tally_profit`";
		$whr = [];
		$whr[] = "`user_id` = '" . $this->User->id . "'";
		if (!empty($request->body->type)) {
			$whr[] = "`target` = '" . $this->MySQL->encode($request->body->type) . "'";
		}

		if (!empty($request->body->src)) {
			$whr[] = "`source` = '" . $this->MySQL->encode($request->body->src) . "'";
		}
		if ($request->body->dir == 1) {
			$whr[] = "`val_delta` >  0";
		} elseif ($request->body->dir == 2) {
			$whr[] = "`val_delta` < 0";
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		$sql .= ' ORDER BY `id` DESC ';

		$result = $this->MySQL->paging($sql, 20, $request->body->page);

		if (empty($result)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => ''
			];
		} else {
			$arr = array();
			foreach ($result->Data as $res) {
				$list['Money'] = $res['val_delta'];
				$list['Source'] = $res['source'];
				$list['Time'] = $res['create_time'];
				$list['Message'] = $res['message'];
				$list['Sarget'] = $res['target'];
				$arr[] = $list;
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => [
					'DataCount' => $result->DataCount,
					'PageSize' => $result->PageSize,
					'PageCount' => $result->PageCount,
					'PageIndex' => $result->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 招聘佣金流水查询 tally_recruit（每页20条）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		type		string		类型（''|dir|alt)
	 * @request		dir			int			方向（0任意,1收入,2支出)
	 * @request		src			string		来源（空任意，其它，匹配）
	 * @request		page		int			页码
	 */
	public function tally_recruit(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `target`, `source`,`val_before`,`val_delta`,`val_after`,`message`,`create_time` " .
				"FROM `yuemi_main`.`tally_recruit`";
		$whr = [];
		$whr[] = "`user_id` = '" . $this->User->id . "'";
		if (!empty($request->body->type)) {
			$whr[] = "`target` = '" . $this->MySQL->encode($request->body->type) . "'";
		}

		if (!empty($request->body->src)) {
			$whr[] = "`source` = '" . $this->MySQL->encode($request->body->src) . "'";
		}
		if ($request->body->dir == 1) {
			$whr[] = "`val_delta` >  0";
		} elseif ($request->body->dir == 2) {
			$whr[] = "`val_delta` < 0";
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		$sql .= ' ORDER BY `id` DESC ';

		$result = $this->MySQL->paging($sql, 20, $request->body->page);

		if (empty($result)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => ''
			];
		} else {
			$arr = array();
			foreach ($result->Data as $res) {
				$list['Money'] = $res['val_delta'];
				$list['Source'] = $res['source'];
				$list['Time'] = $res['create_time'];
				$list['Message'] = $res['message'];
				$list['Sarget'] = $res['target'];
				$arr[] = $list;
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => [
					'DataCount' => $result->DataCount,
					'PageSize' => $result->PageSize,
					'PageCount' => $result->PageCount,
					'PageIndex' => $result->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 提现申请列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		page		int			页码
	 */
	public function withdraw_list(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT u.*,b.name " .
				"FROM `yuemi_main`.`user_withdraw` AS u " .
				"LEFT JOIN `yuemi_main`.`bank` AS b ON u.`bank_id` = b.`id` " .
				"WHERE u.user_id = " . $this->User->id;

		$re = $this->MySQL->paging($sql, 10, $request->body->page);
		if (empty($re)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => ''
			];
		} else {
			$arr = array();
			foreach ($re->Data as $res) {
				$list['id'] = $res['id'];  //提现记录ID
				$list['money'] = $res['total']; //兑换总金额
				$list['status'] = $res['status']; //状态
				$list['c_time'] = $res['create_time']; //创建时间
				$arr = $list;
			}

			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 发起提现申请
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		total		float			提现总金额
	 * @request		money		float			从余额提取部分
	 * @request		profit		float			从销售佣金提取部分
	 * @request		recruit		float			从礼包佣金提取部分
	 */
	public function withdraw_request(\Ziima\MVC\REST\Request $request) {
		//检查下参数
		$UserId = $this->User->id;

		$ReqMoney = $request->body->money;

		$ReqProfit = $request->body->profit;

		$ReqRecruit = $request->body->recruit;

		$ReqBankId = $request->body->bankid;

		$ClientIp = $this->Context->Runtime->ticket->ip;

		$ret = \yuemi_main\ProcedureInvoker::Instance()->withdraw_request($UserId, $ReqMoney, $ReqProfit, $ReqRecruit, $ReqBankId, $ClientIp);
		if ($ret === null) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库系统错误');
		}
		if ($ret->ReturnValue != 'OK') {
			throw new \Ziima\MVC\REST\Exception($ret->ReturnValue, $ret->ReturnMessage);
		}
		return [
			'WithdrawId' => $ret->WithdrawId
		];
	}

	/**
	 * 放弃提现申请
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int			申请记录ID
	 */
	public function withdraw_cancel(\Ziima\MVC\REST\Request $request) {

		$WithdrawId = $this->User->id;

		$ClientIp = $this->Context->Runtime->ticket->ip;
		
		$ret = \yuemi_main\ProcedureInvoker::Instance()->withdraw_cancel($WithdrawId, $ClientIp);
		if ($ret === null) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库系统错误');
		}
		if ($ret->ReturnValue != 'OK') {
			throw new \Ziima\MVC\REST\Exception($ret->ReturnValue, $ret->ReturnMessage);
		}
		return [
			'WithdrawId' => $ret->WithdrawId
		];
	}

	/**
	 * 提现申请状态查询
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int			申请记录ID
	 */
	public function withdraw_detail(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT u.*,b.name " .
				" FROM `user_withdraw` AS u " .
				"LEFT JOIN `yuemi_main`.`bank` AS b ON u.`bank_id` = b.`id` " .
				"WHERE u.id = %d";
		$result = $this->MySQL->grid($sql, $request->body->id);

		if (empty($sql)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => ''
			];
		} else {
			$arr[] = array();
			foreach ($result as $res) {
				$list['id'] = $res['id']; //订单ID
				$list['money'] = $res['total']; //体现金额
				$list['card_no'] = $res['card_no']; //银行卡号
				$list['status'] = $res['status'];  //状态，0提交,1审核,2打款,3完成,4拒绝
				$list['bank_name'] = $res['bank_name']; //银行名称
				$list['c_time'] = $res['create_time']; //创建时间
				$list['a_time'] = $res['audit_time']; //审核时间
				$list['p_time'] = $res['process_time']; //处理时间
				$list['f_time'] = $res['finish_time']; //完成时间
				$arr = $list;
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				//尽量优雅
				'Tally' => $arr
			];
		}
	}

}
