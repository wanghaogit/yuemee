<?php

include_once 'lib/ApiHandler.php';

/**
 * 任务系统接口
 * @auth
 */
class task_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 签到列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		month		int		查询月份
	 */
	public function sign_list(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `month_id` AS `Month`";
		for ($i = 1; $i <= 31; $i ++) {
			$sql .= ",`day_$i` AS `Day_$i`";
		}
		$sql .= " FROM `yuemi_main`.`task_sign` " .
				" WHERE `user_id` = %d AND `month_id` = %d";
		return [
			'__code' => 'OK',
			'__message' => '',
			'Data' => $this->MySQL->row($sql, $this->User->id, $request->body->month)
		];
	}

	/**
	 * 签到
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function sign_do(\Ziima\MVC\REST\Request $request) {
		$ret = \yuemi_main\ProcedureInvoker::Instance()->task_sign_exec($this->User->id, $this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			return [
				'__code' => 'E_SYSTEM',
				'__message' => '数据库系统错误'
			];
		}
		if ($ret->ReturnValue != 'OK') {
			return [
				'__code' => 'E_LOGICAL',
				'__message' => $ret->ReturnMessage
			];
		}
		$sql = "SELECT `month_id` AS `Month`";
		for ($i = 1; $i <= 31; $i ++) {
			$sql .= ",`day_$i` AS `Day_$i`";
		}
		$sql .= " FROM `yuemi_main`.`task_sign` " .
				" WHERE `user_id` = %d AND `month_id` = %d";
		return [
			'__code' => 'OK',
			'__message' => '',
			'Data' => $this->MySQL->row($sql, $this->User->id, date('Ym'))
		];
	}

}
