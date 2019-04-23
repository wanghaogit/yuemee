<?php

include "lib/AdminHandler.php";

/**
 * 私信管理
 * @auth
 */
class notify_handler extends AdminHandler {

	/**
	 * 公告类工厂
	 * @var \yuemi_main\NoticeFactory 
	 */
	private $FactoryNotice;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0, int $status = 9, int $scope = 0) {
		$sql = "SELECT `N`.* " .
				"FROM `yuemi_main`.`notice` AS `N`";
		$wh = [];
		if ($status !== 9) {
			$wh[] = "`status` = {$status}";
		}
		if ($scope > 0) {
			$wh[] = "`scope` = {$scope}";
		}
		if ($wh) {
			$sql .= ' WHERE ' . implode(' AND ', $wh);
		}
		$sql .= " ORDER BY `create_time` DESC";

		return [
			'scope' => $scope,
			'status' => $status,
			'Result' => $this->MySQL->paging($sql, 25, $p)
		];
	}
	
	public function update_status(string $id = '',int $t = 0){
		$this->MySQL->execute("UPDATE `yuemi_main`.`notice` SET `status` = {$t} WHERE `id` = '{$id}'");
		throw new \Ziima\MVC\Redirector('/index.php?call=notify.index');
	}

	/**
	 * 编辑公告
	 * @param string $id
	 * @return type
	 * @throws \Ziima\MVC\Redirector
	 */
	public function notice_edit(string $id = "") {
		if ($this->Context->Runtime->ticket->postback) {
			$title = $this->MySQL->encode($_POST['title']);
			$content = $this->MySQL->encode($_POST['content']);
			$scope_id = (int)$_POST['scope_id'];
			$open_time = $this->MySQL->encode($_POST['open_time']);
			$close_time = $this->MySQL->encode($_POST['close_time']);
			$id = $this->MySQL->encode($_POST['id']);
			
			$this->MySQL->execute("UPDATE `yuemi_main`.`notice` SET `title` = '{$title}'," .
					"`content` = '{$content}',`scope_id` = {$scope_id},`open_time` = " .
					"'{$open_time}',`close_time` = '{$close_time}' WHERE `id` = '{$id}' ");
			
			throw new \Ziima\MVC\Redirector('/index.php?call=notify.index');
		}

		$sql = "SELECT *  FROM `yuemi_main`.`notice` WHERE id='" . $id . "'";
		return[
			'Result' => $this->MySQL->row($sql)
		];
	}

	/**
	 * 保存
	 * @return type
	 * @throws \Exception
	 * @throws \Ziima\MVC\Redirector
	 */
	public function notice_create() {
		$time = date('Y-m-d H:i:s', Z_NOW);
		if ($this->Context->Runtime->ticket->postback) {
			$sj_id = mt_rand(10000000, 99999999);
			$b = MD5($sj_id);
			$NoticeEntity = new \yuemi_main\NoticeEntity();
			$NoticeEntity->id = $b;
			$NoticeEntity->title = $this->MySQL->encode($_POST['title']);
			$NoticeEntity->content = $this->MySQL->encode($_POST['content']);
			$NoticeEntity->open_time = $this->MySQL->encode($_POST['open_time']);
			$NoticeEntity->close_time = $this->MySQL->encode($_POST['close_time']);
			$NoticeEntity->create_time = $time;
			$NoticeEntity->create_from = $this->Context->Runtime->ticket->ip;
			$NoticeEntity->audit_user = 0;
			$NoticeEntity->audit_time = "0000-00-00 00:00:00";
			$NoticeEntity->audit_from = 0;
			$NoticeEntity->create_user = $this->User->id;
			$NoticeEntity->scope = (int)$_POST['scope_id'];
			$NoticeEntity->scope_id = 0;
			$NoticeEntity->status = isset($_POST['status']) ? 1 : 0;
			$NoticeFactory = new \yuemi_main\NoticeFactory(MYSQL_WRITER, MYSQL_READER);
			if (!$NoticeFactory->insert($NoticeEntity)) {
				throw new \Exception('插入表Notice失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=notify.index');
		}
		return[
			'time' => $time
		];
	}

	public function sms(int $p = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`sms`";
		$result = $this->MySQL->paging($sql,30,$p);
		return[
			'Result' => $result
		];
	}

	public function private(int $p = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`mail` AS `t` ";
		//$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 20, $p);
		return[
			'Result' => $result
		];
	}

	public function assist() {
		
	}

	public function train() {
		
	}

	public function im() {
		
	}

	/**
	 * 通知
	 */
	public function message() {
		
	}

}
