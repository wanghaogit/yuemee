<?php

include "lib/AdminHandler.php";

/**
 * 系统管理
 * @auth
 */
class system_handler extends AdminHandler {

	/**
	 *
	 * @var \yuemi_main\InviteTemplateFactory
	 */
	private $FactoryInviteTemplate;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) 
	{
		$DataCountLog = $this->MySQL->grid("SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS FROM information_schema.`TABLES` WHERE TABLE_SCHEMA = 'yuemi_log' ORDER BY TABLE_ROWS DESC");
		$DataCountMain = $this->MySQL->grid("SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS FROM information_schema.`TABLES` WHERE TABLE_SCHEMA = 'yuemi_main' ORDER BY TABLE_ROWS DESC");
		$DataCountSale = $this->MySQL->grid("SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS FROM information_schema.`TABLES` WHERE TABLE_SCHEMA = 'yuemi_sale' ORDER BY TABLE_ROWS DESC");
		return ['DataCountLog' => $DataCountLog, 'DataCountMain' => $DataCountMain, 'DataCountSale' => $DataCountSale];
	}

	/**
	 * 短信记录
	 */
	public function sms()
	{
		$DataList = \yuemi_main\SmsFactory::Instance()->queryWith("id > 0 ORDER BY id DESC", 0, 100);
		return ['DataList' => $DataList];
	}
	
	/**
	 * MySql运行状态
	 */
	public function mysql()
	{
		$DataStatus = $this->MySQL->grid("SHOW STATUS"); // MySql 运行状态
		$DataInnoDBStatus = $this->MySQL->row("SHOW ENGINE INNODB STATUS"); // InnoDB 状态
		$DataProcessList = $this->MySQL->grid("SHOW FULL PROCESSLIST"); // 正在进行的进程
		$DataVariables = $this->MySQL->grid("SHOW VARIABLES"); // 配置参数
		$DataOpenTables = $this->MySQL->grid("SHOW OPEN TABLES"); // 当前被打开的表列表
		$DataGlobalStatus = $this->MySQL->grid("show global status"); // 运行状态值
		$DataTableLocks = $this->MySQL->grid("show global status like 'table_locks%'"); // 表锁情况
		
		// 打开文件数
		$Data1 = $this->MySQL->row("show global status like 'open_files'");
		$Data2 = $this->MySQL->row("show variables like 'open_files_limit'");
		$DataOpenFiles['open'] = $Data1['Value'];
		$DataOpenFiles['max'] = $Data2['Value'];
		
		$DataInnoDBStatus['Status'] = str_replace("\n", "<br />", $DataInnoDBStatus['Status']);

		return ['DataInnoDBStatus' => $DataInnoDBStatus,
			'DataProcessList' => $DataProcessList,
			'DataVariables' => $DataVariables,
			'DataOpenTables' => $DataOpenTables,
			'DataGlobalStatus' => $DataGlobalStatus,
			'DataOpenFiles' => $DataOpenFiles,
			'DataTableLocks' => $DataTableLocks,
			'XXX' => 111111,
			'XXX' => 111111,
			'DataStatus' => $DataStatus];
	}

	public function teach_logs()
	{
		
	}

	public function config() {
		
	}

	public function region(int $p = 0, int $t = 0) {
		//先写SQL主体，注意缩进
		$sql = "SELECT * " .
				"FROM `yuemi_main`.`region` ";
		//准备用技巧来拼接SQL条件
		$whr = [];
		//-------上面的留着别动-----
		//各种SQL高级玩法
		$Current = null;
		if (strlen($t) != 6) {
			$whr[] = "`id` LIKE '%0000'";
		} elseif (floor($t / 10000) * 10000 == $t) {
			$whr[] = "`id` LIKE '" . substr($t, 0, 2) . "__00' AND `id` NOT LIKE '__0000'";
			$Current = $this->MySQL->row("SELECT * FROM `yuemi_main`.`region` WHERE `id` = $t");
		} else {
			$whr[] = "`id` LIKE '" . substr($t, 0, 4) . "__' AND `id` NOT LIKE '____00'";
			$Current = $this->MySQL->row("SELECT * FROM `yuemi_main`.`region` WHERE `id` = $t");
		}

		//-------下面也不要改----
		if (!empty($whr)) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		//固定追加排序子句
		$sql .= " ORDER BY `id` asc";
		//固定调用 paging
		$result = $this->MySQL->paging($sql, 50, $p);
		//一定是return
		return[
			'Current' => $Current,
			'Result' => $result
		];
	}

	public function bank(int $p = 0, int $u = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`bank` AS `t` ";
		//       $sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 20, $p);

		return[
			'Result' => $result
		];
	}

	public function rbac(int $p = 0) {

		$sql = "SELECT * FROM `yuemi_main`.`rbac_role`";
		$result = $this->MySQL->paging($sql, 10, $p);
		return[
			'Result' => $result
		];
	}
	
	/**
	 * 管理员管理
	 */
	public function admin(int $p = 0, string $m = '', string $v = '') {
		$this->Cacher->loadRbacRole();
		$sql2 = "SELECT `ra`.*,`user`.`name`,`user`.`mobile`,`uc`.`card_name` " .
				"FROM `yuemi_main`.`rbac_admin` AS `ra` " .
				"LEFT JOIN `yuemi_main`.`user` AS `user` ON `user`.`id` = `ra`.`user_id` LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = `ra`.`user_id`";
		$res2 = $this->MySQL->paging($sql2, 30, $p);
		return [
			'res2' => $res2,
			'Role' => $this->Cacher->rbac_role
		];
	}
	
	//管理员修改详情
	public function change_admin_info(int $uid = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$role = intval($_POST['role']) == '' ? 1 : intval($_POST['role']);
			$pass = $this->MySQL->encode($_POST['pass']) == '' ? 123456 : sha1(SECURITY_SALT_USER . '/' . $_POST['pass']);
			$uid = intval($_POST['uid']);
			if ($pass == 123456) {
				$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_admin` SET `role_id` = {$role} WHERE `user_id` = {$uid}");
				throw new \Ziima\MVC\Redirector('/index.php?call=system.admin');
			} else {
				$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_admin` SET `role_id` = {$role},`password` = '{$pass}' WHERE `user_id` = {$uid}");
				$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `password` = '{$pass}' WHERE `id` = {$uid}");
				throw new \Ziima\MVC\Redirector('/index.php?call=system.admin');
			}
		}

		$list = $this->MySQL->row("SELECT `ra`.*,`u`.`name` FROM `yuemi_main`.`rbac_admin` AS `ra` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `ra`.`user_id` " .
				"WHERE `ra`.`user_id` = {$uid}");
		
		return [
			'list' => $list,
			'role' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = 0")
		];
	}
	
	public function add_admin(int $msg = 0) {
		return [
			'role' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = 0")
		];
	}

	//添加管理员
	public function subadmin() {
		$mobile = intval($_POST['mobile']);
//		$password = sha1(SECURITY_SALT_USER . '/' . $_POST['password']);
		$role1 = intval($_POST['role']); //hidden
		$row = $this->MySQL->row("SELECT * FROM `yuemi_main`.`user` WHERE `mobile` = {$mobile}");
		if (empty($row)) {
			//没有用户
			throw new \Ziima\MVC\Redirector('/index.php?call=system.add_admin&msg=1');
		}
		$RbacAdminEntity = new \yuemi_main\RbacAdminEntity();
		$RbacAdminEntity->user_id = $row['id'];
//		$RbacAdminEntity->password = $password;
		$RbacAdminEntity->status = 1;
		$RbacAdminEntity->create_time = time();
		$RbacAdminEntity->role_id = $role1;
		$RbacAdminEntity->create_from = (float) $_SERVER["REMOTE_ADDR"];
		$RbacAdminFactory = new \yuemi_main\RbacAdminFactory(MYSQL_WRITER, MYSQL_READER);
		if ($RbacAdminFactory->insert($RbacAdminEntity)) {
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `level_a` = 1 WHERE `mobile` = {$mobile}");
			//重置缓存
			$this->reset_redis();
			throw new \Ziima\MVC\Redirector('/index.php?call=system.admin&msg=0');
		} else {
			//添加失败
			throw new \Ziima\MVC\Redirector('/index.php?call=system.add_admin&msg=2');
		}
	}

	/**
	 * 规则管理
	 */
	public function rule(int $p = 0){
		$sql = " SELECT `rule`.*,`role`.`name` AS `rolename`,`target`.`name` AS `tarname` FROM `yuemi_main`.`rbac_rule` AS `rule` ".
				"LEFT JOIN `yuemi_main`.`rbac_role` AS `role` ON `role`.`id` = `rule`.`role_id` ".
				"LEFT JOIN `yuemi_main`.`rbac_target` AS `target` ON `target`.`id` = `rule`.`target_id` ";
		$sql = "SELECT `role_id` FROM `yuemi_main`.`rbac_rule`";
		$res = $this->MySQL->grid($sql);
		
		$arr = [];
		foreach ($res as $k => $v) {
			if(in_array($v['role_id'], $arr)){
				
			}else{
				array_push($arr, $v['role_id']);
			}
		}
		$list = [];
		foreach ($arr as $k => $v) {
			$lis['arr'] = $this->MySQL->grid("SELECT `rr`.`target_id`,`rt`.`name` FROM `yuemi_main`.`rbac_rule` AS `rr` LEFT JOIN `yuemi_main`.`rbac_target` AS `rt` ON `rr`.`target_id` = `rt`.`id` WHERE `rr`.`role_id` = {$v}");
			$role_name = $this->MySQL->scalar("SELECT name FROM yuemi_main.rbac_role WHERE id = {$v}");
			$lis['role'] = $role_name;
			$list[$v] = $lis;
		}
		return [
			'res' => $res,
			'list' => $list
		];
	}
	
	/**
	 * 目标管理
	 */
	public function target(int $p = 0){
		$sql = " SELECT `target`.*,`tar2`.`name` AS `pname` FROM `yuemi_main`.`rbac_target` AS `target`".
				" LEFT JOIN `yuemi_main`.`rbac_target` AS `tar2` ".
				"ON `tar2`.`id` = `target`.`parent_id` ";
		$sql .= " ORDER BY `target`.`mvc_handler`,`target`.`id` ";
		
		$res = $this->MySQL->paging($sql,30,$p);
		return [
			'res' => $res
		];
	}
	
	/**
	 * 新增规则
	 */
	public function add_rule(){
		if ($this->Context->Runtime->ticket->postback) {
			$role = (int)$_POST['role'];
			$this->MySQL->execute("DELETE FROM `yuemi_main`.`rbac_rule` WHERE `role_id` = {$role}");
			$arr = [];
			foreach ($_POST as $k => $v) {
				if(substr($k,0,1) == 'a'){
					$arr[] = $v;
				}
			}
			foreach ($arr as $k => $v) {
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`rbac_rule` (role_id,target_id,acl_view,acl_edit,acl_delete) VALUES ({$role},{$v},1,1,1)");
			}
			//重置缓存
			$this->reset_redis();
			throw new \Ziima\MVC\Redirector('/index.php?call=system.rule');
		}
		
		return [
			'role' => $this->MySQL->grid(" SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = 0 "),
			'TargetList' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_target` ORDER BY `mvc_handler`,`id`")
		];
		
	}
	
	//新增角色
	public function release_new(){
		if ($this->Context->Runtime->ticket->postback) {
			$name = $this->MySQL->encode($_POST['name']);
			$pid = (int)$_POST['parent'];
			$this->MySQL->execute("INSERT INTO `yuemi_main`.`rbac_role` (parent_id,name) VALUES ({$pid},'{$name}')");
			//重置缓存
			$this->reset_redis();
			throw new \Ziima\MVC\Redirector('/index.php?call=system.rbac');
		}
		$res = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = 0");
		return [
			'res' => $res
		];
	}
	
	/**
	 * 接口权限
	 */
	public function applet(int $p = 0){
		$sql = " SELECT `a`.*,`u`.`name` AS `uname` FROM `yuemi_main`.`applet` AS `a` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.id  = `a`.`user_id` ";
		$res = $this->MySQL->paging($sql,30,$p);
		return [
			'res' => $res
		];
	}
	
	public function update_ruleList(int $id = 0){
		if ($this->Context->Runtime->ticket->postback) {
			$role = (int)$_POST['hiderole'];
			$arr = [];
			foreach ($_POST as $k => $v) {
				if(substr($k,0,1) == 'a'){
					$arr[] = $v;
				}
			}
			$this->MySQL->execute("DELETE FROM `yuemi_main`.`rbac_rule` WHERE `role_id` = {$role}");
			
			foreach ($arr as $k => $v) {
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`rbac_rule` (role_id,target_id,acl_view,acl_edit,acl_delete) VALUES ({$role},{$v},1,1,1)");
			}
			//重置缓存
			$this->reset_redis();
			throw new \Ziima\MVC\Redirector('/index.php?call=system.rule');
		}
		$ChoseList = $this->MySQL->grid("SELECT `target_id` FROM `yuemi_main`.`rbac_rule` WHERE `role_id` = {$id}");
		$choArr = [];
		foreach ($ChoseList as $k => $v) {
			$choArr[] = $v['target_id'];
		}
		return [
			'TargetList' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_target` ORDER BY `mvc_handler`,`id`"),
			'role_name' => $this->MySQL->row("SELECT `name`,`id` FROM `yuemi_main`.`rbac_role` WHERE `id` = {$id}"),
			'ChoArr' => $choArr
		];
	}

	public function smssearch(int $id = 0){

	}

	
	/**
	 * 重置管理缓存
	 */
	private function reset_redis(){
		$this->Redis->select(0);
		if ($this->Redis->exists('dict_admin')) {
			$this->Redis->delete('dict_admin');
		} 
		if ($this->Redis->exists('dict_role')) {
			$this->Redis->delete('dict_role');
		} 
		if ($this->Redis->exists('dict_target')) {
			$this->Redis->delete('dict_target');
		} 
		if ($this->Redis->exists('dict_rule')) {
			$this->Redis->delete('dict_rule');
		} 
		return;
	}
	
}
