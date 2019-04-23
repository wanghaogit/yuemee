<?php

include "lib/ApiHandler.php";

/**
 * 权限管理接口
 */
class rbac_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 启用管理员
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		管理员ID
	 */
	public function admin_enable(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_admin` SET `status` = 1 WHERE `id` = %d", $request->body->id);
		return 'OK';
	}

	/**
	 * 禁用管理员
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		管理员ID
	 */
	public function admin_disable(\Ziima\MVC\REST\Request $request) {
		if ($request->body->id <= 1) {
			throw new \Ziima\MVC\REST\Exception('E_INTERNAL', '最高级别管理员不可禁用');
		}
		$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_admin` SET `status` = 0 WHERE `id` = %d", $request->body->id);
		return 'OK';
	}

	/**
	 * 获取规则父级
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function target_parent(\Ziima\MVC\REST\Request $request) {
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_target` WHERE `parent_id` = 0");
		return [
			'list' => $list
		];
	}

	/**
	 * 添加规则
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function insert_target(\Ziima\MVC\REST\Request $request) {
//		$RbacTargetEntity = new \yuemi_main\RbacTargetEntity();
//		$RbacTargetEntity->parent_id = $request->body->parent;
//		$RbacTargetEntity->name = $request->body->name;
//		$RbacTargetEntity->mvc_module = $request->body->modle;
//		$RbacTargetEntity->mvc_handler = $request->body->handler;
//		$RbacTargetEntity->mvc_action = $request->body->action;
//		$RbacTargetEntity->mvc_param = $request->body->param;
//		$RbacTargetEntity->mvc_value = $request->body->value;
//		$RbacTargetFactory = new \yuemi_main\RbacTargetFactory(MYSQL_WRITER, MYSQL_READER);
		$parent = $request->body->parent;
		$name = $request->body->name;
		$modle = $request->body->modle;

		$handler = $request->body->handler;
		$action = $request->body->action;
		$param = $request->body->param;
		$value = $request->body->value;
		$this->MySQL->execute("INSERT INTO `yuemi_main`.`rbac_target` (parent_id,name,mvc_module,mvc_handler,mvc_action,mvc_param,mvc_value) " .
				"VALUES ({$parent},'{$name}','{$modle}','{$handler}','{$action}','{$param}','{$value}')");
				
		return [
			'__msg' => 'OK'
		];
	}

	/**
	 * 获取规则子
	 * @param \Ziima\MVC\REST\Request $request
	 * @return type
	 */
	public function get_target(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_target` WHERE `parent_id` = {$id}");
		return [
			'__code' => 'OK',
			'__arr' => $list
		];
	}

	/**
	 * 修改管理规则
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function edit_rule(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$read = $request->body->read;
		$edit = $request->body->edit;
		$delete = $request->body->delete;
		$rr = $this->MySQL->execute("UPDATE `yuemi_main`.`rbac_rule` SET `acl_view` = {$read},`acl_edit` = {$edit},`acl_delete` = {$delete} WHERE `id` = {$id}");
		if ($rr) {
			return [
				'__code' => 'OK',
			];
		} else {
			return [
			'__code' => 'NO'
			];
		}
	}
	
	/**
	 * 删角色
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function delete_rbac(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`rbac_role` WHERE `id` = {$id}");
		return [
			'__code' => 'OK'
		];
	}
	
	/**
	 * 删规则
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function delete_rule(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`rbac_rule` WHERE `role_id` = {$id}");
		return [
			'__code' => 'OK'
		];
	}
	
	/**
	 * 删目标
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function delete_target(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`rbac_target` WHERE `id` = {$id}");
		return [
			'__code' => 'OK'
		];
	}
	
	/**
	 * 获取目标详情
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_target_info(\Ziima\MVC\REST\Request $request){
		$row = $this->MySQL->row("SELECT * FROM `yuemi_main`.`rbac_target` WHERE `id` = {$request->body->id}");
	
		return [
			'__arr' => $row
		];
	}
	
	/**
	 * 修改目标
	 * @param \Ziima\MVC\REST\Request $request
	 * @return type
	 */
	public function update_target(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$parent = $request->body->parent;
		$name = $request->body->name;
		$module = $request->body->module;

		$handler = $request->body->handler;
		$action = $request->body->action;
		$param = $request->body->param;
		$value = $request->body->value;
		
		$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_target` SET `parent_id` = {$parent},`name` = '{$name}',`mvc_module` = '{$module}',`mvc_handler` = '{$handler}',`mvc_action` = '{$action}',".
				"`mvc_param` = '{$param}',`mvc_value` = '{$value}' WHERE `id` = {$id}");
		return [
			'__msg' => 'OK'
		];
	}
	
	/**
	 * 删除接口权限
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function delete_applet(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`applet` WHERE `id` = {$id}");
		return [
			'__code' => 'OK'
		];
	}
	
	/**
	 * 更改状态
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function change_applets(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		$val = $request->body->val;
		$this->MySQL->execute("UPDATE `yuemi_main`.`applet` SET `status` = {$val} WHERE `id` = {$id}");
		return [
			'__code' => 'OK'
		];
	}
	
	/**
	 * 
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_role(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		return [
			'__arr' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = {$id}")
		];
	}

}
