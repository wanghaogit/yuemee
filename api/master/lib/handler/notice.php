<?php

include_once 'lib/ApiHandler.php';

/**
 * 公告API接口
 */
class notice_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 系统公告接口
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 * @noauth
	 */
	public function list(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `id`,`scope`,`title`,`create_time` " .
				"FROM `yuemi_main`.`notice` ";
		$whr = [];
		$whr[] = '`open_time` < NOW()';
		$whr[] = '`close_time` > NOW()';
		$whr[] = '`status` = 2';
		$scope = [0, 1];
		if ($this->User !== null) {
			if ($this->User->level_v > 0)
				$scope[] = 2;
			if ($this->User->level_c > 0)
				$scope[] = 3;
			if ($this->User->level_d > 0)
				$scope[] = 4;
			if ($this->User->level_s > 0)
				$scope[] = 5;
			if ($this->User->level_t > 0)
				$scope[] = 6;
		}
		$whr[] = '`scope` IN (' . implode(',', $scope) . ')';
		if ($whr) {
			$sql .= 'WHERE ' . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `id` ASC";

		$list = $this->MySQL->grid($sql);
		$scope = ['All', 'User', 'Vip', 'Chief', 'Director', 'Supplier'];
		$data = [
		];
		foreach ($list as $n) {
			$k = $scope[$n['scope']] ?? '';
			if (empty($k))
				continue;
			if (!isset($data[$k])) {
				$data[$k] = [];
			}
			$data[$k][] = [
				'Id' => $n['id'],
				'Title' => $n['title'],
				'Time' => $n['create_time']
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Data' => $data
		];
	}

	/**
	 * 系统公告内容
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		objid		公告ID
	 * @noauth
	 */
	public function detail(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'Id' => $request->body->id,
			'Content' => $this->MySQL->scalar("SELECT `content` FROM `yuemi_main`.`notice` WHERE `id` = '%s'", $request->body->id)
		];
	}
	
	/**
	 * 私信接口--拉取
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		int		$reciver_id		接收人ID
	 * @request		int		$orther_id		对方已显示最后一条私信ID
	 * @request		int		$this_id		自己已发送最后一条私信ID
	 * @noauth
	 */
	public function get_private(\Ziima\MVC\REST\Request $request) {
		//获取自己发送的私信
		$Sql = "SELECT `id` FROM `yuemi_main`.`mail` WHERE `sender_id` = {$this->User->id} AND `id` > {$request->body->this_id}";
		$SelfArr = [];
		$TemporaryArr1 = $this->MySQL->grid($Sql);
		if (!empty($TemporaryArr1)){
			foreach($TemporaryArr1 AS $val){
				$SelfArr[$val['id']] = 1;
			}
		}
		$Sql2 = "SELECT `id` FROM `yuemi_main`.`mail` WHERE `reciver_id` = {$request->body->reciver_id} AND `id` > {$request->body->orther_id}";
		$OtherArr = [];
		$TemporaryArr = $this->MySQL->grid($Sql2);
		if (!empty($TemporaryArr)){
			foreach($TemporaryArr AS $val){
				$OtherArr[$val['id']] = 0;
			}
		}
		$Re = [];
		if (!empty($TemporaryArr) || !empty($TemporaryArr1)){
			$ReArr = array_merge($OtherArr,$SelfArr);
			ksort($ReArr);
			$i = 0;
			foreach ($ReArr as $id=>$role){
				$sql = "SELECT * FROM `yuemi_main`.`mail` WHERE `id` = {$id}";
				$Re[$i]['mail'] = $this->MySQL->row($sql);
				$Re[$i]['role'] = $role;
				$i++;
			}
		}
		return [
			'__code'	=>'OK',
			'__message'	=>'',
			'ReMessage'	=>$Re
		];
	}
	/**
	 * 私信接口--发送
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		sender_id		int		    发送人id
	 * @request		sender_name		varchar		发送人
	 * @request		content        	text		内容
	 * @noauth
	 */
	public function set_private(\Ziima\MVC\REST\Request $request) {
		
        $sql="SELECT * FROM `yuemi_main`.`mail` ";
		$data=$this->MySQL->grid($sql);
		return [
			'__code'	=>'OK',
			'__message'	=>'',
			'data' =>$data
		];
		
	}
}
