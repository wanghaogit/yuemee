<?php
include_once Z_ROOT . '/Database.php';
include_once Z_ROOT . '/Data/MySQL.php';
include_once Z_SITE . '/../../_base/entity/yuemi_main.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';

class ApiHandler extends \Ziima\MVC\Handler {

	/**
	 * 数据连接
	 * @var \Ziima\Data\MySQLConnection
	 */
	protected $MySQL;

	/**
	 * Redis连接
	 * @var \Redis
	 */
	protected $Redis;

	/**
	 * MongoDB 连接
	 * @var \MongoDB\Driver\Manager
	 */
	protected $Mongo;

	/**
	 * 当前调用的Applet
	 * @var \yuemi_main\AppletEntity
	 */
	protected $Applet;

	/**
	 * 当前用户
	 * @var \yuemi_main\UserEntity
	 */
	protected $User;

	/**
	 * 当前管理员
	 * @var \yuemi_main\RbacAdminEntity
	 */
	protected $Admin;

	/**
	 * 当前角色
	 * @var \yuemi_main\RbacRoleEntity
	 */
	protected $Role;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);

		// MySql连接
		$this->MySQL = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
		$this->Redis = new \Redis();
		$this->Redis->connect(REDIS_HOST, REDIS_PORT);
		if (!empty(REDIS_AUTH)) {
			$this->Redis->auth(REDIS_AUTH);
		}
	}

	public function __init() {

	}

	public function __close() {

	}

	public function __auth() {
		$this->Applet = \yuemi_main\AppletFactory::Instance()->loadByToken($this->Context->Request->__applet_token);

		if ($this->Applet === null) {
			throw new \Ziima\MVC\REST\Exception('E_APPLET', '应用Token无效');
		}
		if ($this->Applet->status != 2) {
			throw new \Ziima\MVC\REST\Exception('E_APPLET_STATUS', '应用状态无效');
		}
		$this->User = \yuemi_main\UserFactory::Instance()->loadOneByToken($this->Context->Request->__access_token);
		if ($this->User === null) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', 'Token已过期');
		}
		if ($this->User->level_u == 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '前台用户被禁止登陆');
		}

		$this->Admin = \yuemi_main\RbacAdminFactory::Instance()->loadByUserId($this->User->id);
		if ($this->Admin === null) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '此用户不是管理员');
		}
		if ($this->Admin->status == 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '管理员权限已被关闭');
		}

		$this->Role = \yuemi_main\RbacRoleFactory::Instance()->load($this->Admin->role_id);
		if ($this->Role === null) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '管理员配置错误');
		}
	}

}
